<?php
$app->notFound(function () use ($app) {
    $app->redirect("/");
});

// Default route
$app->get("/", function () use ($app){
    include( "view/index.php" );
});
