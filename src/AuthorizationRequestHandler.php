<?php
namespace Ikcrm;

use Carbon\Carbon;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Client;
use function GuzzleHttp\json_decode;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Psr\Http\Message\ResponseInterface;

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
    protected $weiwenjia;

    public function __construct(Ikcrm $weiwenjia, $login = null, $password = null, $corp_id = null)
    {
        $this->weiwenjia = $weiwenjia;

        $this->login = $login;
        $this->password = $password;
        $this->corp_id = $this->corp_id;
    }

    public function login($login = null, $password = null, $corp_id = null)
    {
        if ($login) {
            $this->login = $login;
        }
        if ($password) {
            $this->password = $password;
        }
        if ($corp_id) {
            $this->corp_id = $this->corp_id;
        }

        $data  = [
            'device' => $this->device,
            'version_code' => $this->version_code,
            'login' => $this->login,
            'password' => $this->password,
            'corp_id' => $this->corp_id,
        ];
        $client = new Client(['base_uri' => $this->weiwenjia->domain,]);
        $login = $client->post(Ikcrm::API_LOGIN, ['form_params' => $data,]);
        $json = json_decode($login->getBody(), true);
        if (0 != $json['code']) {
            throw new HttpException(401, $json['message'], null, [], $json['code']);
        }

        $this->token = $json['data']['user_token'];
    }

    /**
     * 触发
     *
     * @param \callable $handler
     * @return \callable
     */
    public function __invoke($handler, $retry = 3)
    {
        return function (RequestInterface $request, array $options) use ($handler, $retry) {
            $headerValue = 'Token token=%s, device=%s, version_code=%s';
            $request = $request->withAddedHeader(
                'Authorization',
                sprintf($headerValue, $this->token, $this->device, $this->version_code)
            );
            /**
             * @var \GuzzleHttp\Promise\FulfilledPromise $response
             */
            $response = $handler($request, $options);
            return $response->then(function (ResponseInterface $response) use ($handler, $retry) {
                $body = $response->getBody();
                $json = json_decode($body, true);
                if (!isset($json['code'])) {
                    throw new HttpException(500, 'Response json with no code');
                }
                if (100000 == $json['code'] || 100401 == $json['code'] || 100400 == $json['code']) {
                    $retry--;
                    if ($retry < 0) {
                        throw new HttpException(401, 'Auth fail after retried 3 times');
                    }
                    $this->login();
                    return $this->__invoke($handler, $retry);
                }
                return $response;
            });
        };
    }
}