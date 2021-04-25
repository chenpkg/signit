<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 16:57
 */

namespace Signit\Signature\Api;

use Signit\Signature\BaseClient;

class Envelopes extends BaseClient
{
    /**
     * 发起快捷签署
     *
     * @param array $data
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function quick(array $data)
    {
        return $this->httpPostJson('/signatures/quick-sign', $data);
    }

    /**
     * 发起签署流程
     *
     * @param array $data
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function start(array $data)
    {
        return $this->httpPostJson('/envelopes/start', $data);
    }
}