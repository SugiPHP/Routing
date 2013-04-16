<?php
/**
 * @package    SugiPHP
 * @subpackage Routing
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class RouterTest extends \PHPUnit_Framework_TestCase
{
	public function testMethods()
	{
		$router = new Router();
		$this->assertFalse($router->has("home"));
		$this->assertNull($router->get("home"));
		$this->assertSame(0, $router->count());
		// add one
		$this->assertInstanceOf("SugiPHP\Routing\Router", $router->add("home", new Route("/")));
		$this->assertSame(1, $router->count());
		$this->assertTrue($router->has("home"));
		$this->assertInstanceOf("SugiPHP\Routing\Route", $router->get("home"));
		// change it
		$this->assertInstanceOf("SugiPHP\Routing\Router", $router->set("home", new Route("/foo")));
		$this->assertSame(1, $router->count());
		$this->assertTrue($router->has("home"));
		$this->assertInstanceOf("SugiPHP\Routing\Route", $router->get("home"));
		// remove it
		$this->assertInstanceOf("SugiPHP\Routing\Router", $router->delete("home"));
		$this->assertFalse($router->has("home"));
		$this->assertNull($router->get("home"));
		$this->assertSame(0, $router->count());
	}

	public function testMatch()
	{
		$router = new Router();
		$router->add("home", new Route("/"));
		$router->add("article", new Route("/show/{title}"));
		$router->add("mvc", new Route("/{controller}/{action}/{param}", array("action" => "index", "param" => "")));
		// var_dump($router->match("/", "GET", "example.com", "http"));
	}
}
