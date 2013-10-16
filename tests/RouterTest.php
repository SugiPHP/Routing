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
		$router->add("mvc", new Route("/{controller}/{action}/{param}", array("controller" => "home", "action" => "index", "param" => "")));

		$this->assertSame("/", $router->build("home", []));
		$this->assertSame("/", $router->build("home", ["something" => "else"]));

		$this->assertFalse($router->build("article", []));
		$this->assertFalse($router->build("article", ["something" => "else"]));
		$this->assertFalse($router->build("article", ["title" => ""]));
		$this->assertFalse($router->build("article", ["title" => false]));
		$this->assertSame("/show/1", $router->build("article", ["title" => "1"]));
		$this->assertSame("/show/thisisatitle", $router->build("article", ["title" => "thisisatitle"]));

		$this->assertSame("/", $router->build("mvc", []));
		$this->assertSame("/", $router->build("mvc", ["foo" => "bar"]));
		$this->assertSame("/", $router->build("mvc", ["controller" => ""]));
		$this->assertSame("/", $router->build("mvc", ["controller" => false]));
		$this->assertSame("/", $router->build("mvc", ["controller" => "home"]));
	}
}
