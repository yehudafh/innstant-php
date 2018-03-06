<?php

namespace Innstant\Hotels;

use Innstant\Innstant;

class DestinationSearch extends Innstant
{
    /**
     * The array of entity.
     *
     * @var array
     */
    protected $entity = [];

    /**
     * The array of dates.
     *
     * @var array
     */
    protected $dates = [];

    /**
     * The array of filters.
     *
     * @var array
     */
    protected $filters = [];

    /**
     * The array of pax-group.
     *
     * @var array
     */
    protected $paxGroup = [];

    /**
     * The array of currencies.
     *
     * @var array
     */
    protected $currencies = [];

    /**
     * Set the entity.
     *
     * @param  int  $id
     * @param  string  $type
     * @return $this
     */
    public function setEntity($id, $type = 'location')
    {
        $this->entity = [
            '@entityType' => $type,
            '@entityID' => $id,
        ];

        return $this;
    }

    /**
     * Set the dates.
     *
     * @param  string  $from
     * @param  string  $to
     * @return $this
     */
    public function setDates($from, $to)
    {
        $this->dates = [
            '@from' => $from,
            '@to' => $to,
        ];

        return $this;
    }

    /**
     * Set the filters.
     *
     * @param  array  $filters
     * @return $this
     */
    public function setFilters($filters)
    {
        foreach ($filters as $type => $filter) {
            $this->filters[] = [
                'filter' => [
                    '@' => $filter,
                    '@type' => $type
                ]
            ];
        }

        return $this;
    }

    /**
     * Set the pax-group.
     *
     * @param  array  $paxGroup
     * @return $this
     */
    public function setPaxGroup($paxGroup)
    {
        $paxes = [];

        foreach ($paxGroup['paxes'] as $pax) {
            $ages = [];

            foreach ($pax['children'] as $age) {
                $ages[] = ['age' => $age];
            }

            $paxes[]['pax'] = [
                [
                    'adults' => [
                        '@count' => $pax['adults']
                    ], [
                        'children' => [
                            '@count' => count($pax['children']),
                            $ages
                        ]
                    ]
                ]
            ];
        }

        $this->paxGroup = ['@customerCountryCode' => $paxGroup['country']];

        $this->paxGroup[] = [$paxes];

        return $this;
    }

    /**
     * Set the currencies.
     *
     * @param  array  $default
     * @param  array  $currencies
     * @return $this
     */
    public function setCurrencies($default = null, $currencies = [])
    {
        if ($default) {
            $this->currencies['@default'] = $default;
        } else {
            $this->currencies['@all'] = 'true';
        }

        foreach ($currencies as $currency) {
           $this->currencies[] = ['currency' => ['@code' => $currency]];
       }

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
            'hotel-search' => [
                $this->entity,
                'dates' => $this->dates,
                'filters' => $this->filters,
                'pax-group' => $this->paxGroup,
                'currencies' => $this->currencies
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

        return [
            'server' => $response['search']['server'] ?? null,
            'status' => $response['search']['@attributes']['status'] ?? null,
            'success' => $response['@attributes']['success'] ?? null,
            'time' => $response['@attributes']['time'] ?? null,
            'session' => $response['@attributes']['session'] ?? null,
        ];
    }
}
