<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 15:36
 */

namespace Signit\Kernel\Traits;

use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface as SimpleCacheInterface;
use Signit\Kernel\ServiceContainer;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Signit\Kernel\Exceptions\InvalidArgumentException;

trait InteractsWithCache
{
    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    protected $cache;

    public function getCache()
    {
        if ($this->cache) {
            return $this->cache;
        }

        if (property_exists($this, 'app') && $this->app instanceof ServiceContainer && isset($this->app['cache'])) {
            $this->setCache($this->app['cache']);

            // Fix PHPStan error
            assert($this->cache instanceof \Psr\SimpleCache\CacheInterface);

            return $this->cache;
        }

        return $this->cache = $this->createDefaultCache();
    }

    public function setCache($cache)
    {
        if (empty(\array_intersect([SimpleCacheInterface::class, CacheItemPoolInterface::class], \class_implements($cache)))) {
            throw new InvalidArgumentException(\sprintf('The cache instance must implements %s or %s interface.', SimpleCacheInterface::class, CacheItemPoolInterface::class));
        }

        if ($cache instanceof CacheItemPoolInterface) {
            if (!$this->isSymfony43OrHigher()) {
                throw new InvalidArgumentException(sprintf('The cache instance must implements %s', SimpleCacheInterface::class));
            }
            $cache = new Psr16Cache($cache);
        }

        $this->cache = $cache;

        return $this;
    }

    /**
     * @return \Psr\SimpleCache\CacheInterface
     */
    protected function createDefaultCache()
    {
        if ($this->isSymfony43OrHigher()) {
            return new Psr16Cache(new FilesystemAdapter('signit', 1500));
        }

        return new FilesystemCache();
    }

    protected function isSymfony43OrHigher(): bool
    {
        return \class_exists('Symfony\Component\Cache\Psr16Cache');
    }
}