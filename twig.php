<?php
/* Alliance Logos
 * Copyright (C) 2013 SquizzLabs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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

$igb = stristr(@$_SERVER["HTTP_USER_AGENT"], "EVE-IGB");
$twig->addGlobal("eveigb", $igb);
