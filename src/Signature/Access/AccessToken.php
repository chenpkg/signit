<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 15:21
 */

namespace Signit\Signature\Access;

use Psr\Http\Message\RequestInterface;
use Signit\Kernel\Exceptions\HttpException;
use Signit\Kernel\Exceptions\RuntimeException;
use Signit\Kernel\ServiceContainer;
use Signit\Kernel\Traits\HasHttpRequests;
use Signit\Kernel\Traits\InteractsWithCache;

class AccessToken
{
    use HasHttpRequests, InteractsWithCache;

    protected $app;

    /**
     * token 键名
     *
     * @var string
     */
    protected $tokenKey = 'access_token';

    /**
     * 授权类型
     *
     * @var string
     */
    protected $grantType = 'client_credentials';

    /**
     * 缓存前缀
     *
     * @var string
     */
    protected $cachePrefix = 'signit.access_token.';

    /**
     * token 请求 url
     *
     * @var string
     */
    protected $endpointToGetToken = 'https://open.signit.cn/v1/oauth/oauth/token';

    /**
     * AccessToken constructor.
     * @param ServiceContainer $app
     */
    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;
    }

    /**
     * @param bool $refresh
     * @return array
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getToken(bool $refresh = false): array
    {
        $cacheKey = $this->getCacheKey();
        $cache = $this->getCache();

        if (!$refresh && $cache->has($cacheKey) && $result = $cache->get($cacheKey)) {
            return $result;
        }

        $token = $this->requestToken($this->getCredentials());

        $this->setToken($token[$this->tokenKey], $token['expires_in'] ?? 7200);

        return $token;
    }

    /**
     * @param     $token
     * @param int $lifetime
     * @return $this
     * @throws RuntimeException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function setToken($token, int $lifetime = 7200)
    {
        $this->getCache()->set($this->getCacheKey(), [
            $this->tokenKey => $token,
            'expires_in'    => $lifetime
        ], $lifetime);

        if (!$this->getCache()->has($this->getCacheKey())) {
            throw new RuntimeException('Failed to cache access token.');
        }

        return $this;
    }

    /**
     * 刷新 token
     *
     * @return $this
     * @throws RuntimeException
     * @author Cestbon <734245503@qq.com>
     * @date   2020/10/7 11:42
     */
    public function refresh()
    {
        $this->getToken(true);

        return $this;
    }

    /**
     * @param array $credentials
     * @return array
     * @throws HttpException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestToken(array $credentials): array
    {
        $response = $this->setHttpClient($this->app['http_client'])
            ->request(
                $this->endpointToGetToken,
                'GET',
                ['query' => $credentials]
            );

        $result = json_decode($response->getBody()->getContents(), true);

        if (empty($result[$this->tokenKey])) {
            throw new HttpException('Request access_token fail: '.json_encode($result, JSON_UNESCAPED_UNICODE), $response);
        }

        return $result;
    }

    /**
     * 将 token 添加至请求 url
     *
     * @param RequestInterface $request
     * @param array            $requestOptions
     * @return RequestInterface
     * @throws RuntimeException
     */
    public function applyToRequest(RequestInterface $request, array $requestOptions = []): RequestInterface
    {
        parse_str($request->getUri()->getQuery(), $query);

        $query = http_build_query(array_merge($this->getQuery(), $query));

        return $request->withUri($request->getUri()->withQuery($query));
    }

    /**
     * 获取需要添加到请求中的 token 数据
     *
     * @return array
     * @throws RuntimeException
     * @author Cestbon <734245503@qq.com>
     * @date   2020/10/7 11:29
     */
    protected function getQuery()
    {
        return [$this->queryName ?? $this->tokenKey => $this->getToken()[$this->tokenKey]];
    }


    /**
     * @return string
     */
    protected function getCacheKey()
    {
        return $this->cachePrefix.md5(json_encode($this->getCredentials()));
    }

    /**
     * 请求 token 所需数据
     *
     * @return array
     * @author Cestbon <734245503@qq.com>
     * @date   2020/10/7 9:02
     */
    protected function getCredentials(): array
    {
        return [
            'grant_type'    => $this->grantType,
            'client_id'     => $this->app['config']->get('client_id'),
            'client_secret' => $this->app['config']->get('client_secret'),
        ];
    }
}