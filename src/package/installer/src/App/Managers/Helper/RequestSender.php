<?php
/** @noinspection PhpComposerExtensionStubsInspection */

namespace Gainhq\Installer\App\Managers\Helper;


use GuzzleHttp\Client;

class RequestSender
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get($url)
    {
        $request = $this->client->get($url, [
            'verify' => false,
            'curl' => [
                CURLOPT_RETURNTRANSFER => true
            ]
        ]);
        return json_decode($request->getBody()->getContents());
    }

    public function getUpdateFile($url, $file_path)
    {
        $request = $this->client->get($url, [
            'curl' => [
                CURLOPT_TIMEOUT => 50,
                CURLOPT_FILE => $file_path,
                CURLOPT_FOLLOWLOCATION => true
            ]
        ]);

        return json_decode($request->getBody()->getContents());
    }
}
