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
class Route
{
	protected $path = "/";
	protected $host = null; // null means all
	protected $method = null; // null means all - GET, HEADER, POST, PUT, DELETE, ...
	protected $scheme = null; // null means all - http, https
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
	 * Set expected path
	 * 
	 * @param string $path
	 * @return SugiPHP\Routing\Route
	 */
	public function setPath($path)
	{
		$path = parse_url($path,  PHP_URL_PATH);
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
	 * Sets default values for variables in host or path (pattern)
	 * and thus making them optional
	 * 
	 * @param array $defaults
	 * @return SugiPHP\Routing\Route
	 */
	public function setDefaults(array $defaults)
	{
		$this->defaults = $defaults;

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
		$host = trim($host);
		if ((strpos($host, "http://") !== 0) and (strpos($host, "https://") !== 0)) {
			$host = "http://" . $host;
		}
		$host = parse_url($host,  PHP_URL_HOST);
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
	 * @return boolean - true if the request match defined route
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

		return true;
	}

	/**
	 * Matches the given scheme to registered scheme.
	 * 
	 * @param  string $scheme
	 * @return boolean
	 */
	public function matchScheme($scheme)
	{
		if ($this->scheme == "http") {
			$regex = "http";
		} elseif ($this->scheme == "https") {
			$regex = "https";
		} else {
			$regex = "http(s)?";
		}

		return (bool) preg_match("#^".$regex."(://)?$#i", $scheme);
	}

	public function matchMethod($method)
	{
		return (!$this->method or preg_match("#" . str_replace("#", "\\#", $this->method)."#i", $method));
	}

	public function matchHost($host)
	{
		if (!$this->host) {
			return true;
		}

		if (preg_match($this->compile($this->host, "host"), $host, $matches)) {
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

	public function matchPath($path)
	{
		$regEx = $this->compile($this->path, "path");
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
	 * Create regular expression for the host or for the path
	 * 
	 * @param  string $pattern
	 * @param  string $style - "host" or "path"
	 * @return string
	 */
	protected function compile($pattern, $style)
	{
		$regex = $pattern;
		// $regex = preg_replace('#[.\\+?[^\\]$()<>=!]#', '\\\\$0', $regex);

		if ($style === "host") {
			$delimiter = ".";
			$defaultRequisites = "[^.,;?<>]+";
		} elseif ($style === "path") {
			$delimiter = "/";
			$defaultRequisites = "[^/,;?<>]+";
		} else {
			throw new \Exception("Unknown style $style");
		}

		preg_match_all("#\{(\w+)\}#", $pattern, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);
		foreach ($matches as $match) {
			$variable = $match[1][0];
			$varPattern = $match[0][0]; // {variable}
			$varPos = $match[0][1];
			$capture = array_key_exists($variable, $this->requisites) ? $this->requisites[$variable] : $defaultRequisites;
			$nextChar = (isset($pattern[$varPos + strlen($varPattern)])) ? $pattern[$varPos + strlen($varPattern)] : "";
			$prevChar = ($varPos > 0) ? $pattern[$varPos - 1] : "";

			if ($this->hasDefault($variable)) {
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
