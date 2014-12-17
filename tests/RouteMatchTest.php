<?php
/**
 * PHP Unit tests for matching routes with some URIs
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class RouteMatchTest extends \PHPUnit_Framework_TestCase
{
	public function testHomePath()
	{
		$route = new Route("/");
		$url = parse_url("http://example.com/");
		$this->assertInternalType("array", $route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("http://example.com/?foo=bar");
		$this->assertInternalType("array", $route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("http://example.com/");
		$this->assertInternalType("array", $route->match($url["path"], "POST", $url["host"], $url["scheme"]));
		$url = parse_url("http://www.example.com/");
		$this->assertInternalType("array", $route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("https://example.com/");
		$this->assertInternalType("array", $route->match($url["path"], "GET", $url["host"], $url["scheme"]));

		$url = parse_url("http://example.com/test");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
	}

	public function testOnePath()
	{
		$route = new Route("/users");
		$url = parse_url("http://example.com/users");
		$this->assertInternalType("array", $route->match($url["path"], "GET", $url["host"], $url["scheme"]));

		$url = parse_url("http://example.com/test");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("http://example.com/");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
	}

	public function testMorePath()
	{
		$route = new Route("/users/login");
		$url = parse_url("http://example.com/users/login?foo=bar");
		$this->assertInternalType("array", $route->match($url["path"], "GET", $url["host"], $url["scheme"]));

		$url = parse_url("http://example.com/users/login.php");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
	}
}
