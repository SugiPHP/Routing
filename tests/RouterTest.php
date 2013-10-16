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

	public function testBuild()
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

	public function testBuildFullPath()
	{
		$router = new Router();
		$router->add("test", new Route("/", array()));
		$this->assertSame("/", $router->build("test", array(), Route::PATH_ONLY));
		$this->assertSame("/", $router->build("test", array(), Route::PATH_NETWORK));
		$this->assertSame("/", $router->build("test", array(), Route::PATH_FULL));

		// with _host
		$this->assertSame("/", $router->build("test", array("_host" => "example.com"), Route::PATH_ONLY));
		$this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com"), Route::PATH_NETWORK));
		$this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com"), Route::PATH_FULL));

		// with _scheme, no _host
		$this->assertSame("/", $router->build("test", array("_scheme" => "https"), Route::PATH_ONLY));
		$this->assertSame("/", $router->build("test", array("_scheme" => "https"), Route::PATH_NETWORK));
		$this->assertSame("/", $router->build("test", array("_scheme" => "https"), Route::PATH_FULL));

		// with _scheme and _host
		$this->assertSame("/", $router->build("test", array("_host" => "example.com", "_scheme" => "https"), Route::PATH_ONLY));
		$this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com", "_scheme" => "https"), Route::PATH_NETWORK));
		$this->assertSame("https://example.com/", $router->build("test", array("_host" => "example.com", "_scheme" => "https"), Route::PATH_FULL));

		// with setHost()
		$router->get("test")->setHost("example.net");
		$this->assertSame("/", $router->build("test", array(), Route::PATH_ONLY));
		$this->assertSame("//example.net/", $router->build("test", array(), Route::PATH_NETWORK));
		$this->assertSame("//example.net/", $router->build("test", array(), Route::PATH_FULL));

		// with setHost and _host
		$this->assertSame("/", $router->build("test", array("_host" => "example.com"), Route::PATH_ONLY));
		$this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com"), Route::PATH_NETWORK));
		$this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com"), Route::PATH_FULL));

		// with setHost() and _scheme
		$this->assertSame("/", $router->build("test", array("_scheme" => "https"), Route::PATH_ONLY));
		$this->assertSame("//example.net/", $router->build("test", array("_scheme" => "https"), Route::PATH_NETWORK));
		$this->assertSame("https://example.net/", $router->build("test", array("_scheme" => "https"), Route::PATH_FULL));

		// with setHost() and setScheme()
		$router->get("test")->setScheme("http");
		$this->assertSame("/", $router->build("test", array(), Route::PATH_ONLY));
		$this->assertSame("//example.net/", $router->build("test", array(), Route::PATH_NETWORK));
		$this->assertSame("http://example.net/", $router->build("test", array(), Route::PATH_FULL));

		// with setHost(), setScheme() and _scheme
		$this->assertSame("/", $router->build("test", array("_scheme" => "https"), Route::PATH_ONLY));
		$this->assertSame("//example.net/", $router->build("test", array("_scheme" => "https"), Route::PATH_NETWORK));
		$this->assertSame("https://example.net/", $router->build("test", array("_scheme" => "https"), Route::PATH_FULL));
	}
}
