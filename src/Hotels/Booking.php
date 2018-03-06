<?php

namespace Innstant\Hotels;

use Innstant\Helper;
use Innstant\Innstant;

class Booking extends Innstant
{
    use Helper;

    /**
     * The string of session.
     *
     * @var string
     */
    protected $session;

    /**
     * The string of clientRef.
     *
     * @var string
     */
    protected $clientRef;

    /**
     * The string of customerCountryCode.
     *
     * @var string
     */
    protected $customerCountryCode;

    /**
     * The array of customer.
     *
     * @var array
     */
    protected $customer = [];

    /**
     * The array of rooms.
     *
     * @var array
     */
    protected $rooms = [];

    /**
     * The array of specialRequest.
     *
     * @var array
     */
    protected $specialRequest;

    public function __construct($session)
    {
        $this->session = $session;
    }

    public function setCustomer($customer)
    {
        $this->customer = $customer;

        return $this;
    }

    public function setCustomerCountryCode($customerCountryCode)
    {
        $this->customerCountryCode = $customerCountryCode;

        return $this;
    }

    public function setRooms($rooms)
    {
        foreach ($rooms as $room) {
            $pax = [];

            foreach ($room['pax-group'] as $value) {
                $pax[] = ['pax' => $value];
            }

            $this->rooms[] = [[
                'room' => [
                    '@resultId' => $room['id'],
                    'pax-group' => $pax
                ],
            ]];
        }

        return $this;
    }

    public function setClientRef($clientRef)
    {
        $this->clientRef = $clientRef;

        return $this;
    }

    public function specialRequest($value)
    {
        $count = 0;

        if (isset($this->specialRequest['special-request-fields'])) {
            $count = count($this->specialRequest['special-request-fields']);
        }

        $this->specialRequest['special-request-fields'][] =
        [
            'field' => [
                '@code' => $count,
                '@' => $value,
            ],
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
                'clientIp' => self::$clientIp ?? null,
                'clientUserAgent' => self::$agent ?? null
            ],
            'hotel-book' => [
                '@session' => $this->session,
                '@clientRef' => $this->clientRef,
                '@customerCountryCode' => $this->customerCountryCode,
                'customer' => $this->customer,
                'rooms' => $this->rooms,
                $this->specialRequest
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
