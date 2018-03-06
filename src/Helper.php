<?php

namespace Innstant;

trait Helper
{
    public function setPercentCancellation($percent)
    {
        $this->percentCancellation = $percent;

        return $this;
    }

    public function getRoomType($name) {
        $roomTypes = [
            'Double',
            'Twin',
            'Standard',
            'Family',
            'Classic',
            'Studio',
            'Suite',
            'Comfort',
            'Superior',
            'Deluxe',
            'Leisure',
            'Executive',
            'Premier',
        ];

        foreach ($roomTypes as $roomType) {
            if (stripos($name, $roomType) !== false) {
                return $roomType;
            }
        }

        return null;
    }

    public function cancellation($room, $price)
    {
        $cancellations = [];

        if (!isset($room['cancellation'])) {
            return $cancellations;
        }

        if(!isset($room['cancellation']['frame'][0])){
            $room['cancellation']['frame'] = [$room['cancellation']['frame']];
        }

        foreach ($room['cancellation']['frame'] as $value) {
            $percent = $this->percentCancellation;

            $cancellation = [
                'type'           => $value['@attributes']['type'],
                'endTime'        => $value['@attributes']['endTime'],
                'timezone'       => $value['@attributes']['timezone'],
                'cost_amount'    => $value['cost']['@attributes']['amount'] ?? null,
                'cost_currency'  => $value['cost']['@attributes']['currency'] ?? null,
            ];

            $cancellation['percent'] = (($cancellation['cost_amount'] ?? 0) / $price) * 100;

            if ($cancellation['percent'] <= $percent) {
                $cancellations[] = $cancellation;
            }
        }

        return $cancellations;
    }
}
