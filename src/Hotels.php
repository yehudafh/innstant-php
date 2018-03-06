<?php

namespace Innstant;

use Innstant\Innstant;
use Innstant\Hotels\Booking;
use Innstant\Hotels\GetRooms;
use Innstant\Hotels\BookingInfo;
use Innstant\Hotels\StaticRates;
use Innstant\Hotels\PollRequest;
use Innstant\Hotels\Cancellation;
use Innstant\Hotels\BookingConfirm;
use Innstant\Hotels\DestinationSearch;
use Innstant\Hotels\ReservationDetails;

class Hotels extends Innstant
{
    public static function destinationSearch()
    {
        return new DestinationSearch();
    }

    public static function pollRequest(...$args)
    {
        return new PollRequest(...$args);
    }

    public static function getRooms(...$args)
    {
        return new GetRooms(...$args);
    }

    public static function bookingInfo(...$args)
    {
        return new BookingInfo(...$args);
    }

    public static function booking(...$args)
    {
        return new Booking(...$args);
    }

    public static function bookingConfirm(...$args)
    {
        return new BookingConfirm(...$args);
    }

    public static function reservationDetails(...$args)
    {
        return new ReservationDetails(...$args);
    }

    public static function cancellation(...$args)
    {
        return new Cancellation(...$args);
    }

    public static function staticRates(...$args)
    {
        return new StaticRates(...$args);
    }
}
