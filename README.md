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

#### 发送请求

+ 设置head头

```
Accept ：application/json
Content-Typ ：application/json
```

+ 设置body参数,并使用POST方式提交到控制器的登入接口

```
{"username": "king19800105", "password": "secret"}
```

+ 响应内容

```
{
    "token_type":"Bearer",
    "expires_in":2591995,
    "access_token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6IjdkYjdmYWZhNWIzODYwMTUyMWMxMzdjNDk0YWUzMzA1ODFhMjUyMmIwYjBmMDkzYzRjMjdmZGEzNzdjMGQ5OTkzMzQ5ZjExMWUxOTc0NTA1In0.eyJhdWQiOiIxIiwianRpIjoiN2RiN2ZhZmE1YjM4NjAxNTIxYzEzN2M0OTRhZTMzMDU4MWEyNTIyYjBiMGYwOTNjNGMyN2ZkYTM3N2MwZDk5OTMzNDlmMTExZTE5NzQ1MDUiLCJpYXQiOjE1MjAyMzg5ODcsIm5iZiI6MTUyMDIzODk4NywiZXhwIjoxNTIyODMwOTgyLCJzdWIiOiIxIiwic2NvcGVzIjpbImNsaWVudC1iYWNrZW5kIl19.OQ8qHpXSvgAylk9w_uNSszVots5mv9phuMk0Py929muvhrTUOVERlupyMqKKH6bvRFYP80ltUe7K6MsrDdddFl60zxcFmuXkObbiNYPL2WrZIctJAQiPPceLgRZCBfQGHLgL4DJ3ZJrdL64OfSnP5luAy8akoU5wAj6N2fEUUqHoktqU96TFmlHiNliSnvhd8RwXTLGoXISqOP_s385wu6N4RjAmRtR1lCIRi-FfNi0Q9Xma5bX5-Z4oxmT9OuG9zQbj5TtY-VVDC2JfjGtqu9Wvp2agI57I9caRJbbHHdNMP7-6VawxT2H08k37VLK5zUfzhju7eBJMux1b6-drcMKxi0OE4b6wbs48KS4tz9Z_gJ4QhbULHoQLKuaQS7uX-LFdUYDpEkCjD5QJK-AplecPT-oSw-TnME_VpaUIJ-PiCBlJgU5eNQv8IcV-qbjK2H1OUILRjJ8J_hiM2P7qBKxbYU6fLhs6vCqsjMnR7Z2fJPM2JuBQMBh-k5u8TBA80nJJx0i1u5KU_GWVbSrdP2Ty17LrYROdJwxBesdzguZWJRGBD769NersxIXFpgCsOlFgdMu-q8LhUllnaP5dTimuzPWkA49ZcpQO2cGR7ia7mL3hsRKbdK5rGUG8BxVx--iuboPf9L2yDBAQBEVViHAmnLVpFBhDv6WwcugaFx0",
    "refresh_token":"def50200b632df0c9150a0d00918a4943546eb26ed33afc6211ae43d7dc65532c3e8fd5959849d2b7bef71dc3a74c3f3eb04bbc1c8bdd039bb994e594c7849b69baa69479dbd306e0b495ac6303183125a26a5a7322123ffc403f9e1b8e6b30fcd3c4180a1669192f0e8accf260f694e0581ec9bc8a025730c704823eb3ac5ba576b938510178a7e76396303a8db7cfcdf1aedf080d16c8217a90d7bbe7ba3f11fafa7e3071f6021fc09b797f1ffcb6668c96139e14fc631cea7fbcfb2c1122e2e516010613b586fc543aed7ed4208ea5896d66d2d52f78526a191d954960d4069789bbf81cefecc0b4151d5e94ec80cb8f4f54c797a89d57caa6ca6b8c33a0b2d948f6bc0a581108ba76fd0adf0305c26a7ad99d81aa940b4ed1674e09ba109081a6e5a27b29474935a81d9e73decb05f003282bad7753679ca8f7beb7811c7ab82e2af85fe236e51f84120cbdf7f75780d956437dd0c66c53554286af0adadd4258ff9a114bbad281ac45319bf92db8f"
}
```


