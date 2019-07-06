<?php
namespace Ikcrm;

use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;

class Ikcrm
{
    const API_DOMAIN = [
        'standalone' => 'https://api.ikcrm.com',
        'lixiao' => 'https://e.lixiaocrm.com',
        'dingtalk' => 'https://dingtalk.e.ikcrm.com',
        'lixiao_standalone' => 'https://lxcrm.weiwenjia.com',
    ];

    const API_LOGIN = '/api/v2/auth/login';

    /**
     * 域名
     *
     * @var string
     */
    public $domain = null;

    /**
     * Token
     *
     * @var string
     */
    protected $token = null;

    /**
     * Guzzle类
     *
     * @var HttpClient
     */
    protected $http = null;

    /**
     * @var array
     */
    public $login_data = null;

    /**
     * @var AuthorizationRequestHandler
     */
    public $authorization_handler;

    public function __construct($type = 'standalone')
    {
        $this->domain = static::API_DOMAIN[$type];

        $handler = new CurlHandler();
        $stack = HandlerStack::create($handler);
        $this->authorization_handler = new AuthorizationRequestHandler($this);
        $stack->push($this->authorization_handler);
        $this->http = new HttpClient(['base_uri' => $this->domain, 'handler' => $stack ], $this);
    }

    /**
     * 用户登录
     *
     * @param array $data
     * @return
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/user_login
     */
    public function login($login, $password, $corp_id)
    {
        $this->authorization_handler->login($login, $password, $corp_id);
        return $this;
    }

    /**
     * 用户信息
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/user_info
     */
    public function userInfo()
    {
        return $this->http->get('/api/v2/user/info');
    }

    /**
     * 用户列表
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/user_list
     */
    public function userList()
    {
        return $this->http->get('/api/v2/user/list');
    }

    /**
     * 用户简单列表
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/user_simple_list
     */
    public function userSimpleList()
    {
        return $this->http->get('/api/v2/user/simple_list');
    }

    /**
     * 部门列表
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/department_list
     */
    public function userDepartmentList()
    {
        return $this->http->get('/api/v2/user/department_list');
    }

    /**
     * 用户详情
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/user_detial
     */
    public function userDetail($user_id)
    {
        return $this->http->get('/api/v2/user/' . $user_id);
    }
}
