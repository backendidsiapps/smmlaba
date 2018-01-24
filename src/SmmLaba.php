<?php

namespace SmmLaba;

use App\Util\ServicesList;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use function config;

/**
 * Class SmmLaba
 * @package App\Util
 */
class SmmLaba
{
    /**
     * @var
     */
    private $guzzle;
    /**
     * @var mixed
     */
    private $username;
    /**
     * @var mixed
     */
    private $apikey;
    /**
     * @var mixed
     */
    private $apiURL;

    /**
     * SmmLaba constructor.
     */
    public function __construct()
    {
        $this->username = config('smmlaba.username');
        $this->apikey = config('smmlaba.key');
        $this->apiURL = config('smmlaba.url');
        $this->init();
    }

    /**
     * init guzzle client
     *
     * @return void
     */
    private function init()
    {
        $this->guzzle = new Client([
            'base_uri' => $this->apiURL,
            'timeout'  => 5.0,
            'verify'   => false,
        ]);
        // $this->guzzle->setDefaultOption('headers', ['Useragent' => 'Curl/Api']);
    }

    /**
     * @param $service
     * @param $quantity
     * @param $url
     * @return object
     */
    public function add(string $service, int $quantity, string $url)
    {
        $response = $this->guzzle->post(null, [
            'query' => [
                'action'   => 'add',
                'username' => $this->username,
                'apikey'   => $this->apikey,
                'service'  => $service,
                'url'      => $url,
                'count'    => $quantity,
            ],
        ]);

        $result = $this->makeObjectFromResponse($response);

//        Log::info(json_encode($result));
        return $result;
    }

    /**
     * @param int $smmlabaOrderID
     * @return object
     */
    public function checkStatus(int $smmlabaOrderID)
    {
        $response = $this->guzzle->post(null, [
            'query' => [
                'action'   => 'check',
                'username' => $this->username,
                'apikey'   => $this->apikey,
                'orderid'  => $smmlabaOrderID,
            ],
        ]);

        $result = $this->makeObjectFromResponse($response);

        if ($result->error != '') {
            Log::critical('can\'t get order info, ' . Auth::id() ?? 'no auth' . ' resp:  ' . json_encode($result));
        }

        return $result;
    }

    /**
     * @param $response
     * @return object
     */
    private function makeObjectFromResponse($response)
    {
        return (object)json_decode($response->getBody()->getContents());
    }


    /**
     * @return int
     */
    public function getBalance(): int
    {
        $response = $this->guzzle->post(null, [
            'query' => [
                'action'   => 'balance',
                'username' => $this->username,
                'apikey'   => $this->apikey,
            ],
        ]);

        $response = (object)json_decode($response->getBody()->getContents());
        if ($response->error != '') {
            Log::critical('error on balance request: ' . $response->error);

            return -1;
        }

        return $response->message->balance;
    }
}
