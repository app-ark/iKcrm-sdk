<?php
namespace Ikcrm;

use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;
use Symfony\Component\HttpKernel\Exception\HttpException;

class HttpClient extends Client
{
    /**
     * @var Weiwenjia
     */
    protected $weiwenjia = null;

    public function __construct(array $config = [], Ikcrm $weiwenjia)
    {
        parent::__construct($config);
        $this->weiwenjia = $weiwenjia;
    }

    public function request($method, $uri = '', array $options = [], $retry = 3)
    {
        $request = parent::request($method, $uri, $options);
        $body = $request->getBody();
        $json = json_decode($body, true);
        if (!isset($json['code'])) {
            throw new HttpException(500, 'Response json with no code');
        }
        if (0 != $json['code']) {
            throw new HttpException($json['code'], $json['message']);
        }

        return $json['data'];
    }
}
