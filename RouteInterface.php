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
	public function build(array $parameters = array());
}
