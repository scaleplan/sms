<?php

namespace Scaleplan\Sms\Exceptions;

/**
 * Class SmsException
 *
 * @package Scaleplan\Sms\Exceptions
 */
class SmsException extends \Exception
{
    public const MESSAGE = 'Ошибка отправки SMS.';
    public const CODE = 523;

    /**
     * SmsException constructor.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     */
    public function __construct($message = '', $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message ?: static::MESSAGE, $code ?: static::CODE, $previous);
    }
}
