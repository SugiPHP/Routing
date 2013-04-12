<?php
/**
 * @package    SugiPHP
 * @subpackage Routing
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

use SugiPHP\Routing\Route;

class RouteMatchTest extends PHPUnit_Framework_TestCase
{
	public function testHomePath()
	{
		$route = new Route("/");
		$url = parse_url("http://example.com/");
		$this->assertTrue($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("http://example.com/?foo=bar");
		$this->assertTrue($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("http://example.com/");
		$this->assertTrue($route->match($url["path"], "POST", $url["host"], $url["scheme"]));
		$url = parse_url("http://www.example.com/");
		$this->assertTrue($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("https://example.com/");
		$this->assertTrue($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		
		$url = parse_url("http://example.com/test");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
	}

	public function testOnePath()
	{
		$route = new Route("/users");
		$url = parse_url("http://example.com/users");
		$this->assertTrue($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		
		$url = parse_url("http://example.com/test");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
		$url = parse_url("http://example.com/");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
	}

	public function testMorePath()
	{
		$route = new Route("/users/login");
		$url = parse_url("http://example.com/users/login?foo=bar");
		$this->assertTrue($route->match($url["path"], "GET", $url["host"], $url["scheme"]));

		$url = parse_url("http://example.com/users/login.php");
		$this->assertFalse($route->match($url["path"], "GET", $url["host"], $url["scheme"]));
	}
}
