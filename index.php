<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

umask(0000);

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/app/autoload.php';

// set environment
$env = 'prod';
$debug = 0;

if(file_exists('.env')){
	$dotenv = new Dotenv\Dotenv(__DIR__);
	$dotenv->load();

	$env = $_SERVER['SYMFONY_ENV'];
	$debug = $_SERVER['SYMFONY_DEBUG'];
}

Debug::enable();

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();

$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
