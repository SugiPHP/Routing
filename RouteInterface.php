<?php
/**
 * @package    SugiPHP
 * @subpackage Routing
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

interface RouteInterface
{
	/**
	 * Generates path only, full path or network path, based on the given parameters.
	 * If in the $parameters _host and _scheme keys are given then Full URL will be returned.
	 * If only _host key is given then network address will be returned
	 * If host and scheme can be also set with setHost() and setScheme() methods.
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
	 * @param  string $path - "/"
	 * @param  string $method - "GET", "POST", etc.
	 * @param  string $host - "example.com"
	 * @param  string $scheme - "http" or "https"
	 * @return array|false - true if the request match defined route, false if there is no match
	 */
	public function match($path, $method, $host, $scheme);


	/**
	 * Builds an URI based on the pattern, default values and given parameters.
	 *
	 * @param  array $parameters
	 * @return string
	 */
	public function build(array $parameters = array(), $pathType = self::PATH_AUTO);
}
