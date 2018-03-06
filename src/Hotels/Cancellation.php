<?php

namespace Innstant\Hotels;

use Innstant\Innstant;

class Cancellation extends Innstant
{
    /**
     * The string of id.
     *
     * @var array
     */
    protected $id;

    /**
     * The string of reason.
     *
     * @var array
     */
    protected $reason = [];

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function setReason($reason)
    {
        $this->reason = [
            'cancellation-reason' => $reason
        ];

        return $this;
    }

    /**
     * Convert the instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'auth' => [
                'username' => self::$username,
                'password' => self::$password,
                'agent' => self::$agent,
            ],
            'hotel-cancel' => [
                '@id' => $this->id,
                $this->reason,
            ]
        ];
    }

    /**
     * Convert the response from instance to an api clean.
     *
     * @return array
     */
    public function toApi()
    {
        $response = $this->getResponse();

        if (isset($response['error'])) {
            return $this->getErrorReponse($response);
        }

        return [
            'success' => $response['@attributes']['success'] ?? null,
            'time' => $response['@attributes']['time'] ?? null,
            'session' => $response['@attributes']['session'] ?? null,
            'cancellation' => $response['cancellation']['@attributes']['status'] ?? null,
        ];
    }
}
