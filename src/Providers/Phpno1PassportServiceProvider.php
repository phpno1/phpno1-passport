<?php

namespace Phpno1\Passport\Providers;

use Laravel\Passport\Bridge\RefreshTokenRepository;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use League\OAuth2\Server\Grant\PasswordGrant;
use Phpno1\Passport\Repositories\Phpno1PassportRepository;

class Phpno1PassportServiceProvider extends PassportServiceProvider
{
    protected function makePasswordGrant()
    {
        $grant = new PasswordGrant(
            $this->app->make(Phpno1PassportRepository::class),
            $this->app->make(RefreshTokenRepository::class)
        );

        $grant->setRefreshTokenTTL(Passport::refreshTokensExpireIn());

        return $grant;
    }
}
