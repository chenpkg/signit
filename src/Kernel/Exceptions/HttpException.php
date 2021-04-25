<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 16:13
 */

namespace Signit\Kernel\Exceptions;

use Psr\Http\Message\ResponseInterface;

class HttpException extends \Exception
{
    /**
     * @var \Psr\Http\Message\ResponseInterface|null
     */
    public $response;

    /**
     * @var \Psr\Http\Message\ResponseInterface|\EasyWeChat\Kernel\Support\Collection|array|object|string|null
     */
    public $formattedResponse;

    /**
     * HttpException constructor.
     *
     * @param string   $message
     * @param int|null $code
     */
    public function __construct($message, ResponseInterface $response = null, $code = null)
    {
        parent::__construct($message, $code);

        $this->response = $response;

        if ($response) {
            $response->getBody()->rewind();
        }
    }
}