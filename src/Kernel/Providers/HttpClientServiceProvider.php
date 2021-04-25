<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 16:07
 */

namespace Signit\Kernel\Providers;

use GuzzleHttp\Client;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class HttpClientServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        !isset($pimple['http_client']) && $pimple['http_client'] = function ($app) {
            return new Client($app['config']->get('http', []));
        };
    }
}