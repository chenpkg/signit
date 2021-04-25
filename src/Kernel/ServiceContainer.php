<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 14:12
 */

namespace Signit\Kernel;

use Pimple\Container;
use Signit\Kernel\Providers\ConfigServiceProvider;
use Signit\Kernel\Providers\HttpClientServiceProvider;

/**
 * @property \Chenpkg\Support\Repository $config
 * @property \GuzzleHttp\Client          $http_client
 */
class ServiceContainer extends Container
{
    protected $providers = [];

    /**
     * @var array
     */
    protected $defaultConfig = [];

    /**
     * @var array
     */
    protected $userConfig = [];

    /**
     * Application constructor.
     * @param array $config
     * @param array $values
     */
    public function __construct(array $config, array $values = [])
    {
        $this->userConfig = $config;

        parent::__construct($values);

        $this->registerProviders($this->getProviders());
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return array_replace_recursive($this->defaultConfig, $this->userConfig);
    }

    /**
     * @return array
     */
    public function getProviders()
    {
        return array_merge([
            ConfigServiceProvider::class,
            HttpClientServiceProvider::class
        ], $this->providers);
    }

    /**
     * @param string $id
     * @param mixed  $value
     */
    public function rebind($id, $value)
    {
        $this->offsetUnset($id);
        $this->offsetSet($id, $value);
    }

    /**
     * Magic get access.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function __get($id)
    {
        return $this->offsetGet($id);
    }

    /**
     * @param $id
     * @return bool
     */
    public function __isset($id)
    {
        return $this->offsetExists($id);
    }

    /**
     * Magic set access.
     *
     * @param string $id
     * @param mixed  $value
     */
    public function __set($id, $value)
    {
        $this->offsetSet($id, $value);
    }

    /**
     * @param array $providers
     */
    public function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            parent::register(new $provider());
        }
    }
}