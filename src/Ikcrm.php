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

    /**
     * 客户列表
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_list
     */
    public function customers()
    {
        return $this->http->get('/api/v2/customers');
    }

    /**
     * 客户列表-根据名称查询
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_list_by_name
     */
    public function customersByName()
    {
        return $this->http->get('/api/v2/customers/by_name');
    }

    /**
     * 客户查询条件
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_filter
     */
    public function customersFilterSortGroup()
    {
        return $this->http->get('/api/v2/customers/filter_sort_group');
    }

    /**
     * 客户二级筛选列表
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_filter_item
     */
    public function customersFilterOptions($field_name)
    {
        return $this->http->get('/api/v2/customers/' . $field_name . '/filter_options');
    }

    /**
     * 客户查重
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_duplicates
     */
    public function duplicatesFieldSetting($entity_type)
    {
        return $this->http->get('/api/v2/duplicates/field_setting?entity_type=' . $entity_type);
    }

    /**
     * 查重搜索
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_duplicates
     */
    public function duplicatesSearch($entity_type)
    {
        return $this->http->get('/api/v2/duplicates/search');
    }

    /**
     * 新增客户
     *
     * @param array $customer
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_create
     */
    public function customerCreate($customer)
    {
        return $this->http->post('/api/v2/customers', [
            'form_params' => [
                'customer' => $customer,
            ],
        ]);
    }

    /**
     * 客户详情
     *
     * @param int $customer_id
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_detial
     */
    public function customerDetail($customer_id)
    {
        return $this->http->get('/api/v2/customers/' . $customer_id);
    }

    /**
     * 客户更新
     *
     * @param int $customer_id
     * @param array $customer
     * @param bool $check_duplicates
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_update
     */
    public function customerUpdate($customer_id, $customer, $check_duplicates = false)
    {
        return $this->http->post('/api/v2/customers/' . $customer_id, [
            'check_duplicates' => $check_duplicates,
            'form_params' => [
                'customer' => $customer,
            ],
        ]);
    }

    /**
     * 删除客户
     *
     * @param array $customer
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/customer_del
     */
    public function customerDelete($customer)
    {
        return $this->http->delete('/api/v2/customers');
    }

    /**
     * 线索列表查询
     *
     * @param array $parameters
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_list
     */
    public function leadList($parameters)
    {
        return $this->http->get('/api/v2/leads', [
            'query' => $parameters,
        ]);
    }

    /**
     * 线索列表-根据名称查询
     *
     * @param array $parameters
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/crm_open_api-1b1k6i9rk12db
     */
    public function leadListByName($parameters)
    {
        return $this->http->get('/api/v2/leads/by_name', [
            'query' => $parameters,
        ]);
    }

    /**
     * 线索筛选条件
     *
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_filter
     */
    public function leadFilterSortGroup($parameters)
    {
        return $this->http->get('/api/v2/leads/filter_sort_group');
    }

    /**
     * 线索二级筛选列表
     *
     * @param string $field_name
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_item_filter
     */
    public function leadFilterOptions($field_name)
    {
        return $this->http->get('/api/v2/leads/' . $field_name . '/filter_options');
    }

    /**
     * 线索详情
     *
     * @param string $lead_id
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_detail
     */
    public function leadDetail($lead_id)
    {
        return $this->http->get('/api/v2/leads/' . $lead_id);
    }

    /**
     * 创建线索
     *
     * @param array $lead
     * @param bool $check_duplicates
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_create
     */
    public function leadCreate($lead, $check_duplicates = false)
    {
        return $this->http->post('/api/v2/leads', [
            'form_params' => [
                'lead' => $lead,
            ],
        ]);
    }

    /**
     * 更新线索
     *
     * @param int $lead_id
     * @param array $lead
     * @param bool $check_duplicates
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/crm_open_api-1b1ka2r0aqi45
     */
    public function leadUpdate($lead_id, $lead, $check_duplicates = false)
    {
        return $this->http->post('/api/v2/leads/' . $lead_id, [
            'form_params' => [
                'lead' => $lead,
            ],
        ]);
    }

    /**
     * 线索转移
     *
     * @param int $lead_id
     * @param int $user_id
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_trans 文档有错误 靠
     */
    public function leadTransfer($lead_id, $user_id)
    {
        return $this->http->post('/api/v2/leads/' . $lead_id . '/update_user', [
            'form_params' => [
                'user_id' => $user_id,
            ],
        ]);
    }

    /**
     * 线索删除
     *
     * @param string $lead_id
     * @return array
     * @link http://apidoc.weiwenjia.com/docs/crm_open_api/lead_detail
     */
    public function leadDelete($lead_id)
    {
        return $this->http->delete('/api/v2/leads/' . $lead_id);
    }
}
