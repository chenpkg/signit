<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 16:54
 */

namespace Signit\Signature\Api;

use Signit\Signature\BaseClient;

class Authentications extends BaseClient
{
    /**
     * 手机三网认证
     *
     * @param $name
     * @param $idCardNo
     * @param $phone
     * @param $customTag
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function phone($name, $idCardNo, $phone, $customTag)
    {
        return $this->httpPostJson(
            '/authentications/phone-authentication/verify',
            compact('name', 'idCardNo', 'phone', 'customTag')
        );
    }

    /**
     * 银行卡四要素认证
     *
     * @param $name
     * @param $idCardNo
     * @param $phone
     * @param $bankCardNo
     * @param $customTag
     * @return array|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function bank($name, $idCardNo, $phone, $bankCardNo, $customTag)
    {
        return $this->httpPostJson(
            '/authentications/bank-authentication/verify',
            compact('name', 'idCardNo', 'phone', 'bankCardNo', 'customTag')
        );
    }
}