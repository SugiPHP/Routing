<?php
/**
 * @package    SugiPHP
 * @subpackage Routing
 * @category   tests
 * @author     Plamen Popov <tzappa@gmail.com>
 * @license    http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class RouteCollectionTest extends \PHPUnit_Framework_TestCase
{
	public function testMethods()
	{
		$router = new RouteCollection();
		$this->assertFalse($router->has("home"));
		$this->assertNull($router->get("home"));
		$this->assertSame(0, $router->count());
		// add one
		$this->assertInstanceOf("SugiPHP\Routing\RouteCollection", $router->add("home", new Route("/")));
		$this->assertSame(1, $router->count());
		$this->assertTrue($router->has("home"));
		$this->assertInstanceOf("SugiPHP\Routing\Route", $router->get("home"));
		// change it
		$this->assertInstanceOf("SugiPHP\Routing\RouteCollection", $router->set("home", new Route("/foo")));
		$this->assertSame(1, $router->count());
		$this->assertTrue($router->has("home"));
		$this->assertInstanceOf("SugiPHP\Routing\Route", $router->get("home"));
		// remove it
		$this->assertInstanceOf("SugiPHP\Routing\RouteCollection", $router->delete("home"));
		$this->assertFalse($router->has("home"));
		$this->assertNull($router->get("home"));
		$this->assertSame(0, $router->count());
	}
}
