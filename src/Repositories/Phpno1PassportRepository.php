<?php
/**
 * Created by PhpStorm.
 * User: king
 * Date: 2018/2/20
 * Time: 下午1:22
 */

namespace Phpno1\Passport\Repositories;


use Laravel\Passport\Bridge\User;
use Laravel\Passport\Bridge\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use RuntimeException;

class Phpno1PassportRepository extends UserRepository
{
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $guard = request()->input('guard') ?? 'api';
        $provider = config("auth.guards.{$guard}.provider");

        if (is_null($model = config('auth.providers.'.$provider.'.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }

        if (method_exists($model, 'findForPassport')) {
            $user = (new $model)->findForPassport($username);
        } else {
            $user = (new $model)->where('email', $username)->first();
        }

        if (!$user) {
            return;
        } else if (method_exists($user, 'validateForPassportPasswordGrant')) {
            if (! $user->validateForPassportPasswordGrant($password)) {
                return;
            }
        } else if (! $this->hasher->check($password, $user->getAuthPassword())) {
            return;
        }

        auth()->guard($guard)->setUser($user);

        return new User($user->getAuthIdentifier());
    }
}