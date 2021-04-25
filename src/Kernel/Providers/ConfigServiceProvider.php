<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 14:11
 */

namespace Signit\Kernel\Providers;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Chenpkg\Support\Repository;

class ConfigServiceProvider implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        ! isset($pimple['config']) && $pimple['config'] = function ($app) {
            return new Repository($app->getConfig());
        };
    }
}