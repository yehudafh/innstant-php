<?php

namespace Innstant\Hotels;

use Innstant\Innstant;

class PollRequest extends Innstant
{
    /**
     * The string of session.
     *
     * @var array
     */
    protected $session;

    /**
     * The number of last.
     *
     * @var array
     */
    protected $last = 0;

    public function __construct($session, $last)
    {
        $this->session = $session;
        $this->last = $last ?? 0;
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
            'hotel-poll' => [
                '@session' => $this->session,
                '@last' => $this->last,
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

        $count = $response['results']['@attributes']['count'];

        if ($count == '1') {
            $response['results']['result'] = [$response['results']['result']];
        }

        $results = [];

        if($response['results']['result'] ?? false) {
            foreach ($response['results']['result'] as $key => $result) {

                if (isset($result['non-billable-price'])) { // availability coming from direct providers
                    $price = $result['non-billable-price']['@attributes']['minCommissionablePrice'];
                    $currency = $result['non-billable-price']['@attributes']['currency'];
                    $min = $result['non-billable-price']['min-rooms']['min-room']['@attributes']['roomCount'];
                } else { // availablity coming from innstant.travel
                    $price = $result['price']['@attributes']['minCommissionablePrice'];
                    $currency = $result['price']['@attributes']['currency'];
                }

                if(isset($min) && $min > 1){
                    $price = $price / $min;
                }

                if (isset($result['special-deals'])) {
                    if (is_array($result['special-deals']['special-deal'])) {
                        $special = $result['special-deals']['special-deal'];
                    } else {
                        $special = [$result['special-deals']['special-deal']];
                    }
                } else {
                    $special = null;
                }

                $results[] = [
                    'id'             => $result['@attributes']['id'] ?? null,
                    'providers'      => explode(',', $result['@attributes']['providers']) ?? null,
                    'minProvider'    => $result['@attributes']['minProvider'] ?? null,
                    'hasPackageRate' => $result['@attributes']['hasPackageRate'] ?? null,
                    'boards'         => explode(',', $result['@attributes']['availableBoards']),
                    'price'          => $price,
                    'currency'       => $currency,
                    'special'        => $special,
                ];
            }
        }

        return [
            'server' => $response['job']['server'] ?? null,
            'status' => $response['job']['@attributes']['status'] ?? null,
            'success' => $response['@attributes']['success'] ?? null,
            'time' => $response['@attributes']['time'] ?? null,
            'session' => $response['@attributes']['session'] ?? null,
            'last' => $response['results']['@attributes']['last'] ?? null,
            'count' => $response['results']['@attributes']['count'] ?? null,
            'results' => $results,
        ];
    }
}
