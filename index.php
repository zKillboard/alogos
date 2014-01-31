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

$t = microtime(true);
//include_once("/var/killboard/xhprof/inc/prepend.php");

// Fire up the session!
session_start();

// Autoload Slim + Twig
require( "vendor/autoload.php" );

// Load modules + database stuff (and the config)
require( "init.php" );

// initiate the timer!
$timer = new Timer();

// Start slim and load the config from the config file
$app = new \Slim\Slim($config);

// Load the routes - always keep at the bottom of the require list ;)
include( "routes.php" );

// Load twig stuff
include( "twig.php" );

// Run the thing!
$app->run();
