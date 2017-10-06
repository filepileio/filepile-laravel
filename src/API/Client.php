<?php

namespace FilePile\FilePileLaravel\API;

class Client{

    private $client;

    public function call($method, $uri, $data = []){
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => config('filepile.baseURI'),
        ]);
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
                    throw new \Exception('Ops, look like your key is invalid.');
                    break;
            }
        }
    }

}