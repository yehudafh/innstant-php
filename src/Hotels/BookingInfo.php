<?php

namespace Innstant\Hotels;

use Innstant\Helper;
use Innstant\Innstant;

class BookingInfo extends Innstant
{
    use Helper;

    /**
     * The string of session.
     *
     * @var array
     */
    protected $session;

    /**
     * The number of rooms.
     *
     * @var array
     */
    protected $rooms = [];

    public function __construct($session, $rooms)
    {
        $this->session = $session;

        foreach (explode(",", $rooms) as $room) {
            $this->rooms[] = ['result' => $room];
        }
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
            'hotel-booking-info' => [
                '@session' => $this->session,
                'results' => [
                    $this->rooms
                ],
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

        $booking = $response['booking-options'];
        $count = $booking['@attributes']['count'];

        if ($count == 1) {
            $booking['booking-option'] = [$booking['booking-option']];
        }

        $bookings = [];

        foreach ($booking['booking-option'] as $book) {
            $room = $book['room'];
            $price = $room['price']['@content'];

            $bookings[] = [
                'hotel' => $book['hotel-info'],
                'room'  => [
                    'name'           => $room['name'],
                    'price'          => $price,
                    'currency'       => $room['price']['@attributes']['currency'],
                    'board'          => $room['board']['@content'],
                    'type'           => $this->getRoomType($room['name']),
                    'board_code'      => $room['board']['@attributes']['code'],
                    'provider'       => $room['provider'],
                    'billable'       => $room['@attributes']['billable'],
                    'commissionable' => $room['@attributes']['commissionable'],
                    'onrequest'      => $room['@attributes']['onrequest'],
                    'count'          => $room['@attributes']['count'],
                ],
                'remarks'             => $book['remarks'],
                'accepted-cards'      => $book['accepted-cards'],
                'additional-info'     => $book['additional-info'],
                'extra-verifications' => $book['extra-verifications'],
                'cancellation'        => $this->cancellation($book, $price),
                'token'               => $book['@attributes']['token'],
                'onrequest'           => $book['@attributes']['onrequest'],
            ];
        }

        return [
            'success'  => $response['@attributes']['success'] ?? null,
            'time'     => $response['@attributes']['time'] ?? null,
            'session'  => $response['@attributes']['session'] ?? null,
            'count'    => $count,
            'split'    => $booking['@attributes']['split'],
            'bookings' => $bookings,
        ];
    }
}
