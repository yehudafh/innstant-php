<?php

namespace Innstant\Hotels;

use Innstant\Innstant;

class StaticRates extends Innstant
{
    /**
     * The array of entities.
     *
     * @var array
     */
    protected $entities = [];

    /**
     * Set the entity.
     *
     * @param  int  $id
     * @param  string  $type
     * @return $this
     */
    public function __construct($entities)
    {
        foreach ($entities as $entity) {
            $this->entities[] = ['entity' => $entity];
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
            'hotel-static-results' => $this->entities
        ];
    }
}
