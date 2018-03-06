<?php

namespace Innstant;

/**
 * Class Innstant
 *
 * @package Innstant
 */
class Innstant
{
    use ApiRequestor;
    /**
     * The base URL for the Innstant API.
     *
     * @var string
     */
    public static $apiBase = 'http://mishor4.innstant-servers.com';

    /**
     * The version of the Innstant API to use for requests.
     *
     * @var string
     */
    public static $apiVersion = '4.0';

    /**
     * The Innstant authentication username.
     *
     * @var string
     */
    public static $username;

    /**
     * The Innstant authentication password.
     *
     * @var string
     */
    public static $password;

    /**
     * The Innstant authentication agent.
     *
     * @var string
     */
    public static $agent;

    /**
     * The uuid.
     *
     * @var string
     */
    public static $uuid;

    /**
     * The client ip.
     *
     * @var string|null
     */
    public static $clientIp = null;

    /**
     * The client user agent.
     *
     * @var string|null
     */
    public static $clientUserAgent = null;

    /**
     * The number of percent Cancellation.
     *
     * @var array
     */
    protected $percentCancellation = 80;

    /**
     * Sets the username to be used for requests.
     *
     * @param string $username
     */
    public static function setUserName($username)
    {
        self::$username = $username;
    }

    /**
     * Sets the API password to be used for requests.
     *
     * @param string $password
     */
    public static function setPassword($password)
    {
        self::$password = $password;
    }

    /**
     * Sets the API clientIp to be used for requests.
     *
     * @param string $clientIp
     */
    public static function setClientIp($clientIp)
    {
        self::$clientIp = $clientIp;
    }

    /**
     * Sets the API client UserAgent to be used for requests.
     *
     * @param string $clientUserAgent
     */
    public static function setClientUserAgent($clientUserAgent)
    {
        self::$clientUserAgent = $clientUserAgent;
    }

    /**
     * Sets the API agent to be used for requests.
     *
     * @param string $agent
     */
    public static function setAgent($agent)
    {
        self::$agent = $agent;
    }

    /**
     * Sets the uuid for requests.
     *
     * @param string $uuid
     */
    public function setUuid($uuid)
    {
        self::$uuid = $uuid;

        return $this;
    }
}
