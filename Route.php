<?php
/**
 * @package    SugiPHP
 * @subpackage Routing
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

/**
 * Route is a set of rules used for routing.
 * Main rule is a path, but there are more:
 *  - host (domains, subdomains)
 *  - scheme (http or https)
 *  - method (GET, POST, etc.)
 *  - ...
 */
class Route implements RouteInterface
{
	protected $path = "/";
	protected $host = null; // null means all
	protected $method = null; // null means all - GET, HEADER, POST, PUT, DELETE, ...
	protected $scheme = null; // null means all - http, https
	protected $defaults = array();
	protected $requisites = array();
	protected $variables = array();

	protected $defaultPathRequisites = "[^/.,;?<>]+";

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
	 * @param  string $path
	 * @return SugiPHP\Routing\Route
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
	 * @param  array $defaults
	 * @return SugiPHP\Routing\Route
	 */
	public function setDefaults(array $defaults)
	{
		$this->defaults = $defaults;

		return $this;
	}

	/**
	 * Sets a default value for a parameter $key.
	 * 
	 * @param  string $key
	 * @param  string $value
	 * @return SugiPHP\Routing\Route
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
	 * @param  string $key
	 * @return string|null - null means that no default parameter was set
	 */
	public function getDefault($key)
	{
		return isset($this->defaults[$key]) ? $this->defaults[$key] : null;
	}

	/**
	 * Checks a default value is set.
	 * 
	 * @param  string $key
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
	 * @param  array $requisites
	 * @return SugiPHP\Routing\Route
	 */
	public function setRequisites(array $requisites)
	{
		$this->requisites = $requisites;

		return $this;
	}

	/**
	 * Sets a requisite - regular expression for a $key path or host variable.
	 * 
	 * @param  string $key
	 * @param  string $value
	 * @return SugiPHP\Routing\Route
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
	 * @param  string $key
	 * @return stting|null - null means no RegEx is set
	 */
	public function getRequisite($key)
	{
		return isset($this->requisites[$key]) ? $this->requisites[$key] : null;
	}

	/**
	 * Checks a requisite is set for the host or path parameter $key.
	 * 
	 * @param  string  $key
	 * @return boolean
	 */
	public function hasRequisite($key)
	{
		return key_exists($key, $this->requisites);
	}

	/**
	 * Sets expected host (pattern)
	 * 
	 * @param  string $host
	 * @return SugiPHP\Routing\Route
	 */
	public function setHost($host)
	{
		// will NOT fix wrong hosts!
		// $host = trim($host);
		// if ((strpos($host, "http://") !== 0) and (strpos($host, "https://") !== 0)) {
		// 	$host = "http://" . $host;
		// }
		// $host = parse_url($host,  PHP_URL_HOST);
		$this->host = $host ?: null;

		return $this;
	}

	/**
	 * Returns expected host.
	 * 
	 * @return string|null - returns null for ANY host
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * Set request methods for which the Route should work.
	 * 
	 * @param  string|null $method - null matches any method
	 * @return SugiPHP\Routing\Route
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
	 * Expected HTTP scheme: "http" or "https"
	 * 
	 * @param  string|null $scheme - null means all
	 * @return SugiPHP\Routing\Route
	 */
	public function setScheme($scheme)
	{
		if (!in_array(strtolower($scheme), array("", "http", "https"))) {
			$scheme = null;
		}
		$this->scheme = $scheme ?: null;

		return $this;
	}

	/**
	 * Returns expected scheme: "http" or "https"
	 * 
	 * @return string|null - returns null for ANY scheme
	 */
	public function getScheme()
	{
		return $this->scheme;
	}

	/**
	 * Match defined route rules against the request.
	 *
	 * @param  string $path - "/"
	 * @param  string $method - "GET", "POST", etc.
	 * @param  string $host - "example.com"
	 * @param  string $scheme - "http" or "https"
	 * @return array|false - true if the request match defined route, false if there is no match
	 */
	public function match($path, $method, $host, $scheme)
	{
		// setting default values as a variables
		$this->variables = $this->defaults;

		if ($this->matchPath($path) === false) {
			return false;
		}

		if ($this->matchMethod($method) === false) {
			return false;
		}

		if ($this->matchHost($host) === false) {
			return false;
		}

		if ($this->matchScheme($scheme) === false) {
			return false;
		}

		return $this->variables;
	}

	/**
	 * Checks the given shceme is within accepted route schemes.
	 * 
	 * @param  string $scheme
	 * @return boolean
	 */
	public function matchScheme($scheme)
	{
		// does not to be so complicated
		// if ($this->scheme == "http") {
		// 	$regex = "http";
		// } elseif ($this->scheme == "https") {
		// 	$regex = "https";
		// }
		// return (bool) preg_match("#^".$regex."(://)?$#i", $scheme);

		if (!$this->scheme) {
			return true;
		}

		return ($this->scheme == $scheme);
	}

	/**
	 * Checks the given method is within those registered in the Route.
	 * 
	 * @param  string $method - "GET", "POST", "HEAD", etc.
	 * @return boolean
	 */
	public function matchMethod($method)
	{
		if (!$this->method) {
			return true;
		}

		return (bool) preg_match("#" . str_replace("#", "\\#", $this->method)."#i", $method);
	}

