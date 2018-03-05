# phpno1-passport

## 官方qq群
    qq:680531281

## 使用要求

#### laravel版本 >= 5.5

#### composer 安装
执行以下命令获取包的最新版本:

```php
    composer require phpno1/passport
```

## 使用方式

#### 注册到服务容器

说明：用扩展中的provider替换laravel官方的passport的provider

```php
    # 在config/app.php中
    'providers' => [
        // Laravel\Passport\PassportServiceProvider::class,
        Phpno1\Passport\Providers\Phpno1PassportServiceProvider::class, // 代替原生的passport provider
    ];
```

#### 使用trait

```php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Phpno1\Passport\Traits\TokenAuthenticatesUsers;

class AdminLoginController extends Controller
{
    use TokenAuthenticatesUsers; // 使用扩展提供的trait

    protected $maxAttempts = 5; // 允许尝试次数

    protected $decayMinutes = 60; // 超过尝试次数后冻结多少分钟

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function guard()
    {
        return 'api_admin'; // 配置文件中的自定义guard，如果是默认的api，则该方法无需重写
    }
    
    // 根据自己生成的数据来配置这些值。
    protected function authorization()
    {
        return [
            'grant_type'    => 'password',
            'client_id'     => 2,
            'client_secret' => 'hNrOGyPZlbqKYuuLgs1JMizaRd78iWbq7Lsk1AHc',
            'scope'         => 'client-backend',
        ];
    }

}
```
