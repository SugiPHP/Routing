<?php
/**
 * Router Class
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class Router implements \Countable, \IteratorAggregate
{
    /**
     * A list of Route instances.
     *
     * @var array
     */
    protected $routes = array();

    private $path;
    private $method;
    private $host;
    private $scheme;
    private $checkedRoutes = array();

    /**
     * Adds a route to the end of the list.
     *
     * @param string $name The route's name
     * @param RouteInterface $route
     *
     * @return Router
     */
    public function add($name, RouteInterface $route)
    {
        // clear it
        unset($this->routes[$name]);
        // Name the route
        $route->setDefault("_name", $name);
        // and Router
        $route->setDefault("_router", $this);
        // add it to the bottom of the list
        $this->routes[$name] = $route;

        return $this;
    }

    /**
     * Sets a route. If the route with this name already exists in the list it will be set on top of it,
     * otherwise it will be added to the end of the list.
     *
     * @param string $name The route's name
     * @param RouteInterface $route
     *
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
     * @param string $name
     *
     * @return RouteInterface
     */
    public function get($name)
    {
        return isset($this->routes[$name]) ? $this->routes[$name] : null;
    }

    /**
     * Checks the route with this name exists in the list.
     *
     * @param string $name
     *
     * @return boolean
     */
    public function has($name)
    {
        return isset($this->routes[$name]);
    }

    /**
     * Removes route with the name given.
     *
     * @param string $name
     *
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
     * implements \Countable
     *
     * @return int
     */
    public function count()
    {
        return count($this->routes);
    }

    /**
     * Implements \IteratorAggregate
     *
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->routes);
    }

    /**
     * Walks through all registered routes and returns first route that matches
     * the given parameters.
     *
     * @param string $path
     * @param string $method "GET", "POST", "PUT" etc. HTTP methods
     * @param string $host
     * @param string $scheme "http" or "https" scheme
     *
     * @return RouteInterface or NULL if no route matches given parameters
     */
    public function getFirstMatch($path, $method = "", $host = "", $scheme = "")
    {
        $this->checkedRoutes = array();
        $this->path = $path;
        $this->method = $method;
        $this->host = $host;
        $this->scheme = $scheme;

        foreach ($this->routes as $name => $route) {
            $this->checkedRoutes[] = $name;
            $match = $route->match($path, $method, $host, $scheme);
            if ($match !== false) {
                return $route;
            }
        }
    }

    /**
     * Continue matching registered routes.
     *
     * @see getFirstMatch()
     *
     * @return RouteInterface or NULL if no route matches given parameters
     */
    public function getNextMatch()
    {
        foreach ($this->routes as $name => $route) {
            if (!in_array($name, $this->checkedRoutes)) {
                $this->checkedRoutes[] = $name;
                $match = $route->match($this->path, $this->method, $this->host, $this->scheme);
                if ($match !== false) {
                    return $route;
                }
            }
        }
    }

    /**
     * Builds an URI based on parameters given.
     *
     * @param string $name Route name
     * @param array  $params
     *
     * @return string|null Will return URI or NULL if the route is not found
     */
    public function build($name, $params = array(), $pathType = Host::PATH_AUTO)
    {
        if (!$route = $this->get($name)) {
            return null;
        }

        return $route->build($params, $pathType);
    }
}
