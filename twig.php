<?php
// Load Twig globals
$app->view(new \Slim\Extras\Views\Twig());
$twig = $app->view()->getEnvironment();

\Slim\Extras\Views\Twig::$twigOptions = array(
    'charset'           => 'utf-8',
    'cache'             => 'cache/templates',
    'auto_reload'       => true,
    'strict_variables'  => false,
    'autoescape'        => true
);

\Slim\Extras\Views\Twig::$twigExtensions = array(
    'Twig_Extensions_Slim'
);

// Twig globals
$twig->addGlobal("siteurl", $baseAddr);
//$twig->addGlobal("fullsiteurl", "http://".$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"]);
$twig->addGlobal("image_character", "https://image.eveonline.com/Character/");
$twig->addGlobal("image_corporation", "https://image.eveonline.com/Corporation/");
$twig->addGlobal("image_alliance", "https://image.eveonline.com/Alliance/");
//$twig->addGlobal("image_item", "https://image.eveonline.com/Type/");
//$twig->addGlobal("image_ship", "https://image.eveonline.com/Render/");
//$twig->addGlobal("requesturi", $_SERVER["REQUEST_URI"]);
$twig->addGlobal("siteName", $siteName);

$twig->addExtension(new UserGlobals());
//require_once( "vendor/twig-markdown/lib/Twig/Extensions/Core.php" );

//$twig->addExtension(new Twig_Markdown_Extension());
$twig->addFunction("pageTimer", new Twig_Function_Function("Util::pageTimer"));
$twig->addFunction("queryCount", new Twig_Function_Function("Db::getQueryCount"));
$twig->addFunction("isActive", new Twig_Function_Function("Util::isActive"));
/*$twig->addFunction("firstUpper", new Twig_Function_Function("Util::firstUpper"));
$twig->addFunction("pluralize", new Twig_Function_Function("Util::pluralize"));
$twig->addFunction("calcX", new Twig_Function_Function("Util::calcX"));
$twig->addFunction("calcY", new Twig_Function_Function("Util::calcY"));
$twig->addFunction("formatIsk", new Twig_Function_Function("Util::formatIsk"));
$twig->addFunction("shortNum", new Twig_Function_Function("Util::formatIsk"));
$twig->addFunction("shortString", new Twig_Function_Function("Util::shortString"));
$twig->addFunction("truncate", new Twig_Function_Function("Util::truncate"));
$twig->addFunction("chart", new Twig_Function_Function("Chart::addChart"));
$twig->addFunction("getMonth", new Twig_Function_Function("Util::getMonth"));
$twig->addFunction("getLongMonth", new Twig_Function_Function("Util::getLongMonth"));
$twig->addFunction("lang", new Twig_Function_Function("Util::translation", array("is_safe" => array("html"))));*/

$igb = stristr(@$_SERVER["HTTP_USER_AGENT"], "EVE-IGB");
$twig->addGlobal("eveigb", $igb);
