<?php

namespace OjsSdk\Providers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;

class LogProvider extends Provider
{
    /**
     * @var Logger
     */
    public $logger;
    /**
     * @return \OjsSdk\Providers\Provider
     */
    public static function register($system)
    {
        $instance = self::getInstance();
        $instance->logger = new \Monolog\Logger($system);

        // Log to email
        $transporter = new \Swift_SmtpTransport(getenv('SMTP_HOST'), getenv('SMTP_PORT'), getenv('SMTP_ENCRYPTION'));
        $transporter->setUsername(getenv('SMTP_USERNAME'));
        $transporter->setPassword(getenv('SMTP_PASSWORD'));
        $message = (new \Swift_Message('A CRITICAL log was added'));
        $message->setFrom([
            getenv('MAIL_FROM') => getenv('MAIL_FROM_NAME')
        ]);
        $message->setTo([
            getenv('MAIL_FROM') => getenv('MAIL_FROM_NAME')
        ]);
        $mailer = new \Swift_Mailer($transporter);
        $instance->logger->pushHandler(new SwiftMailerHandler($mailer, $message, Logger::DEBUG, false));

        // Log to file
        $instance->logger->pushHandler(new StreamHandler(getenv('LOG_DIR') . '/ojs-sdk.log', Logger::DEBUG));

        return $instance;
    }

    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        $instance = self::getInstance();
        return $instance->logger;
    }

    public function setErrorHandler()
    {
        // Error Handler
        $handler = new \Monolog\ErrorHandler($this->logger);
        $handler->registerErrorHandler([], false);
        $handler->registerExceptionHandler();
        $handler->registerFatalHandler();
    }
}
