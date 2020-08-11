<?php

namespace App\Helpers;


use Illuminate\Support\Facades\Http;

class FoursquareAPI {
    private $clientId;
    private $clientSecret;
    private $apiUrl;
    public $endpoint;
    public $query;
    public $params;
    public $coordinates;

    public function __construct($clientId = null, $clientSecret = null)
    {
        $this->apiUrl = 'https://api.foursquare.com/v2/';
        $this->clientId = $clientId == null ? env('FOURSQUARE_CLIENT_ID') : $clientId;
        $this->clientSecret = $clientSecret == null ? env('FOURSQUARE_CLIENT_SECRET') : $clientSecret;
    }

    public function endpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    public function query($query)
    {
        $this->query = $query;
        return $this;
    }

    public function params($params)
    {
        $this->params = $params;
        return $this;
    }

    public function coordinates($coordinates)
    {
        $this->coordinates = $coordinates;
        return $this;
    }

    public function get()
    {
        if($this->clientId == null || $this->clientSecret == null){
            throw new \Exception('Foursquare client_id/client_secret missing.');
        }

        if($this->endpoint == null){
            throw new \Exception('Endpoint missing.');
        }

        if($this->query == null){
            throw new \Exception('Query is empty.');
        }


        $request = [
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'query' => $this->query,
            'v' => '20180323'
        ];

        if($this->coordinates == null){
            $request['near'] = 'IST'; //IP'ye göre lokasyon kontrolü yapılabilir.
        }else{
            $request['ll'] = $this->coordinates;
        }


        if($this->params != null){
            if(!is_array($this->params)){
                throw new \Exception('Parameters value is not array.');
            }

            $request = array_merge($request,$this->params);
        }

        $response = Http::get($this->apiUrl.$this->endpoint, $request)->object();

        return $response;
    }
}