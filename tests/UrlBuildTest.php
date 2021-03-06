<?php
/**
 * Building URIs tests for Route Class
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class UrlBuildTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildHome()
    {
        $route = new Route("/");
        $this->assertEquals("/", $route->build());
        $this->assertEquals("/", $route->build(array()));
        $this->assertEquals("/", $route->build(array("page" => 2)));

        $route = new Route("/home");
        $this->assertEquals("/home", $route->build());

        $route = new Route("/index.php");
        $this->assertEquals("/index.php", $route->build());
    }

    public function testBuildWithOneParam()
    {
        $route = new Route("/{slug}");
        $this->assertEquals("/test", $route->build(array("slug" => "test")));
        $this->assertEquals("/12", $route->build(array("slug" => "12")));
        $this->assertEquals("/index.php", $route->build(array("slug" => "index.php")));
        // will not be build
        $this->assertFalse($route->build(array("slug" => "")));

        $route = new Route("/show/{slug}");
        $this->assertEquals("/show/test", $route->build(array("slug" => "test")));
        $this->assertEquals("/show/12", $route->build(array("slug" => "12")));
        $this->assertEquals("/show/index.php", $route->build(array("slug" => "index.php")));
        // will not be build
        $this->assertFalse($route->build(array("slug" => "")));

        $route = new Route("/{slug}/view");
        $this->assertEquals("/test/view", $route->build(array("slug" => "test")));
        $this->assertEquals("/12/view", $route->build(array("slug" => "12")));
        $this->assertEquals("/index.php/view", $route->build(array("slug" => "index.php")));
        // will not be build
        $this->assertFalse($route->build(array("slug" => "")));
    }

    public function testBuildWithOneParamAndDefaultEmptyValue()
    {
        $route = new Route("/{slug}", array("slug" => ""));
        $this->assertEquals("/test", $route->build(array("slug" => "test")));
        $this->assertEquals("/12", $route->build(array("slug" => "12")));
        $this->assertEquals("/", $route->build(array()));
        $this->assertEquals("/", $route->build(array("slug" => "")));
        $this->assertEquals("/index.php", $route->build(array("slug" => "index.php")));

        $route = new Route("/show/{slug}", array("slug" => ""));
        $this->assertEquals("/show/foo", $route->build(array("slug" => "foo")));
        $this->assertEquals("/show/12", $route->build(array("slug" => 12)));
        $this->assertEquals("/show", $route->build(array("slug" => "")));
        $this->assertEquals("/show", $route->build());

        $route = new Route("/{lang}/view", array("lang" => ""));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en")));
        $this->assertEquals("/view", $route->build(array("lang" => "")));
        $this->assertEquals("/view", $route->build());
    }

    public function testBuildWithOneParamAndDefaultNotEmptyValue()
    {
        $route = new Route("/{slug}", array("slug" => "foo"));
        $this->assertEquals("/test", $route->build(array("slug" => "test")));
        $this->assertEquals("/12", $route->build(array("slug" => "12")));
        $this->assertEquals("/", $route->build());
        $this->assertEquals("/", $route->build(array("slug" => "")));

        $route = new Route("/show/{slug}", array("slug" => "foo"));
        $this->assertEquals("/show", $route->build(array("slug" => "foo")));
        $this->assertEquals("/show/12", $route->build(array("slug" => 12)));
        $this->assertEquals("/show", $route->build(array("slug" => "")));

        $route = new Route("/{lang}/view", array("lang" => "en"));
        $this->assertEquals("/bg/view", $route->build(array("lang" => "bg")));
        $this->assertEquals("/view", $route->build(array("lang" => "")));
        $this->assertEquals("/view", $route->build(array()));

        $route = new Route("/show/{site}/list", array("site" => "main"));
        $this->assertEquals("/show/list", $route->build(array("site" => "main")));

        $route = new Route("{site}/{category}/list/{page}", array("controller" => "category", "page" => 1, "site" => "main"));
        $this->assertEquals("/show/list", $route->build(array("category" => "show")));
        $this->assertEquals("/second/show/list", $route->build(array("site"=>"second", "category" => "show")));
        $this->assertEquals("/second/show/list/3", $route->build(array("site"=>"second", "category" => "show", "page" => 3)));
        $this->assertEquals("/show/list/3", $route->build(array("site"=>"main", "category" => "show", "page" => 3)));
    }

    public function testBuildWithOneParamAndDefaultEmptyAndRequisite()
    {
        $route = new Route("/{lang}", array("lang" => ""), array("lang" => "bg|en"));
        $this->assertEquals("/bg", $route->build(array("lang" => "bg")));
        $this->assertEquals("/", $route->build(array()));
        $this->assertEquals("/", $route->build(array("lang" => "")));
        // will not be build
        $this->assertFalse($route->build(array("lang" => "fr")));

        $route = new Route("/show/{id}", array("id" => ""), array("id" => "\d+"));
        $this->assertEquals("/show/12", $route->build(array("id" => 12)));
        $this->assertEquals("/show", $route->build(array("id" => "")));
        $this->assertEquals("/show", $route->build());
        // will not be build
        $this->assertFalse($route->build(array("id" => "foo")));

        $route = new Route("/{lang}/view", array("lang" => ""), array("lang" => "bg|en"));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en")));
        $this->assertEquals("/view", $route->build(array("lang" => "")));
        $this->assertEquals("/view", $route->build());
        // will not be build
        $this->assertFalse($route->build(array("lang" => "fr")));
    }

    public function testMatchPathWithSpecialFormat()
    {
        $route = new Route("/foo/file.{_format}", array("_format" => ""));
        $this->assertEquals("/foo/file.php", $route->build(array("_format" => "php")));
        $this->assertEquals("/foo/file", $route->build(array("_format" => "")));
        $this->assertEquals("/foo/file", $route->build());
    }

    public function testWith2Params()
    {
        $route = new Route("/{lang}/view/{slug}");

        $this->assertEquals("/en/view/test", $route->build(array("lang" => "en", "slug" => "test")));
        $this->assertFalse($route->build(array("slug" => "test")));
        $this->assertFalse($route->build(array("lang" => "en")));
        $this->assertFalse($route->build());

        $route = new Route("/{lang}/view/{slug}", array("lang" => ""));
        $this->assertEquals("/en/view/test", $route->build(array("lang" => "en", "slug" => "test")));
        $this->assertEquals("/view/test", $route->build(array("slug" => "test")));
        $this->assertFalse($route->build(array("lang" => "en")));
        $this->assertFalse($route->build());

        $route = new Route("/{lang}/view/{slug}", array("lang" => "en"));
        $this->assertEquals("/view/test", $route->build(array("lang" => "en", "slug" => "test")));
        $this->assertEquals("/bg/view/test", $route->build(array("lang" => "bg", "slug" => "test")));
        $this->assertEquals("/view/test", $route->build(array("slug" => "test")));
        $this->assertFalse($route->build(array("lang" => "en")));
        $this->assertFalse($route->build());

        $route = new Route("/{lang}/view/{slug}", array("slug" => ""));
        $this->assertEquals("/en/view/test", $route->build(array("lang" => "en", "slug" => "test")));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en")));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en", "slug" => "")));
        $this->assertFalse($route->build(array("slug" => "test")));
        $this->assertFalse($route->build());

        $route = new Route("/{lang}/view/{slug}", array("slug" => "foo"));
        $this->assertEquals("/en/view/test", $route->build(array("lang" => "en", "slug" => "test")));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en")));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en", "slug" => "foo")));
        $this->assertEquals("/en/view", $route->build(array("lang" => "en", "slug" => "")));
        $this->assertFalse($route->build(array("slug" => "test")));
        $this->assertFalse($route->build(array("slug" => "foo")));
        $this->assertFalse($route->build());

        $route = new Route("/{lang}/view/{slug}", array("slug" => "foo", "lang" => "en"));
        $this->assertEquals("/view/test", $route->build(array("lang" => "en", "slug" => "test")));
        $this->assertEquals("/bg/view/test", $route->build(array("lang" => "bg", "slug" => "test")));
        $this->assertEquals("/view", $route->build(array("lang" => "en")));
        $this->assertEquals("/view", $route->build(array("lang" => "en", "slug" => "foo")));
        $this->assertEquals("/view", $route->build(array("lang" => "en", "slug" => "")));
        $this->assertEquals("/view", $route->build(array("lang" => "en")));
        $this->assertEquals("/view", $route->build(array("lang" => "en", "slug" => "foo")));
        $this->assertEquals("/view", $route->build(array("lang" => "en", "slug" => "")));
    }
}
