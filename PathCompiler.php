<?php
/**
 * Path Compiler
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

/**
 * Create a regular expression for Path
 */
class PathCompiler implements CompilerInterface
{
    /**
     * Implements CompilerInterface::compile() method
     * {@inheritdoc}
     */
    public static function compile($pattern, $defaults, $requisites)
    {
        $regex = $pattern;
        $delimiter = "/";
        $defaultRequisites = "[^/,;?<>]+";

        preg_match_all("#\{(\w+)\}#", $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        if (is_array($matches)) {
            foreach ($matches as $match) {
                $variable = $match[1][0];
                $varPattern = $match[0][0]; // {variable}
                $varPos = $match[0][1];
                $capture = array_key_exists($variable, $requisites) ? $requisites[$variable] : $defaultRequisites;
                $nextChar = (isset($pattern[$varPos + strlen($varPattern)])) ? $pattern[$varPos + strlen($varPattern)] : "";
                $prevChar = ($varPos > 0) ? $pattern[$varPos - 1] : "";

                if (array_key_exists($variable, $defaults)) {
                    // Make variables that have default values optional
                    // Also make delimiter (if next char is a delimiter) to be also optional
                    if (($prevChar == $delimiter and $nextChar == $delimiter) or ($prevChar == $delimiter and $nextChar == "" and $varPos > 1)) {
                        $regex = preg_replace("#".$delimiter.$varPattern."#", "(".$delimiter."(?P<".$variable.">".$capture."))?", $regex);
                    } else {
                        $regex = preg_replace("#".$varPattern."#", "((?P<".$variable.">".$capture."))?", $regex);
                    }
                } else {
                    $regex = preg_replace("#".$varPattern."#", "(?P<".$variable.">".$capture.")", $regex);
                }
            }
        }
        $regex = "/?".$regex;

        return "#^".$regex.'$#siuD';
    }

    /**
     * Implements CompilerInterface::compile() method
     * {@inheritdoc}
     */
    public static function build($pattern, $parameters, $defaults, $requisites)
    {
        $defaultRequisites = "[^/,;?<>]+";

        preg_match_all("#\{(\w+)\}#", $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
        $cnt = count($matches);
        while ($cnt--) {
            $variable = $matches[$cnt][1][0];
            $param = empty($parameters[$variable]) ? null : $parameters[$variable];
            $default = array_key_exists($variable, $defaults) ? $defaults[$variable] : null;
            $requisite = array_key_exists($variable, $requisites) ? $requisites[$variable] : $defaultRequisites;

            if ($param && !preg_match("#^".$requisite."$#", $param)) {
                return false;
            }

            if (!is_null($default) && !is_null($param)) {
                // if the given param value is equal to the default value for that parameter we'll leave it empty
                if ($param == $default) {
                    $replace = "";
                } elseif ($param) {
                    $replace = $param;
                } else {
                    $replace = $default;
                }
            } elseif (!is_null($param)) {
                if (!$param) {
                    return false;
                }
                $replace = $param;
            } elseif (!is_null($default)) {
                $replace = "";
            } else {
                return false;
            }

            $pattern = str_replace($matches[$cnt][0][0], $replace, $pattern);
            if (!$replace) {
                if ($variable == "_format") {
                    $pattern = rtrim($pattern, ".");
                } else {
                    $pattern = rtrim($pattern, "/");
                }
            }
        }

        $pattern = "/".trim(str_replace("//", "/", $pattern), "/");

        return $pattern;
    }
}
