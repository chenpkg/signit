<?php
/**
 * Created by Cestbon.
 * Author Cestbon <734245503@qq.com>
 * Date 2021/4/23 16:37
 */

namespace Signit\Kernel\Traits;

use Chenpkg\Support\Contracts\Arrayable;
use Psr\Http\Message\ResponseInterface;
use Signit\Kernel\Exceptions\InvalidConfigException;
use Signit\Kernel\Http\Response;

trait ResponseCastable
{
    protected function castResponseToType(ResponseInterface $response, $type = null)
    {
        $response = Response::buildFromPsrResponse($response);
        $response->getBody()->rewind();

        switch ($type ?? 'array') {
            case 'collection':
                return $response->toCollection();
            case 'array':
                return $response->toArray();
            case 'object':
                return $response->toObject();
            case 'raw':
                return $response;
            default:
                if (! is_subclass_of($type, Arrayable::class)) {
                    throw new InvalidConfigException(sprintf('Config key "response_type" classname must be an instanceof %s', Arrayable::class));
                }

                return new $type($response);
         }
    }
}