<?php

require __DIR__ . '/vendor/autoload.php';
require 'TwitchAuth.php';

session_start();

$provider = TwitchAuth::getInstance();
if($_SESSION['twitch_token']){
    //destroy our token
    $client = new GuzzleHttp\Client();
    $req = $client->request('POST',
        $provider->getTokenRevokeUrl($_SESSION['twitch_token'])
    );
}

session_destroy();

header('Location: /index.php');

exit;