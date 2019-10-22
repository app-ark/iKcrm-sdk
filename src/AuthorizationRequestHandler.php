<?php
namespace Ikcrm;

use function GuzzleHttp\json_decode;
use Symfony\Component\HttpKernel\Exception\HttpException;

class AuthorizationRequestHandler
{
    protected $device = 'open_api';
    protected $version_code = '9.9.9';
    protected $login;
    protected $password;
    protected $corp_id;
    protected $token;
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
        _retry:
        $request = parent::request($method, $uri, $options);
        $body = $request->getBody();
        $json = json_decode($body, true);
        if (!isset($json['code'])) {
            throw new HttpException(500, 'Response json with no code');
        }
        if (100000 == $json['code'] || 100401 == $json['code'] || 100400 == $json['code']) {
            $retry--;
            if ($retry < 0) {
                throw new HttpException(401, 'Auth fail after retried 3 times, and final caught:' . $json['code'] . ' - ' . $json['message']);
            }
            if (function_exists('cache')) {
                cache()->forget('IKCRM_TOKEN_' . $this->weiwenjia->authorization_handler->getLogin());
            }
            $this->weiwenjia->authorization_handler->_login();
            goto _retry;
        }
        if (0 != $json['code']) {
            throw new HttpException($json['code'], $json['message']);
        }

        return $json['data'];
    }
}
