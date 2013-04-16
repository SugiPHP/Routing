<?php

namespace SugiPHP\Routing;

interface RouteInterface
{
	public function match($path, $method, $host, $scheme);
}