	/**
	 * Checks the given host matches route's host.
	 * 
	 * @param  string $host - like "sub.example.com"
	 * @return boolean
	 */
	public function matchHost($host)
	{
		if (!$this->host) {
			return true;
		}

		if (preg_match($this->compile($this->host, $this->defaults, $this->requisites, "host"), $host, $matches)) {
			// add matches in array to know variables in host name
			foreach ($matches as $var => $value) {
				if (!is_int($var) and $value) {
					$this->variables[$var] = $value;
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * Checks that given path matches root's path.
	 * 
	 * @param  string $path
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

		$regEx = $this->compile($routePath, $this->defaults, $requisites, "path");

		if (preg_match($regEx, $path, $matches)) {
			// add matches in array to know variables in path name
			foreach ($matches as $var => $value) {
				if (!is_int($var) and $value) {
					$this->variables[$var] = $value;
				}
			}
			return true;
		}

		return false;
	}

	/**
	 * Builds an URI based on the pattern, default values and given parameters.
	 * If some parameter is not set the default value will be used.
	 * If some parameters are equal to their default values they can be skipped,
	 * thus making a more friendly URL.
	 * 
	 * @param  array  $parameters
	 * @return string
	 */
	public function build(array $parameters = array())
	{
		$pattern = $this->path;
		$requisites = $this->requisites;
		$defaults = $this->defaults;
		$defaultRequisites = $this->defaultPathRequisites;

		preg_match_all("#\{(\w+)\}#", $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		$cnt = count($matches);
		$last = true;
		while ($cnt--) {
			$match = $matches[$cnt];
			$variable = $match[1][0];
			$varPattern = $match[0][0]; // {variable}
			$varPos = $match[0][1];
			$nextChar = (isset($pattern[$varPos + strlen($varPattern)])) ? $pattern[$varPos + strlen($varPattern)] : "";
			$prevChar = ($varPos > 0) ? $pattern[$varPos - 1] : "";
			$param = empty($parameters[$variable]) ? null : $parameters[$variable];
			$default = array_key_exists($variable, $defaults) ? $defaults[$variable] : null;
			$requisite = array_key_exists($variable, $requisites) ? $requisites[$variable] : $defaultRequisites;

			if ($param and !preg_match("#^".$requisite."$#", $param)) {
				return false;
			}

			if (!is_null($default) and !is_null($param)) {
				// if the given param value is equal to the default value for that parameter we'll leave it empty
				if ($param == $default) {
					$replace = ($last) ? $default : "";
				} elseif ($param) {
					$replace = $param;
				} else {
					$replace = $default;
				}
			} elseif (!is_null($param)) {
				if (!$param) {
					return false;
				} else {
					$replace = $param;
				}
			} elseif (!is_null($default)) {
				$replace = "";
			} else {
				return false;
			}

			if ($replace) {
				$last = false;
			}

			$pattern = str_replace($varPattern, $replace, $pattern);
			if (!$replace) {
				if ($variable == "_format") {
					$pattern = rtrim($pattern, ".");
				} else {
					$pattern = rtrim($pattern, "/");
				}
			}
		}

		return "/".trim($pattern, "/");
	}

	/**
	 * Returns variable $var.
	 * 
	 * @param  string $var
	 * @return string
	 */
	public function get($var)
	{
		return isset($this->variables[$var]) ? $this->variables[$var] : null;
	}

	/**
	 * Create regular expression for the host or for the path
	 * 
	 * @param  string $pattern
	 * @param  array  $defaults
	 * @param  arrat  $requisites
	 * @param  string $style - "host" or "path"
	 * @return string
	 */
	protected function compile($pattern, $defaults, $requisites, $style)
	{
		$regex = $pattern;
		// $regex = preg_replace('#[.\\+?[^\\]$()<>=!]#', '\\\\$0', $regex);

		if ($style === "host") {
			$delimiter = ".";
			$defaultRequisites = "[^.,;?<>]+";
		} elseif ($style === "path") {
			$delimiter = "/";
			$defaultRequisites = $this->defaultPathRequisites;
		} else {
			throw new \Exception("Unknown style $style");
		}

		preg_match_all("#\{(\w+)\}#", $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
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
				if ($style == "host" and $nextChar == $delimiter and ($prevChar == "" or $prevChar == $delimiter)) {
					$regex = preg_replace("#".$varPattern.$delimiter."#", "((?P<".$variable.">".$capture.")".$delimiter.")?", $regex);
				} elseif ($style == "path" and (($prevChar == $delimiter and $nextChar == $delimiter) or ($prevChar == $delimiter and $nextChar == "" and $varPos > 1))) {
					$regex = preg_replace("#".$delimiter.$varPattern."#", "(".$delimiter."(?P<".$variable.">".$capture."))?", $regex);
				} else {
					$regex = preg_replace("#".$varPattern."#", "((?P<".$variable.">".$capture."))?", $regex);
				}
			} else {
				$regex = preg_replace("#".$varPattern."#", "(?P<".$variable.">".$capture.")", $regex);
			}

			$this->variables[$variable] = $this->getDefault($variable);
		}
		
		if ($style == "host") {
			$regex = str_replace(".", "\.", $regex);
		} else {
			$regex = "/?".$regex;
		}

		return "#^".$regex.'$#siuD';
	}
}
