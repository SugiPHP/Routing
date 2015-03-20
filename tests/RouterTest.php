<?php
/**
 * PHP Unit tests for Router class
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
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

        $this->assertSame("/", $router->build("home", array()));
        $this->assertSame("/", $router->build("home", array("something" => "else")));

        $this->assertFalse($router->build("article", array()));
        $this->assertFalse($router->build("article", array("something" => "else")));
        $this->assertFalse($router->build("article", array("title" => "")));
        $this->assertFalse($router->build("article", array("title" => false)));
        $this->assertSame("/show/1", $router->build("article", array("title" => "1")));
        $this->assertSame("/show/thisisatitle", $router->build("article", array("title" => "thisisatitle")));

        $this->assertSame("/", $router->build("mvc", array()));
        $this->assertSame("/", $router->build("mvc", array("foo" => "bar")));
        $this->assertSame("/", $router->build("mvc", array("controller" => "")));
        $this->assertSame("/", $router->build("mvc", array("controller" => false)));
        $this->assertSame("/", $router->build("mvc", array("controller" => "home")));
    }

    public function testBuildWithPathType()
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
    }
}
