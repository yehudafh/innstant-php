<?php

namespace Innstant\Hotels;

use Innstant\Innstant;

class BookingConfirm extends Innstant
{
    /**
     * The string of session.
     *
     * @var string
     */
    protected $session;

    /**
     * The string of id.
     *
     * @var string
     */
    protected $id;

    public function __construct($session, $id)
    {
        $this->session = $session;
        $this->id = $id;
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
            'hotel-confirm-reservation' => [
                '@session' => $this->session,
                '@reservationId' => $this->id,
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

        return $response;
    }
}
