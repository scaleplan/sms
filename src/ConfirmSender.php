<?php

namespace App\Services;

use function Scaleplan\Helpers\get_env;
use Scaleplan\Sms\Exceptions\SmsException;
use Scaleplan\Sms\Interfaces\SmsInterface;
use function Scaleplan\Translator\translate;

/**
 * Class ConfirmSender
 *
 * @package App\Services
 */
class ConfirmSender
{
    public const KEY_LENGTH = 16;

    public const DEFAULT_CODE_TTL = 3600;

    /**
     * @var \Redis
     */
    protected $cache;

    /**
     * @var SmsInterface
     */
    protected $sms;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * ConfirmSender constructor.
     *
     * @param SmsInterface $sms
     * @param \Redis $cache
     *
     * @throws \Exception
     */
    public function __construct(
        SmsInterface $sms,
        \Redis $cache
    ) {
        $this->cache = $cache;
        $this->sms = $sms;
        $this->ttl = (int)(get_env('SMS_CODE_TTL') ?? static::DEFAULT_CODE_TTL);
    }

    /**
     * @param string $phone
     * @param string $message
     *
     * @return string
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Http\Exceptions\ClassMustBeDTOException
     * @throws \Scaleplan\Http\Exceptions\HttpException
     * @throws \Scaleplan\Http\Exceptions\RemoteServiceNotAvailableException
     * @throws \Exception
     */
    public function sendCode(string $phone, string $message) : string
    {
        $key = random_bytes(static::KEY_LENGTH);
        $value = $this->cache->get($key);
        if ($value) {
            return $key;
        }

        $code = random_int(1000, 9999);
        $this->cache->set($key, $code, $this->ttl);
        $this->sms->send([$phone], str_replace(':code', $code, $message));

        return $key;
    }

    /**
     * @param int $code
     * @param string $key
     *
     * @throws SmsException
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     */
    public function checkCode(int $code, string $key) : void
    {
        $value = $this->cache->get($key);
        if (!$value) {
            throw new SmsException(translate('sms.code-expired'));
        }

        if ($code !== $value) {
            throw new SmsException(translate('sms.invalid-code'));
        }

        $this->cache->unlink($key);
    }
}
