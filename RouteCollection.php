<?php
/**
 * @package    SugiPHP
 * @subpackage Routing
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class RouteCollection implements \Countable, \IteratorAggregate
{
	/**
	 * A list of Route instances.
	 *
	 * @var array
	 */
	protected $routes = array();

	/**
	 * Adds a route to the end of the list.
	 *
	 * @param  string $name The route's name
	 * @param  RouteInterface $route
	 * @return Router
	 */
	public function add($name, RouteInterface $route)
	{
		// clear it
		unset($this->routes[$name]);
		// add it to the bottom of the list
		$this->routes[$name] = $route;

		return $this;
	}

	/**
	 * Sets a route. If the route with this name already exists in the list it will be set on top of it,
	 * otherwise it will be added to the end of the list.
	 *
	 * @param  string $name The route's name
	 * @param  RouterInterface $route
	 * @return Router
	 */
	public function set($name, RouteInterface $route)
	{
		$this->routes[$name] = $route;

		return $this;
	}

	/**
	 * Returns a route instance by it's name.
	 *
	 * @param  string $name
	 * @return RouteInterface
	 */
	public function get($name)
	{
		return isset($this->routes[$name]) ? $this->routes[$name] : null;
	}

	/**
	 * Checks the route with this name exists in the list.
	 *
	 * @param  string $name
	 * @return boolean
	 */
	public function has($name)
	{
		return isset($this->routes[$name]);
	}

	/**
	 * Removes route with the name given.
	 *
	 * @param  string $name
	 * @return Router
	 */
	public function delete($name)
	{
		unset($this->routes[$name]);

		return $this;
	}

	/**
	 * Removes all registered routes from the list.
	 *
	 * @return Router
	 */
	public function flush()
	{
		$this->routes = array();

		return $this;
	}

	/**
	 * Returns all registered routes
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->routes;
	}

	/**
	 * Returns the number of routes in the list.
	 *
	 * @implements \Countable
	 * @return integer
	 */
	public function count()
	{
		return count($this->routes);
	}

	/**
	 * @implements \IteratorAggregate
	 * @return array
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->routes);
	}
}
