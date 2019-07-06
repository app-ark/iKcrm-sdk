# 𝒊𝑲𝒄𝒓𝒎 𝑳𝒂𝒓𝒂𝒗𝒆𝒍 𝑺𝑫𝑲

ikcrm 的 laravel SDK

## 安装

```bash
composer require ikcrm/sdk
```
## 使用

```php
<?php
use Ikcrm\Ikcrm;

require_once 'vendor/autoload.php';
$ikcrm = new Ikcrm();
$ikcrm->login('用户名', '密码', '企业CORP_ID');
dd($ikcrm->userInfo());
```

## 授权

MIT