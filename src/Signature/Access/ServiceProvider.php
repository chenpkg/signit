<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 15:27
 */

namespace Signit\Signature\Access;

use Pimple\Container;
use \Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        !isset($app['access_token']) && $app['access_token'] = function ($app) {
            return new AccessToken($app);
        };
    }
}