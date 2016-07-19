<?php

use Symfony\Component\Debug\Debug;
use Symfony\Component\HttpFoundation\Request;

// If you don want to setup permissions the proper way, just comment the following PHP line
umask(0000);

/**
 * @var Composer\Autoload\ClassLoader $loader
 */
$loader = require __DIR__.'/app/autoload.php';

/** @throws \Dotenv\Exception\InvalidPathException If the .env file does not exists */
$dotenv = new Dotenv\Dotenv(__DIR__);
$dotenv->load();

// required variables
$dotenv->required(['SYMFONY__ENV', 'SYMFONY__DEBUG']);

// the app environment
$env = getenv('SYMFONY__ENV');
// whether to enable debugging or not in the app
$debug = (bool) getenv('SYMFONY__DEBUG');

// enable the debug mode
if ($debug) {
    Debug::enable();
}

// @TODO, whene to use this files?
// include_once __DIR__.'/../var/bootstrap.php.cache';
// $kernel = new AppCache($kernel);
// Request::enableHttpMethodParameterOverride();

$kernel = new AppKernel($env, $debug);
$kernel->loadClassCache();

$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
