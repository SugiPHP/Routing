<?php
/**
 * Route Interface.
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

interface RouteInterface
{
    /**
     * Match defined route rules against the request.
     *
     * @param string $path - "/"
     * @param string $method - "GET", "POST", etc.
     *
     * @return array|false Array of parameters if the request matches defined route or FALSE if there is no match
     */
    public function match($path, $method);

    /**
     * Builds an URI based on the pattern, default values and given parameters.
     * If some parameter is not set the default value will be used.
     * If some parameters are equal to their default values they can be skipped,
     * thus making a more friendly URL.
     *
     * @param array  $parameters
     *
     * @return string|false False will be returned if the URI cannot be build,
     *                      typically when parameter which has no default value is not given
     */
    public function build(array $parameters = array());
}
