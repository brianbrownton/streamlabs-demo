<?php

require __DIR__ . '/vendor/autoload.php';
require 'TwitchAuth.php';

//drop any existing sessions
if(isset($_SESSION)){
    session_destroy();
}

session_start();

$twig_loader = new Twig_Loader_Filesystem('twig_templates');
$twig = new Twig_Environment($twig_loader);
$provider = TwitchAuth::getInstance();

$_SESSION['state'] = $provider->getState();

echo $twig->render('index.html.twig', [
    'login_url' => $provider->getAuthUrl(),
]);

exit;
