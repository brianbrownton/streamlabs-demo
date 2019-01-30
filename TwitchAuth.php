<?php

require __DIR__ . '/vendor/autoload.php';

/**
 * 
 */
final class TwitchAuth
{
    private static $instance;
    private $options;

    public static function getInstance(): TwitchAuth
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }
    
    private function __construct()
    {
        $factory = new RandomLib\Factory;
        $generator = $factory->getMediumStrengthGenerator();
        $state = $generator->generateString(32, '0123456789');

        $this->options = [
            'clientId' => 'v8vgr7kkyu24lcjkkbqs66vnderfpw',
            'clientSecret' => 'nv77h62ahao1j58ggucozjw17j17u0',
            'redirectUri' => 'http://streamlabs.wzy.ca/doLogin.php',
            'state' => $state,
            'scopes' => []
        ];

        // return parent::__construct($options);
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public function getAuthUrl()
    {
        // return 'https://id.twitch.tv/oauth2/authorize';
        return "https://id.twitch.tv/oauth2/authorize"
            ."?client_id={$this->options['clientId']}"
            ."&response_type=code"
            ."&redirect_uri={$this->options['redirectUri']}"
            ."&state={$this->options['state']}";
    }

    public function getAuthHeader(string $token)
    {
        return ['Authorization' => "Bearer $token"];
    }

    public function getState()
    {
        return $this->options['state'];
    }

    public function getClientId()
    {
        return $this->options['clientId'];
    }

    public function getTokenRequestUrl(string $code)
    {
        return "https://id.twitch.tv/oauth2/token"
            ."?client_id={$this->options['clientId']}"
            ."&client_secret={$this->options['clientSecret']}"
            ."&code={$code}"
            ."&grant_type=authorization_code"
            ."&redirect_uri={$this->options['redirectUri']}";
    }

    public function getTokenRevokeUrl(string $token)
    {
        return "https://id.twitch.tv/oauth2/revoke"
            ."?client_id={$this->options['clientId']}"
            ."&token={$token}";
    }

    public function getRevokeUrl()
    {
        return 'https://id.twitch.tv/oauth2/revoke';
    }

    public function getEvents(string $channelId)
    {
        return $this->doAuthenticatedRequest('GET',
            "https://api.twitch.tv/v5/channels/$channelId/events?client_id={$this->options['clientId']}"
        );
    }

    public function doAuthenticatedRequest(string $method, string $url)
    {

        $client = new GuzzleHttp\Client();
        $req = new GuzzleHttp\Psr7\Request($method,
            $url,
            ['Authorization'=>'Bearer '.$_SESSION['twitch_token']]
        );

        $res = $client->send($req);

        return json_decode($res->getBody()->getContents());
    }
}

