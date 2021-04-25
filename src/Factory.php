<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 14:03
 */

namespace Signit;

use Signit\Kernel\ServiceContainer;

/**
 * @property \Signit\Signature\Api\Authentications $auth
 * @property \Signit\Signature\Api\Envelopes       $envelopes
 */
class Factory extends ServiceContainer
{
    protected $providers = [
        Signature\Access\ServiceProvider::class,
        Signature\Api\ServiceProvider::class
    ];
}