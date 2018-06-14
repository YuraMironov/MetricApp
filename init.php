<?php


use Symfony\Component\HttpFoundation\Request;

require 'vendor/autoload.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('ROOT_PATH', realpath(__DIR__));
define('SRC_PATH', ROOT_PATH . DIRECTORY_SEPARATOR . 'src');


//twig
$twigLoader = new Twig_Loader_Filesystem('view/twig');
$twig = new Twig_Environment($twigLoader, ['debug' => true]);
$twig->addExtension(new Twig_Extension_Debug());
$array_values = new Twig_Function('myarrval', function (array $a) {
    return array_values($a);
});
$array_avg = new Twig_Function('myarravg', function (array $a) {
    return array_sum($a)/count($a);
});
$twig->addFunction($array_values);
$twig->addFunction($array_avg);

//request
$request = Request::createFromGlobals();