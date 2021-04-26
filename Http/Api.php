<?php

namespace Http;

use GuzzleHttp\Client;
use Throwable;

class Api
{
    const GUZZLE_CONCURRENCY = 5;

    public $before = [
        'are',
        'can',
        'how',
        'what',
        'when',
        'where',
        'which',
        'who',
        'why',
        'will',
        'and',
        'like',
        'or',
        'versus',
        'vs'
    ];

    public $after = [
        'can',
        'for',
        'is',
        'near',
        'to',
        'with',
        'without'
    ];

    private $api = 'https://www.api.okomaps.com/getdata.php?q=';

    public $beforeApi = [];
    public $finalApi = [];

    public function __construct(string $keyword = '', $country = 'us', $language = 'en')
    {
        $this->keyword  = $keyword;
        $this->country  = $country;
        $this->language = $language;
        /** Merge all alphabetical */
        $this->after    = array_merge($this->after, range('a', 'z'));
    }

    /**
     * Add word before keyword
     * 
     */
    public function handleBefore()
    {
        foreach ($this->before as $before) {
            $this->beforeApi[$before] = $before . ' ' . $this->keyword;
        }

        return $this;
    }

    /**
     * Add word after keyword
     * 
     */
    public function handleAfter()
    {
        foreach ($this->beforeApi as $bkey => $bvalue) {

            foreach ($this->after as $after) {
                $this->finalApi[$bkey][] = $this->api . urlencode($bvalue . ' ' . $after) . '&gl=' . $this->country . '&hl=' . $this->language;
            }
        }

        return $this;
    }

    /**
     * Merge before and after API
     * 
     */
    public function mergeApi()
    {
        $this->handleBefore()->handleAfter();

        return $this;
    }

    /**
     * Make API call
     * 
     */
    public function handleApi($keyword = 'can')
    {
        $this->mergeApi();

        $client         = new Client();
        $responseXml    = null;
        $rawArray       = [];

        foreach ($this->finalApi[$keyword] as $url) {

            $request = $client->request('GET', $url, [
                'headers' => ['Accept' => 'application/xml']
            ]);
            $responseXml = simplexml_load_string($request->getBody()->getContents());
            $json = json_encode($responseXml);
            $json = json_decode($json, TRUE);
            if (isset($json['CompleteSuggestion'])) {
                foreach ($json['CompleteSuggestion'] as $array) {
                    array_push($rawArray, $array);
                }
            }
        }

        header('Content-Type: application/json');
        echo json_encode($rawArray);
        return;
    }

    /**
     * Getter
     * 
     */
    public function __get($property)
    {
        if (property_exists($this, $property)) {
            return $this->$property;
        }
    }
}
