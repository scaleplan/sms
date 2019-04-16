<?php

namespace Scaleplan\Sms\Interfaces;


use Scaleplan\Http\RemoteResponse;

/**
 * Class Sms
 *
 * @package Scaleplan\Sms
 */
interface SmsInterface
{
    /**
     * @param array $phones
     * @param string $message
     *
     * @return RemoteResponse
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
     */
    public function send(array $phones, string $message) : RemoteResponse;
}
