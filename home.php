<?php

require __DIR__ . '/vendor/autoload.php';
require 'TwitchAuth.php';

session_start();

$twig_loader = new Twig_Loader_Filesystem('twig_templates');
$twig = new Twig_Environment($twig_loader, ['debug' => true]);
$twig->addExtension(new Twig_Extension_Debug());
$provider = TwitchAuth::getInstance();

if(!$_SESSION['twitch_token']){
    exit('No auth token, you shouldn\'t be here 🤔</br><a href="/">Rescue me</a>');
}

$streamer_name_shim = empty($_POST['streamer_name'])
    ? ''
    : '?login='.$_POST['streamer_name'];


$res = $provider->doAuthenticatedRequest('GET',
    'https://api.twitch.tv/helix/users'.$streamer_name_shim
);

if(!empty($res->data))
{
    $id = $res->data[0]->id;

    $events = mutateEvents($provider->getEvents($id));

    $params = [
        'found'=>true,
        'access_token' => $_SESSION['twitch_token'],
        'username' => $res->data[0]->display_name,
        'events'=>$events,
    ];
} else {
    $params = [
        'found'=>false,
        'access_token' => $_SESSION['twitch_token'],
        'username' => 'User not found',
        'events'=>[],
    ];
}


echo $twig->render('home.html.twig', $params);

function mutateEvents(object $origEvents)
{
    $len = $origEvents->_total <= 10
        ? $origEvents->_total
        : 10;

    $newEvents = [];

    for ($i=0; $i < $len; $i++) { 
        $obj = (object) [
            'time'=>$origEvents->events[$i]->start_time,
            'title'=>$origEvents->events[$i]->title,
            'image'=>fixEventImage($origEvents->events[$i]->cover_image_url),
            'game'=>$origEvents->events[$i]->game->name,
        ];

        $newEvents[] = $obj;
    }

    return $newEvents;
}

function fixEventImage(string $url)
{
    return str_replace(['{width}','{height}'], '75', $url);
}
