<?php
/**
 * Host class.
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

/**
 * Route Extension for
 *  - host (domains, subdomains)
 *  - scheme (http or https)
 */
class Host extends Route
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

    protected $host; // if not set means all
    protected $scheme; // if not set means all - http, https

    /**
     * Sets expected host (pattern)
     *
     * @param string $host
     *
     * @return Route
     */
    public function setHost($host)
    {
        // will NOT fix wrong hosts!
        // $host = trim($host);
        // if ((strpos($host, "http://") !== 0) and (strpos($host, "https://") !== 0)) {
        //  $host = "http://" . $host;
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
     * Expected HTTP scheme: "http" or "https"
     *
     * @param string|null $scheme - null means all
     *
     * @return Route
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;

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
     * Implements RouteInterface::match() method
     * {@inheritdoc}
     */
    public function match($path, $method, $host = "", $scheme = "")
    {
        $pathVars = parent::match($path, $method);

        if ($pathVars === false) {
            return false;
        }

        // setting default values as a variables
        $this->variables = $pathVars;

        if ($this->matchScheme($scheme) === false) {
            return false;
        }

        if ($this->matchHost($host) === false) {
            return false;
        }

        return $this->variables;
    }

    /**
     * Checks the given host matches route's host.
     *
     * @param string $host - like "sub.example.com"
     *
     * @return boolean
     */
    public function matchHost($host)
    {
        if (empty($this->host)) {
            return true;
        }

        if (preg_match(HostCompiler::compile($this->getHost(), $this->defaults, $this->requisites), $host, $matches)) {
            // add matches in array to know variables in host name
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
     * Checks the given scheme is within accepted route schemes.
     *
     * @param string $scheme
     *
     * @return boolean
     */
    public function matchScheme($scheme)
    {
        // does not to be so complicated
        // if ($this->scheme == "http") {
        //  $regex = "http";
        // } elseif ($this->scheme == "https") {
        //  $regex = "https";
        // }
        // return (bool) preg_match("#^".$regex."(://)?$#i", $scheme);

        // if it's empty it will not be == to scheme (and if it will)
        if (empty($this->scheme)) {
            return true;
        }

        return (strtolower($this->scheme) == strtolower($scheme));
    }

    /**
     * Implements RouteInterface::build() method
     * {@inheritdoc}
     *
     * @param string Which parts of the path should be used: PATH_ONLY, PATH_FULL, PATH_NETWORK
     */
    public function build(array $parameters = array(), $pathType = self::PATH_AUTO)
    {

        $path = parent::build($parameters, $pathType);

        if ($pathType == self::PATH_AUTO) {
            if (!empty($parameters["_host"])) {
                if (!empty($parameters["_scheme"])) {
                    $pathType = self::PATH_FULL;
                } else {
                    $pathType = self::PATH_NETWORK;
                }
            } else {
                $pathType = self::PATH_ONLY;
            }
        }

        if (($pathType == self::PATH_NETWORK) || ($pathType == self::PATH_FULL)) {
            if (isset($parameters["_host"])) {
                $path = "//" . $parameters["_host"] . $path;
                if ($pathType == self::PATH_FULL) {
                    $scheme =  (isset($parameters["_scheme"])) ? $scheme = $parameters["_scheme"] : $scheme = $this->getScheme();
                    if ($scheme) {
                        $path = $scheme . ":" . $path;
                    }
                }
            }
        }

        return $path;
    }
}
