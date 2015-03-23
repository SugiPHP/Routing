<?php
/**
 * PHP Unit tests for Router class
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class RouterWithHostTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildWithPathType()
    {
        $router = new Router();
        $router->add("test", new Host("/", array()));
        $this->assertSame("/", $router->build("test", array(), Host::PATH_ONLY));
        $this->assertSame("/", $router->build("test", array(), Host::PATH_NETWORK));
        $this->assertSame("/", $router->build("test", array(), Host::PATH_FULL));

        // with _host
        $this->assertSame("/", $router->build("test", array("_host" => "example.com"), Host::PATH_ONLY));
        $this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com"), Host::PATH_NETWORK));
        $this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com"), Host::PATH_FULL));

        // with _scheme, no _host
        $this->assertSame("/", $router->build("test", array("_scheme" => "https"), Host::PATH_ONLY));
        $this->assertSame("/", $router->build("test", array("_scheme" => "https"), Host::PATH_NETWORK));
        $this->assertSame("/", $router->build("test", array("_scheme" => "https"), Host::PATH_FULL));

        // with _scheme and _host
        $this->assertSame("/", $router->build("test", array("_host" => "example.com", "_scheme" => "https"), Host::PATH_ONLY));
        $this->assertSame("//example.com/", $router->build("test", array("_host" => "example.com", "_scheme" => "https"), Host::PATH_NETWORK));
        $this->assertSame("https://example.com/", $router->build("test", array("_host" => "example.com", "_scheme" => "https"), Host::PATH_FULL));
    }
}
