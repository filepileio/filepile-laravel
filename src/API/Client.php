<?php

namespace FilePile\FilePileLaravel\API;

use FilePile\FilePileLaravel\API\Exceptions\InvalidKeyException;

class Client{

    private $client;

    public function call($method, $uri, $data = []){
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => config('filepile.baseURI'),
        ]);
        $apiKey = config('filepile.apiKey');
        if(empty($apiKey)){
            throw new InvalidKeyException('Please enter your FilePile API Key in .env');
        }
        $requestOptions = [
            'headers' => [
                'Authorization' => 'Bearer '.config('filepile.apiKey'),
                'Accept' => 'application/json',
            ],
        ];
        if($method == 'GET'){
            $requestOptions['query'] = $data;
        }else{
            $requestOptions['form_params'] = $data;
        }

        return $this->handleRequest($method, $uri, $requestOptions)->getContents();
    }

    private function handleRequest($method, $uri, $requestOptions){
        try {
            $request = $this->client->request($method, $uri, $requestOptions);
            return $request->getBody();
        } catch (\GuzzleHttp\Exception\ClientException $error){
            switch ($error->getResponse()->getStatusCode()) {
                case '401':
                    throw new InvalidKeyException('Unable to authenticate, please verify your FilePile API key');
                    break;
            }
        }
    }

}