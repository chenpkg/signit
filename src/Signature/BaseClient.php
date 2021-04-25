<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 14:29
 */

namespace Signit\Signature;

use Chenpkg\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Signit\Kernel\ServiceContainer;
use Signit\Kernel\Traits\HasHttpRequests;
use Signit\Signature\Access\AccessToken;

class BaseClient
{
    use HasHttpRequests { request as performRequest; }

    protected $baseUri = 'https://open.signit.cn/v1/open';

    /**
     * @var ServiceContainer
     */
    protected $app;

    /**
     * @var AccessToken
     */
    protected $accessToken;

    public function __construct(ServiceContainer $app)
    {
        $this->app = $app;

        $this->accessToken = $this->app['access_token'];

        if ($url = $this->app->config->get('base_url')) {
            $this->baseUri = $url;
        }
    }

    public function request($url, $method = 'GET', $options = [], $returnRaw = false)
    {
        if (empty($this->middlewares)) {
            $this->registerHttpMiddlewares();
        }

        $response = $this->performRequest($url, $method, $options);

        return $returnRaw ? $response : $this->castResponseToType($response, $this->app->config->get('response_type'));
    }

    protected function registerHttpMiddlewares()
    {
        $this->pushMiddleware($this->accessTokenMiddleware(), 'access_token');

        $this->pushMiddleware($this->headerSignitMiddleware(), 'signit_header');
    }

    /**
     * @return \Closure
     */
    protected function accessTokenMiddleware()
    {
        return function (callable $handle) {
            return function (RequestInterface $request, array $options) use ($handle) {
                if ($this->accessToken) {
                    $request = $this->accessToken->applyToRequest($request, $options);
                }

                return $handle($request, $options);
            };
        };
    }

    /**
     * @return \Closure
     */
    protected function headerSignitMiddleware()
    {
        return function (callable $handle) {
            return function (RequestInterface $request, array $options) use ($handle) {
                $request = $request->withHeader('X-Signit-App-Id', $this->app->config->get('client_id'));
                return $handle($request, $options);
            };
        };
    }

    /**
     * GET request
     *
     * @param string $url
     * @param null   $query
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Cestbon <734245503@qq.com>
     * @date   2020/10/7 19:28
     */
    public function httpGet(string $url, $query = null)
    {
        return $this->request($url, 'GET', [
            'query' => $query
        ]);
    }

    /**
     * POST request
     *
     * @param string $url
     * @param array  $data
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Cestbon <734245503@qq.com>
     * @date   2020/10/7 19:28
     */
    public function httpPost(string $url, array $data = [])
    {
        return $this->request($url, 'POST', [
            'form_params' => $data
        ]);
    }

    /**
     * JSON request
     *
     * @param string $url
     * @param array  $data
     * @param array  $query
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @author Cestbon <734245503@qq.com>
     * @date   2020/10/7 19:28
     */
    public function httpPostJson(string $url, array $data = [], array $query = [])
    {
        return $this->request($url, 'POST', [
            'query' => $query,
            'json'  => $data
        ]);
    }
}