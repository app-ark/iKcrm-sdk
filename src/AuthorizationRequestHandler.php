<?php
namespace Weiwenjia;

use Carbon\Carbon;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\Client;
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
    protected $weiwenjia;

    public function __construct(Weiwenjia $weiwenjia, $login = null, $password = null, $corp_id = null)
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
        $login = $client->post(Weiwenjia::API_LOGIN, ['form_params' => $data,]);
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
    public function __invoke($handler)
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $headerValue = 'Token token=%s, device=%s, version_code=%s';
            $request = $request->withAddedHeader(
                'Authorization',
                sprintf($headerValue, $this->token, $this->device, $this->version_code)
            );
            return $handler($request, $options);
        };
    }
}