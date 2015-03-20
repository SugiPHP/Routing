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
     * Generates path only, full path or network path, based on the given parameters.
     * If in the $parameters _host and _scheme keys are given then Full URL will be returned.
     * If only _host key is given then network address will be returned
     */
    const PATH_AUTO = "auto";

    /**
     * Generates path only.
     * Example: /foo/bar
     */
    const PATH_ONLY = "path";

    /**
     * Generates an absolute URLs.
     * Example: http://example.com/foo/bar
     */
    const PATH_FULL = "full";

    /**
     * Generates an absolute path without the scheme.
     * Example: //example.com/dir/file
     */
    const PATH_NETWORK = "network";

    /**
     * Match defined route rules against the request.
     *
     * @param string $path - "/"
     * @param string $method - "GET", "POST", etc.
     * @param string $host - "example.com"
     * @param string $scheme - "http" or "https"
     *
     * @return array|false Array of parameters if the request matches defined route or FALSE if there is no match
     */
    public function match($path, $method, $host, $scheme);

    /**
     * Builds an URI based on the pattern, default values and given parameters.
     * If some parameter is not set the default value will be used.
     * If some parameters are equal to their default values they can be skipped,
     * thus making a more friendly URL.
     *
     * @param array  $parameters
     * @param string Which parts of the path should be used: PATH_ONLY, PATH_FULL, PATH_NETWORK
     *
     * @return string|false False will be returned if the URI cannot be build,
     *                      typically when parameter which has no default value is not given
     */
    public function build(array $parameters = array(), $pathType = self::PATH_AUTO);
}
