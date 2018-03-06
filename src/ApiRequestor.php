<?php

namespace Innstant;

use FluidXml\FluidXml;

trait ApiRequestor {
    public function getErrorReponse($response)
    {
        $error = $response['@attributes'];
        $error['error'] = $response['error']['@content'];
        $error['error_code'] = $response['error']['@attributes']['code'];

        if (isset($error['booking-options'])) {
            $error['booking-options'] = $response['booking-options'] ?? null;
        }

        return $error;
    }

    public function toXml()
    {
        $xml = new FluidXml('request');

        $xml->attr(['version' => self::$apiVersion])->add($this->toArray());

        return $xml;
    }

    /**
     * Convert the instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $json = json_encode($this->jsonSerialize(), $options);

        return $json;
    }

    /**
     * Convert the object into something JSON serializable.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function getResponse()
    {
        $http = new \GuzzleHttp\Client();

        $xmlRequest = $this->toXml();

        $response = $http->request('POST', self::$apiBase, ['body' => $xmlRequest]);

        $xmlResponse = $response->getBody();

        $this->outputLog($xmlRequest, $xmlResponse);

        return $this->xmlToArray($xmlResponse);
    }

    public function outputLog($xmlRequest, $xmlResponse)
    {
        $log = [
            'uuid' => self::$uuid ?? null,
            'called' => get_called_class(),
            'request' => (string) $xmlRequest,
            'response' => (string) $xmlResponse
        ];

        // Log::create($log);
    }

    function xmlToArray($xml) {
        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $root = $doc->documentElement;
        $output = $this->nodeToArray($root);
        $output['@root'] = $root->tagName;

        return $output;
    }

    function nodeToArray($node) {
        $output = [];

        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;

            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->nodeToArray($child);

                    if(isset($child->tagName)) {
                        $t = $child->tagName;

                        if(!isset($output[$t])) {
                            $output[$t] = [];
                        }

                        $output[$t][] = $v;
                    } elseif($v || $v === '0') {
                        $output = (string) $v;
                    }
                }

                if($node->attributes->length && !is_array($output)) { //Has attributes but isn't an array
                    $output = array('@content'=>$output); //Change output into an array.
                }

                if(is_array($output)) {
                    if($node->attributes->length) {
                        $a = [];

                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }

                        $output['@attributes'] = $a;
                    }

                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }

        return $output;
    }
}
