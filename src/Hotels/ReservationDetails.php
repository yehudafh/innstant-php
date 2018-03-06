<?php

namespace Innstant\Hotels;

use Innstant\Helper;
use Innstant\Innstant;

class ReservationDetails extends Innstant
{
    use Helper;

    /**
     * The string of id.
     *
     * @var array
     */
    protected $id;

    public function __construct($id)
    {
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
            'hotel-reservation-details' => [
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

        $reservation = array_merge(
            $response['reservation']['@attributes'],
            $response['reservation']['item']
        );

        $price = $reservation['booking']['price']['@attributes'];

        return [
            'success' => $response['@attributes']['success'] ?? null,
            'time' => $response['@attributes']['time'] ?? null,
            'session' => $response['@attributes']['session'] ?? null,
            'reservation' => [
                'reservationId' => $reservation['id'],
                'reservationStatus' => $reservation['status'],
                'clientReference' => $reservation['clientReference'],
                'quantity' => $reservation['quantity'],
                'error' => $reservation['error'] ?? null,
                'name' => $reservation['name'],
                'board' => $reservation['board']['@content'],
                'board_code' => $reservation['board']['@attributes']['code'],
                'remarks' => $reservation['remarks'],
                'reference' => $reservation['source']['reference'],
                'reference_provider' => $reservation['source']['@attributes']['provider'],
                'booking' => [
                    'price' => $price['amount'] ?? null,
                    'currency' => $price['currency'] ?? null,
                    'billable' => $reservation['booking']['@attributes']['billable'] ?? null,
                    'payAtTheHotel' => $reservation['booking']['@attributes']['payAtTheHotel'] ?? null,
                ],
                'old_price' => [
                    'price' => $reservation['old-price']['@attributes']['amount'] ?? null,
                    'currency' => $reservation['old-price']['@attributes']['currency'] ?? null,
                ],
                'cancellation' => $this->cancellation($reservation, $price['amount']),
                'pax-groups' => $reservation['pax-groups'],
                'token' => $reservation['@attributes']['token'],
                'status' => $reservation['@attributes']['status'],
                'id' => $reservation['@attributes']['id'],
            ],
        ];
    }
}
