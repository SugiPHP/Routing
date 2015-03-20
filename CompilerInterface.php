<?php
/**
 * Compiler Interface
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

/**
 * Create a regular expressions.
 */
interface CompilerInterface
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
    public static function compile($pattern, $defaults, $requisites);

    /**
     * Builds an URI based on the pattern, default values and given parameters.
     * If some parameter is not set the default value will be used.
     * If some parameters are equal to their default values they can be skipped,
     * thus making a more friendly URL.
     *
     * @param string $pattern
     * @param array  $parameters
     * @param array  $defaults
     * @param array  $requisites
     *
     * @return string|false False will be returned if the URI cannot be build,
     *                      typically when parameter which has no default value is not given
     */
    public static function build($pattern, $parameters, $defaults, $requisites);
}
