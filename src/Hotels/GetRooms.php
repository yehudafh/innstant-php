<?php

namespace Innstant\Hotels;

use Innstant\Helper;
use Innstant\Innstant;

class GetRooms extends Innstant
{
    use Helper;

    /**
     * The string of session.
     *
     * @var array
     */
    protected $session;

    /**
     * The number of id.
     *
     * @var array
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
            'hotel-rooms' => [
                '@session' => $this->session,
                '@id' => $this->id,
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

        $rooms = [];

        if (isset($response['result']['rooms']['room'])) {
                if (!isset($response['result']['rooms']['room'][0])) {
                    $response['result']['rooms']['room'] = [$response['result']['rooms']['room']];
                }

                $rooms = [];

                foreach ($response['result']['rooms']['room'] as $room) {
                    $rooms[] = $this->getRoom($room);
                }
        }

        return [
            'status' => $response['job']['@attributes']['status'] ?? null,
            'success' => $response['@attributes']['success'] ?? null,
            'time' => $response['@attributes']['time'] ?? null,
            'session' => $response['@attributes']['session'] ?? null,
            'rooms' => $rooms,
        ];
    }

    public function getRoom($room)
    {
        $min = (int)$room['@attributes']['minquantity'] > 1 ? (int)$room['@attributes']['minquantity'] : 1;
        $max = (int)$room['@attributes']['maxquantity'];
        $price = $room['price']['@content'] / $min;
        $pax = $room['pax'];

        if ($pax['children-ages']) {
            $pax['children-ages'] = explode(',', $pax['children-ages']);
        }

        return [
            'roomID'            => $room['room-id'],
            'resultID'          => $room['@attributes']['resultid'],
            'onrequest'         => $room['@attributes']['onrequest'],
            'commissionable'    => $room['@attributes']['commissionable'],
            'pay_at_hotel'      => $room['@attributes']['payAtTheHotel'],
            'billable'          => $room['@attributes']['billable'],
            'packageRate'       => $room['@attributes']['packageRate'],
            'cachedResult'      => $room['@attributes']['cachedResult'],
            'min'               => $min,
            'max'               => $max,
            'price'             => $price,
            'currency'          => $room['price']['@attributes']['currency'],
            'name'              => $room['name'],
            'board'             => $room['board']['@content'],
            'board_code'        => trim($room['board']['@attributes']['code']),
            'type'              => $this->getRoomType($room['name']),
            'provider'          => $room['provider'],
            'pax'               => $pax,
            'cancellation'      => $this->cancellation($room, $price),
            'special'           => $room['special-deals'] ?? null,
            'category'          => $room['category'] ?? null,
            'bedding'           => $room['bedding'] ?? null,
            'description'       => (isset($room['description']) && $room['description'] !== ($room['special-deals']['special-deal']['description'] ?? '')) ? $room['description'] : $room['name'] . ' - ' . $room['board']['@content'],
        ];
    }
}
