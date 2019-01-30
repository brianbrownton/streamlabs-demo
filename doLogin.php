<?php

require __DIR__ . '/vendor/autoload.php';
require 'TwitchAuth.php';

session_start();

$twig_loader = new Twig_Loader_Filesystem('twig_templates');
$twig = new Twig_Environment($twig_loader);
$provider = TwitchAuth::getInstance();


//check to make sure they came from twitch auth
if ($_GET['state'] !== $_SESSION['state']){
    echo "GET: " . $_GET['state'] . "\n";
    echo "SESSION: " . $_SESSION['state'] . "\n";
    exit('states don\'t match, issue with OAuth login. <a href="/">Please try again</a>');

//continue to get an access token
} else {
    $_SESSION['code'] = $_GET['code'];

    $client = new GuzzleHttp\Client();
    $req = $client->request('POST',
        $provider->getTokenRequestUrl($_SESSION['code'])
    );

    $res = json_decode($req->getBody());
    $_SESSION['twitch_token'] = $res->access_token;
    /*
        also available if needed:
        expires_in => <time left on token>
        refresh_token => <token used to get a new access_token>
        token_type => bearer
    */

    header('Location: /home.php');
    exit;
}
