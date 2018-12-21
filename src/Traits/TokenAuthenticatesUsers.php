<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/2/20
 * Time: 下午12:08
 */

namespace Phpno1\Passport\Traits;

use Phpno1\Passport\Exceptions\GuardNotFindException;
use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * 在没有session的情况下进行用户校验
 *
 * Class TokenAuthenticatesUsers
 * @package App\Traits
 */
trait TokenAuthenticatesUsers
{
    use ThrottlesLogins;

    /**
     * 登入操作
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response|void
     */
    public function login()
    {
        $request = request();
        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            try {
                return $this->sendLoginResponse($request);
            } catch (UnauthorizedHttpException $e) {
                $this->responseLoginFail($request);
                return;
            }
        }

        $this->responseLoginFail($request);
    }

    /**
     * 登出操作
     */
    public function logout()
    {
        $user = auth()->guard($this->guard())->user();

        if ($user) {
            $user->token()->revoke();
        }

        session()->flush();
        session()->regenerate();

        return true;
    }

    /**
     * 当前的guard
     *
     * @return string
     */
    public function guard()
    {
        return 'api';
    }

    /**
     * 授权的url
     *
     * @return string
     */
    public function api()
    {
        return 'oauth/token';
    }

    /**
     * 授权请求方式
     *
     * @return string
     */
    public function method()
    {
        return 'POST';
    }

    /**
     * 登入时传递的字段
     *
     * @return string
     */
    public function username()
    {
        return 'username';
    }

    /**
     * 登入验证
     *
     * @param Request $request
     * @return mixed
     */
    protected function validateLogin(Request $request)
    {
        return $this->validate($request, [
            $this->username() => 'required|string|between:5,255',
            'password' => 'required|min:6|alpha_dash',
        ]);
    }

    /**
     * 获取guard绑定的模型
     *
     * @return mixed
     */
    protected function getModelByGuard()
    {
        $provider = config('auth.guards.' . $this->guard() . '.provider');

        throw_if(
            !$provider,
            new GuardNotFindException()
        );

        $model = config('auth.providers.' . $provider . '.model');

        throw_if(
            !$model,
            (new ModelNotFoundException())->setModel($model)
        );

        return app()->make($model);
    }

    /**
     * 登入失败的响应
     *
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse()
    {
        throw ValidationException::withMessages([
            $this->username() => [__('auth.failed')],
        ]);
    }

    /**
     * 登入成功后的响应
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->merge(array_merge($this->authorization(), ['guard' => $this->guard()]));
        $response = $this->forward($request);
        $user = auth()->guard($this->guard())->user();

        throw_if(
            200 !== $response->getStatusCode() || !$user,
            new UnauthorizedHttpException('', 'Unauthorized')
        );

        $this->authenticated($request, $user);

        return $response->getContent();
    }

    protected function forward(Request $request)
    {
        $api = $this->api();

        if (starts_with($api, 'http://') || starts_with($api, 'https://')) {
            $client = new Client();

            return $client->request($this->method(), $api, $request->input());
        }

        $proxy = $request->create($this->api(), $this->method());

        return Route::dispatch($proxy);
    }

    /**
     * 尝试登入
     *
     * @param Request $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        return $this->attempt($request) ? true : false;
    }

    /**
     * 尝试访问数据
     *
     * @param Request $request
     * @return mixed
     */
    protected function attempt(Request $request)
    {
        $loginModel = $this->getModelByGuard();

        return method_exists($loginModel, 'findForPassport')
            ? $loginModel->findForPassport($request->username)
            : $loginModel->where('email', $request->username)->first();
    }

    /**
     * 传递的授权参数
     *
     * @return array
     */
    protected function authorization()
    {
        return [
            'grant_type'    => 'password',
            'client_id'     => 1,
            'client_secret' => 'your-client-secret',
            'scope'         => '*'
        ];
    }

    /**
     * 授权成功后的处理
     *
     * @param Request $request
     * @param $user
     */
    protected function authenticated(Request $request, $user)
    {

    }

    /**
     * 登入失败的处理
     *
     * @throws ValidationException
     */
    protected function responseLoginFail($request)
    {
        $this->incrementLoginAttempts($request);
        $this->sendFailedLoginResponse();
    }

}