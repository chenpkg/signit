<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 17:00
 */

namespace Signit\Signature\Api;

use Pimple\Container;
use Pimple\ServiceProviderInterface;

class ServiceProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['auth'] = function ($app) {
            return new Authentications($app);
        };

        $app['envelopes'] = function ($app) {
            return new Envelopes($app);
        };
    }
}