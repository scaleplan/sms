<?php

namespace Scaleplan\Sms;

use Psr\Log\LoggerInterface;
use Scaleplan\Http\Exceptions\HttpException;
use Scaleplan\Http\Interfaces\RequestInterface;
use Scaleplan\Http\RemoteResponse;
use Scaleplan\Sms\DTO\SmscDTO;
use Scaleplan\Sms\Interfaces\SmsInterface;
use function Scaleplan\DependencyInjection\get_required_container;
use function Scaleplan\Helpers\get_required_env;
use function Scaleplan\Translator\translate;

/**
 * Class Sms
 *
 * @package Scaleplan\Sms
 */
class Sms implements SmsInterface
{
    public const FORMAT_JSON = 3;

    /**
     * @var string
     */
    protected $endpoint = 'https://smsc.ru/sys/send.php';

    /**
     * @var string
     */
    protected $login;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $sender;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Sms constructor.
     *
     * @param string|null $sender
     *
     * @throws \ReflectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Helpers\Exceptions\EnvNotFoundException
     */
    public function __construct(string $sender = null)
    {
        $this->login = get_required_env('SMSC_LOGIN');
        $this->secret = get_required_env('SMSC_PASSWORD');
        $this->sender = $sender ?? get_required_env('SMSC_SENDER');
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger) : void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param array $context
     */
    protected function logOk(string $message, array $context = []) : void
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    protected function logError(string $message, array $context = []) : void
    {
        if (!$this->logger) {
            return;
        }

        $this->logger->error($message, $context);
    }

    /**
     * @param array $phones
     * @param string $message
     *
     * @return RemoteResponse
     *
     * @throws HttpException
     * @throws \ReflectionException
     * @throws \Scaleplan\DTO\Exceptions\ValidationException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ContainerTypeNotSupportingException
     * @throws \Scaleplan\DependencyInjection\Exceptions\DependencyInjectionException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ParameterMustBeInterfaceNameOrClassNameException
     * @throws \Scaleplan\DependencyInjection\Exceptions\ReturnTypeMustImplementsInterfaceException
     * @throws \Scaleplan\Http\Exceptions\ClassMustBeDTOException
     */
    public function send(array $phones, string $message) : RemoteResponse
    {
        $params = [
            'charset' => 'utf-8',
            'login'   => $this->login,
            'psw'     => $this->secret,
            'sender'  => $this->sender,
            'phones'  => implode(';', $phones),
            'mes'     => $message,
            'fmt'     => self::FORMAT_JSON,
        ];

        /** @var RequestInterface $request */
        $request = get_required_container(RequestInterface::class, [$this->endpoint, $params]);
        $request->setDtoClass(SmscDTO::class);
        $request->setMethod('POST');
        $request->setValidationEnable(true);

        try {
            $response = $request->send();
            $this->logOk(translate('sms.ok'), ['phones' => $phones, 'message' => $message]);
            return $response;
        } catch (HttpException $e) {
            $this->logError(translate('sms.error'), ['phones' => $phones, 'message' => $message]);
            throw $e;
        }
    }
}
