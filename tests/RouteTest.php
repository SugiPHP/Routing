<?php
/**
 * PHP Unit tests for Route class
 *
 * @package SugiPHP.Routing
 * @author  Plamen Popov <tzappa@gmail.com>
 * @license http://opensource.org/licenses/mit-license.php (MIT License)
 */

namespace SugiPHP\Routing;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateWithPath()
    {
        $route = new Route("/");
        $this->assertEquals("/", $route->getPath());

        // leading slash
        $route = new Route("");
        $this->assertEquals("/", $route->getPath());

        $route = new Route(null);
        $this->assertEquals("/", $route->getPath());

        $route = new Route(false);
        $this->assertEquals("/", $route->getPath());

        $route = new Route("/help");
        $this->assertEquals("/help", $route->getPath());

        // no trailing slash
        $route = new Route("/help/");
        $this->assertEquals("/help", $route->getPath());

        // always with leading slash
        $route = new Route("help");
        $this->assertEquals("/help", $route->getPath());
    }

    public function testSetMethodsReturnSelf()
    {
        $route = new Route("/");
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setPath("/"));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setHost("example.com"));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setDefaults(array()));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setDefault("controller", "main"));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setRequisites(array()));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setRequisite("id", "\d+"));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setMethod("get"));
        $this->assertInstanceOf("\SugiPHP\Routing\Route", $route->setScheme("http"));
    }

    public function testPath()
    {
        // absolute valid
        $route = new Route("/home");
        $this->assertEquals("/home", $route->getPath());

        // absolute valid
        $route = new Route("/home.php");
        $this->assertEquals("/home.php", $route->getPath());

        // tries to fix wrong input
        // This is not the right thing to do,
        // so I'm removing it from the code!
/*      $route = new Route("/home?page=2");
        $this->assertEquals("/home", $route->getPath());
        $route = new Route("/?page=2");
        $this->assertEquals("/", $route->getPath());
        $route = new Route("/home?page=2#tralala");
        $this->assertEquals("/home", $route->getPath());
        $route = new Route("/home#tralala");
        $this->assertEquals("/home", $route->getPath());

        // no fix for this!!!
        $route = new Route("www.example.com/home");
        $this->assertEquals("/www.example.com/home", $route->getPath());

        // this is better
        $route = new Route("http://www.example.com/home");
        $this->assertEquals("/home", $route->getPath());
        // but will NOT set host
        $this->assertEquals("", $route->getHost());
*/
    }

    public function testSegmentsInPath()
    {
        $route = new Route("admin/{controller}/{method}/{id}");
        $this->assertEquals("/admin/{controller}/{method}/{id}", $route->getPath());
    }

    public function testSetPath()
    {
        $route = new Route("/");
        $route->setPath("/home");
        $this->assertEquals("/home", $route->getPath());
    }

    public function testCreateWithDefaults()
    {
        $route = new Route("/");
        $this->assertEquals(array(), $route->getDefaults());

        $route = new Route("/", array());
        $this->assertEquals(array(), $route->getDefaults());

        $route = new Route("/", array("key" => "value"));
        $this->assertEquals(array("key" => "value"), $route->getDefaults());

        $route = new Route("/", array("key" => "value", "test" => "test"));
        $this->assertEquals(array("key" => "value", "test" => "test"), $route->getDefaults());
    }

    public function testSetDefaults()
    {
        $route = new Route("/");
        $route->setDefaults(array("controller" => "main"));
        $this->assertEquals(array("controller" => "main"), $route->getDefaults());
        $route->setDefaults(array("controller" => "main", "action" => "index"));
        $this->assertEquals(array("controller" => "main", "action" => "index"), $route->getDefaults());
        // add a default
        $route->setDefault("param", "one");
        $this->assertEquals(array("controller" => "main", "action" => "index", "param" => "one"), $route->getDefaults());
    }

    public function testGetDefault()
    {
        $route = new Route("/");
        $route->setDefaults(array("controller" => "main", "action" => "index"));
        $this->assertEquals("main", $route->getDefault("controller"));
        $this->assertEquals("index", $route->getDefault("action"));
        $this->assertEquals("", $route->getDefault("test"));
        $this->assertEquals(null, $route->getDefault("test"));
        $this->assertEquals(false, $route->getDefault("test"));
        // if there is no such default parameters returns NULL
        $this->assertSame(null, $route->getDefault("test"));
    }

    public function testCreateWithRequisites()
    {
        $route = new Route("/", array());
        $this->assertEquals(array(), $route->getRequisites());

        $route = new Route("/", array(), array());
        $this->assertEquals(array(), $route->getRequisites());

        $route = new Route("/", array(), array("key" => "value"));
        $this->assertEquals(array("key" => "value"), $route->getRequisites());

        $route = new Route("/", array(), array("key" => "value", "test" => "test"));
        $this->assertEquals(array("key" => "value", "test" => "test"), $route->getRequisites());
    }

    public function testSetRequisites()
    {
        $route = new Route("/");
        $route->setRequisites(array("lang" => "en|fr"));
        $this->assertEquals(array("lang" => "en|fr"), $route->getRequisites());
        $route->setRequisites(array("lang" => "en|fr", "id" => "\d+"));
        $this->assertEquals(array("id" => "\d+", "lang" => "en|fr"), $route->getRequisites());
        // add another requisite
        $route->setRequisite("username", "[A-z_0-1]+");
        $this->assertEquals(array("id" => "\d+", "lang" => "en|fr", "username" => "[A-z_0-1]+"), $route->getRequisites());

    }

    public function testGetRequisite()
    {
        $route = new Route("/");
        $route->setRequisites(array("lang" => "en|fr", "id" => "\d+"));
        $this->assertEquals("en|fr", $route->getRequisite("lang"));
        $this->assertEquals("\d+", $route->getRequisite("id"));
        $this->assertEquals("", $route->getRequisite("test"));
        $this->assertEquals(null, $route->getRequisite("test"));
        $this->assertEquals(false, $route->getRequisite("test"));
        // if there is no such requisite returns NULL
        $this->assertSame(null, $route->getRequisite("test"));
    }

    public function testMatchPathWithNoPath()
    {
        $route = new Route("/");
        $this->assertTrue($route->matchPath(""));
        $route = new Route("/test");
        $this->assertFalse($route->matchPath(""));

        $route = new Route("");
        $this->assertTrue($route->matchPath("/"));
        $route = new Route("/");
        $this->assertTrue($route->matchPath("/"));
        $route = new Route("/test");
        $this->assertFalse($route->matchPath("/"));
    }

    public function testMatchSinglePath()
    {
        // ok
        $route = new Route("/path");
        $this->assertTrue($route->matchPath("/path"));
        // ok, route adds leading slash
        $this->assertTrue($route->matchPath("path"));
        // ok, route removes trailing slash
        $this->assertTrue($route->matchPath("/path/"));
        // false
        $this->assertFalse($route->matchPath("/path/more"));
        $this->assertFalse($route->matchPath("/otherpathpath"));
        $this->assertFalse($route->matchPath("/"));
    }

    public function testMatchLongPath()
    {
        $route = new Route("/path/to/file.html");
        $this->assertTrue($route->matchPath("/path/to/file.html"));
        // false
        $this->assertFalse($route->matchPath("/"));
        $this->assertFalse($route->matchPath("/path/to/file"));
        $this->assertFalse($route->matchPath("/path/to/"));
        $this->assertFalse($route->matchPath("/otherpath/to/file.html"));
        $this->assertFalse($route->matchPath("/path/to/file.php"));
    }

    public function testMatchPathVariables()
    {
        $route = new Route("/path/to/{file}");
        // ok
        $this->assertTrue($route->matchPath("/path/to/file"));
        $this->assertTrue($route->matchPath("/path/to/file/"));
        $this->assertTrue($route->matchPath("/path/to/fi-le"));
        $this->assertTrue($route->matchPath("/path/to/fi_le"));
        $this->assertTrue($route->matchPath("/path/to/fi_le1234"));

        // false
        $this->assertFalse($route->matchPath("/path/to"));
        $this->assertFalse($route->matchPath("/path/to/"));
        $this->assertFalse($route->matchPath("/path//file"));
        $this->assertFalse($route->matchPath("/wrong/to/file"));
        $this->assertFalse($route->matchPath("/wrong/path/to/file"));
        $this->assertFalse($route->matchPath("/wrong/path/to/file/"));
        $this->assertFalse($route->matchPath("/path/to/file.html/foo"));
        $this->assertFalse($route->matchPath("/path/to/file/foo"));
        // not a path
        $this->assertFalse($route->matchPath("/path/to/something.php?get=param"));
        $this->assertFalse($route->matchPath("http://example.com/path/to/file"));
        $this->assertFalse($route->matchPath("http://example.com/path/to/file.html"));
        $this->assertFalse($route->matchPath("http://example.com/path/to/something.php?get=param"));
    }

    public function testMatchPathVariablesWithDefault()
    {
        $route = new Route("/path/to/{file}", array("file" => "index"));
        // ok
        $this->assertTrue($route->matchPath("/path/to/"));
        $this->assertTrue($route->matchPath("/path/to"));
        $this->assertTrue($route->matchPath("/path/to/file"));
        $this->assertTrue($route->matchPath("/path/to/index"));
        $this->assertTrue($route->matchPath("/path/to/index.php"));

        $route = new Route("/path/to/{file}", array("file" => ""));
        // ok
        $this->assertTrue($route->matchPath("/path/to/"));
        $this->assertTrue($route->matchPath("/path/to"));
        $this->assertTrue($route->matchPath("/path/to/file"));
        $this->assertTrue($route->matchPath("/path/to/index"));
        $this->assertTrue($route->matchPath("/path/to/index.php"));
    }

    public function testMatchPathVariableAndDot()
    {
        $route = new Route("/path/to/file{ext}", array("ext" => ""));

        $this->assertTrue($route->matchPath("/path/to/filephp"));
        $this->assertTrue($route->matchPath("/path/to/file"));

        // add a requirement
        $route->setRequisite("ext", "\.\w{2,3}");

        $this->assertTrue($route->matchPath("/path/to/file.php"));
        $this->assertTrue($route->matchPath("/path/to/file"));
        // fails
        $this->assertFalse($route->matchPath("/path/to/filephp"));
        $this->assertFalse($route->matchPath("/path/to/file."));
    }

    public function testMatchPathWithSpecialFormat()
    {
        $route = new Route("/path/to/file.{_format}", array("_format" => ""));

        $this->assertTrue($route->matchPath("/path/to/file.php"));
        $this->assertTrue($route->matchPath("/path/to/file"));
        // fails
        $this->assertFalse($route->matchPath("/path/to/filephp"));
        $this->assertFalse($route->matchPath("/path/to/file."));

        // only some formats allowed
        $route->setRequisite("_format", "php|html");
        $this->assertTrue($route->matchPath("/path/to/file.php"));
        $this->assertTrue($route->matchPath("/path/to/file.html"));
        $this->assertTrue($route->matchPath("/path/to/file"));
        // fails
        $this->assertFalse($route->matchPath("/path/to/filephp"));
        $this->assertFalse($route->matchPath("/path/to/file."));
        $this->assertFalse($route->matchPath("/path/to/file.js"));
        $this->assertFalse($route->matchPath("/path/to/file.aspx"));
    }

    public function testMatchPathVariablesWithRequirements()
    {
        $route = new Route("/{lang}/index.php", array(), array("lang" => "en|bg"));
        // ok
        $this->assertTrue($route->matchPath("/en/index.php"));
        $this->assertTrue($route->matchPath("/bg/index.php"));
        // fails
        $this->assertFalse($route->matchPath("/ru/index.php"));
        $this->assertFalse($route->matchPath("/index.php"));

        // same as above with default
        $route = new Route("/{lang}/index.php", array("lang" => "en"), array("lang" => "en|bg"));
        // ok
        $this->assertTrue($route->matchPath("/en/index.php"));
        $this->assertTrue($route->matchPath("/bg/index.php"));
        $this->assertTrue($route->matchPath("/index.php"));
        // fails
        $this->assertFalse($route->matchPath("/ru/index.php"));

        // more simple
        $route = new Route("/{lang}", array(), array("lang" => "en|bg"));
        // ok
        $this->assertTrue($route->matchPath("/en"));
        $this->assertTrue($route->matchPath("/bg/"));
        // fails
        $this->assertFalse($route->matchPath("/ru"));
        $this->assertFalse($route->matchPath("/ru/index"));
        $this->assertFalse($route->matchPath("/"));
        $this->assertFalse($route->matchPath(""));

        // with default
        $route = new Route("/{lang}", array("lang" => "en"), array("lang" => "en|bg"));
        // ok
        $this->assertTrue($route->matchPath("/en"));
        $this->assertTrue($route->matchPath("/bg/"));
        $this->assertTrue($route->matchPath("/"));
        $this->assertTrue($route->matchPath(""));
        // fails
        $this->assertFalse($route->matchPath("/ru"));
        $this->assertFalse($route->matchPath("/ru/index"));
    }

    public function testMatchMVC()
    {
        // this is real world example
        $route = new Route("/{controller}/{action}/{param}", array("controller" => "home", "action" => "index", "param" => ""));
        // ok
        $this->assertTrue($route->matchPath(""));
        $this->assertTrue($route->matchPath("/"));
        $this->assertTrue($route->matchPath("/home"));
        $this->assertTrue($route->matchPath("/home/"));
        $this->assertTrue($route->matchPath("/home/index"));
        $this->assertTrue($route->matchPath("/user/edit/3"));
        $this->assertTrue($route->matchPath("/user/edit/ivan"));
    }

    public function testHost()
    {
        $route = new Route("/");
        $this->assertEquals("", $route->getHost());
        // returns "" ? or it should be NULL / FALSE ?
        $this->assertSame(null, $route->getHost());
        $route->setHost("example.com");
        $this->assertEquals("example.com", $route->getHost());
        $route->setHost(null);
        $this->assertEquals("", $route->getHost());
        $this->assertSame(null, $route->getHost());

        // wrong but will be fixed
        // EDIT: this will not be fixed!
        // $route->setHost("http://example.com/users/list?page=1");
        // $this->assertEquals("example.com", $route->getHost());

        // sub domain
        $route->setHost("sub.example.com");
        $this->assertEquals("sub.example.com", $route->getHost());

        // check parameterized hosts
        $route->setHost("{subdomain}.example.com");
        $this->assertEquals("{subdomain}.example.com", $route->getHost());
    }

    public function testMatchHost()
    {
        $route = new Route("/");
        $this->assertTrue($route->matchHost("example.com"));
        $this->assertTrue($route->matchHost("sub.example.com"));

        $route->setHost("example.com");
        $this->assertTrue($route->matchHost("example.com"));
        $this->assertFalse($route->matchHost("www.example.com"));
        $this->assertFalse($route->matchHost("sub.example.com"));
        $this->assertFalse($route->matchHost("anything.com"));
        $this->assertFalse($route->matchHost("example.net"));
        $this->assertFalse($route->matchHost("foo.bar"));

        $route->setHost("");
        $this->assertTrue($route->matchHost("foobar.tld"));

        $route->setHost("sub.example.com");
        $this->assertTrue($route->matchHost("sub.example.com"));
        $this->assertFalse($route->matchHost("sub.sub.example.com"));
        $this->assertFalse($route->matchHost("example.com"));
        $this->assertFalse($route->matchHost("www.example.com"));
        $this->assertFalse($route->matchHost("foo.bar"));
    }

    public function testMatchHostWithParam()
    {
        $route = new Route("/");
        $route->setHost("{subdomain}.example.com");
        $this->assertTrue($route->matchHost("sub.example.com"));
        $this->assertTrue($route->matchHost("foobar.example.com"));
        $this->assertFalse($route->matchHost("example.com"));
        $this->assertFalse($route->matchHost("sub.sub.example.com"));
    }

    public function testMatchHostWithParamAndDefaultValue()
    {
        $route = new Route("/");
        $route->setHost("{subdomain}.example.com");
        $route->setDefault("subdomain", "www");
        $this->assertTrue($route->matchHost("sub.example.com"));
        $this->assertTrue($route->matchHost("www.example.com"));
        $this->assertTrue($route->matchHost("example.com"));
        $this->assertFalse($route->matchHost("sub.sub.example.com"));
    }

    public function testMatchHostWithParamAndRequisite()
    {
        $route = new Route("/");

        $route->setHost("{subdomain}.example.com");
        $route->setRequisite("subdomain", "en|bg");
        $this->assertTrue($route->matchHost("en.example.com"));
        $this->assertTrue($route->matchHost("bg.example.com"));
        $this->assertFalse($route->matchHost("example.com"));
        $this->assertFalse($route->matchHost("bg.www.example.com"));
        $this->assertFalse($route->matchHost("www.en.example.com"));
        $this->assertFalse($route->matchHost("sr.example.com"));
    }

    public function testMatchHostWithParamDefaultAndRequisite()
    {
        $route = new Route("/");

        $route->setHost("{subdomain}.example.com");
        $route->setRequisite("subdomain", "en|bg");
        $route->setDefault("subdomain", "en");
        $this->assertTrue($route->matchHost("en.example.com"));
        $this->assertTrue($route->matchHost("bg.example.com"));
        $this->assertTrue($route->matchHost("example.com"));
        $this->assertFalse($route->matchHost("bg.www.example.com"));
        $this->assertFalse($route->matchHost("www.en.example.com"));
        $this->assertFalse($route->matchHost("sr.example.com"));
    }

    public function testTLD()
    {
        $route = new Route("/");

        $route->setHost("example.{tld}");
        $this->assertTrue($route->matchHost("example.com"));
        $this->assertTrue($route->matchHost("example.eu"));
        $this->assertFalse($route->matchHost("foobar.com"));
        $this->assertFalse($route->matchHost("www.example.eu"));
    }

    public function testScheme()
    {
        // all
        $route = new Route("/");
        $this->assertEquals("", $route->getScheme());
        $this->assertSame(null, $route->getScheme());
        // http only
        $route->setScheme("http");
        $this->assertEquals("http", $route->getScheme());
        // all
        $route->setScheme(null);
        $this->assertEquals("", $route->getScheme());
        $this->assertSame(null, $route->getScheme());
        // https only
        $route->setScheme("https");
        $this->assertEquals("https", $route->getScheme());
        // all
        $route->setScheme("");
        $this->assertEquals("", $route->getScheme());
        $this->assertSame(null, $route->getScheme());
    }

    public function testMatchScheme()
    {
        // no limitations
        $route = new Route("/");
        $this->assertTrue($route->matchScheme("http"));
        $this->assertTrue($route->matchScheme("https"));
        $this->assertTrue($route->matchScheme("http://"));
        $this->assertTrue($route->matchScheme("https://"));
        $this->assertTrue($route->matchScheme("HTTP"));
        $this->assertTrue($route->matchScheme("HTTPS"));
        // not http/https, but should not happen
        $this->assertTrue($route->matchScheme("ftp"));

        // only http
        $route->setScheme("http");
        $this->assertTrue($route->matchScheme("http"));
        $this->assertFalse($route->matchScheme("https"));

        // only https
        $route->setScheme("https");
        $this->assertTrue($route->matchScheme("https"));
        $this->assertFalse($route->matchScheme("http"));
    }

    public function testMethod()
    {
        // all
        $route = new Route("/");
        $this->assertEquals("", $route->getMethod());
        $this->assertSame(null, $route->getMethod());
        // GET
        $route->setMethod("GET");
        $this->assertEquals("GET", $route->getMethod());
        // lowercase
        $route->setMethod("post");
        $this->assertEquals("POST", $route->getMethod());
        // all
        $route->setMethod(null);
        $this->assertEquals("", $route->getMethod());
        $this->assertSame(null, $route->getMethod());
        // several
        $route->setMethod("GET|POST|PUT");
        $this->assertEquals("GET|POST|PUT", $route->getMethod());
        // all
        $route->setMethod("");
        $this->assertEquals("", $route->getMethod());
        $this->assertSame(null, $route->getMethod());
    }

    public function testMatchMethod()
    {
        // all
        $route = new Route("/");
        $this->assertTrue($route->matchMethod("GET"));
        $this->assertTrue($route->matchMethod("HEAD"));
        $this->assertTrue($route->matchMethod("POST"));
        $this->assertTrue($route->matchMethod("PUT"));
        $this->assertTrue($route->matchMethod("DELETE"));
        $this->assertTrue($route->matchMethod("OPTIONS"));
        $this->assertTrue($route->matchMethod("get"));
        $this->assertTrue($route->matchMethod("post"));

        // GET
        $route->setMethod("GET");
        $this->assertTrue($route->matchMethod("GET"));
        $this->assertTrue($route->matchMethod("get"));
        $this->assertFalse($route->matchMethod("POST"));
        $this->assertFalse($route->matchMethod("put"));

        // POST
        $route->setMethod("post");
        $this->assertTrue($route->matchMethod("POST"));
        $this->assertTrue($route->matchMethod("post"));
        $this->assertFalse($route->matchMethod("GET"));
        $this->assertFalse($route->matchMethod("put"));
    }
}
