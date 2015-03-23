<?php
/**
 * Route class.
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

/**
 * Route is a set of rules used for routing.
 * Main rule is a path and a method (GET, POST, etc.)
 */
class Route implements RouteInterface
{
    protected $path;
    protected $method; // if not set means all - GET, HEADER, POST, PUT, DELETE, ...
    protected $defaults = array();
    protected $requisites = array();
    protected $variables = array();

    /**
     * Constructor
     *
     * @param string $path - the path pattern, usually with variables like "/{controller}/{action}/{id}"
     * @param array  $defaults - default values for variables in the path or for the host
     *                         array("id" => "", "action" => "index")
     * @param array  $requisites - regular expression to match variables like array("id" => "\d+")
     */
    public function __construct($path, array $defaults = array(), array $requisites = array())
    {
        $this->setPath($path);
        $this->setDefaults($defaults);
        $this->setRequisites($requisites);
    }

    /**
     * Set expected path (pattern).
     *
     * @param string $path
     *
     * @return Route
     */
    public function setPath($path)
    {
        // Will not fix paths like /index.php?q=123 !
        // $path = parse_url($path,  PHP_URL_PATH);
        // This fix is OK!
        $path = "/" . trim($path, "/");
        $this->path = $path;

        return $this;
    }

    /**
     * Returns expected path (pattern).
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets default values for variables in host and
     * in the path (pattern) and thus making them optional.
     *
     * @param array $defaults
     *
     * @return Route
     */
    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    /**
     * Sets a default value for a parameter $key.
     *
     * @param string $key
     * @param string $value
     *
     * @return Route
     */
    public function setDefault($key, $value)
    {
        $this->defaults[$key] = $value;

        return $this;
    }

    /**
     * Returns all default values.
     *
     * @return array
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Returns a default value for a given parameter $key.
     *
     * @param string $key
     *
     * @return string|null - null means that no default parameter was set
     */
    public function getDefault($key)
    {
        return isset($this->defaults[$key]) ? $this->defaults[$key] : null;
    }

    /**
     * Checks a default value is set.
     *
     * @param string $key
     *
     * @return boolean
     */
    public function hasDefault($key)
    {
        return key_exists($key, $this->defaults);
    }

    /**
     * Sets requisites (regular expressions) for variables in host and path
     * <code>
     *  array("lang" => "en|bg");
     * </code>
     *
     * @param array $requisites
     *
     * @return Route
     */
    public function setRequisites(array $requisites)
    {
        $this->requisites = $requisites;

        return $this;
    }

    /**
     * Sets a requisite - regular expression for a $key path or host variable.
     *
     * @param string $key
     * @param string $value
     *
     * @return Route
     */
    public function setRequisite($key, $value)
    {
        $this->requisites[$key] = $value;

        return $this;
    }

    /**
     * Returns all registered requisites for the parameters in the path and for the host.
     *
     * @return array
     */
    public function getRequisites()
    {
        return $this->requisites;
    }

    /**
     * Returns a requisite (RegEx) for a path or host variable $key.
     *
     * @param string $key
     *
     * @return stting|null - null means no RegEx is set
     */
    public function getRequisite($key)
    {
        return isset($this->requisites[$key]) ? $this->requisites[$key] : null;
    }

    /**
     * Checks a requisite is set for the host or path parameter $key.
     *
     * @param string  $key
     *
     * @return boolean
     */
    public function hasRequisite($key)
    {
        return key_exists($key, $this->requisites);
    }

    /**
     * Set request methods for which the Route should work.
     *
     * @param string|null $method - null matches any method
     *
     * @return Route
     */
    public function setMethod($method)
    {
        $this->method = $method ? strtoupper($method) : null;

        return $this;
    }

    /**
     * Get expected request method
     *
     * @return string|null - null means ALL
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Implements RouteInterface::match() method
     * {@inheritdoc}
     */
    public function match($path, $method)
    {
        // setting default values as a variables
        $this->variables = $this->defaults;

        if ($this->matchMethod($method) === false) {
            return false;
        }

        if ($this->matchPath($path) === false) {
            return false;
        }

        return $this->variables;
    }

    /**
     * Checks the given method is within those registered in the Route.
     *
     * @param string $method - "GET", "POST", "HEAD", etc.
     *
     * @return boolean
     */
    public function matchMethod($method)
    {
        if (empty($this->method)) {
            return true;
        }

        return (bool) preg_match("#" . str_replace("#", "\\#", $this->method)."#i", $method);
    }

    /**
     * Checks that given path matches root's path.
     *
     * @param string $path
     *
     * @return boolean
     */
    public function matchPath($path)
    {
        $path = "/" . trim($path, "/");

        // copy requisites, so we can change them temporary
        $requisites = array_merge($this->requisites);

        // special {_format} parameter
        if (strpos($this->path, ".{_format}") !== false) {
            if ($requisite = $this->getRequisite("_format")) {
                $requisites["_format"] = "\.(" . $requisite . ")";
            } else {
                $requisites["_format"] = "\.\w+";
            }
            $routePath = str_replace(".{_format}", "{_format}", $this->path);
        } else {
            $routePath = $this->path;
        }

        $regEx = PathCompiler::compile($routePath, $this->defaults, $requisites);

        if (preg_match($regEx, $path, $matches)) {
            // add matches in array to know variables in path name
            foreach ($matches as $var => $value) {
                if (!is_int($var) && $value) {
                    $this->variables[$var] = $value;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Implements RouteInterface::build() method
     * {@inheritdoc}
     */
    public function build(array $parameters = array())
    {
        $path = PathCompiler::build($this->path, $parameters, $this->defaults, $this->requisites);

        return $path;
    }

    /**
     * Returns variable $var.
     *
     * @param string $var
     *
     * @return string
     */
    public function get($var)
    {
        return isset($this->variables[$var]) ? $this->variables[$var] : null;
    }
}
