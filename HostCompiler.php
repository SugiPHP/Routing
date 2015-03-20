<?php
/**
 * Host Compiler
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

/**
 * Create a regular expression for host
 */
class HostCompiler implements CompilerInterface
{
    /**
     * Creates regular expression
     *
     * @param string $pattern
     * @param array  $defaults
     * @param array  $requisites
     *
     * @return string
     */
    public static function compile($pattern, $defaults, $requisites)
    {
        $defaultRequisites = "[^.,;?<>]+";
        $delimiter = ".";
        $regex = $pattern;

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
                    if (($nextChar == $delimiter) && (($prevChar == "") || ($prevChar == $delimiter))) {
                        $regex = preg_replace("#".$varPattern.$delimiter."#", "((?P<".$variable.">".$capture.")".$delimiter.")?", $regex);
                    } else {
                        $regex = preg_replace("#".$varPattern."#", "((?P<".$variable.">".$capture."))?", $regex);
                    }
                } else {
                    $regex = preg_replace("#".$varPattern."#", "(?P<".$variable.">".$capture.")", $regex);
                }

                // $this->variables[$variable] = $this->getDefault($variable);
            }
        }
        $regex = str_replace(".", "\.", $regex);

        return "#^".$regex.'$#siuD';
    }

    public static function build($pattern, $parameters, $defaults, $requisites)
    {
        return $pattern;
    }
}
