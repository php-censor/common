<?php

declare(strict_types=1);

namespace PHPCensor\Common\External;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Email\EmailInterface;
use PHPCensor\Common\Email\EmailSenderInterface;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport\SendmailTransport;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class EmailSender implements EmailSenderInterface
{
    private BuildLoggerInterface $logger;

    private ConfigurationInterface $configuration;

    public function __construct(
        ConfigurationInterface $configuration,
        BuildLoggerInterface $logger
    ) {
        $this->configuration = $configuration;
        $this->logger        = $logger;
    }

    private function getSwiftMailerFromConfig(): MailerInterface
    {
        $smtpAddress = (string)$this->configuration->get('php-censor.email_settings.smtp_address', '');
        if ($smtpAddress) {
            $transport = new EsmtpTransport(
                $smtpAddress,
                (int)$this->configuration->get('php-censor.email_settings.smtp_port', 25),
                (bool)$this->configuration->get('php-censor.email_settings.smtp_encryption')
            );
            $transport->setUsername(
                (string)$this->configuration->get('php-censor.email_settings.smtp_username', '')
            );
            $transport->setPassword(
                (string)$this->configuration->get('php-censor.email_settings.smtp_password', '')
            );
        } else {
            $transport = new SendmailTransport();
        }

        return new Mailer($transport);
    }

    public function getFrom(): Address
    {
        $from = (string)$this->configuration->get(
            'php-censor.email_settings.from_address',
            EmailInterface::DEFAULT_FROM
        );

        if (false === \strpos($from, '<')) {
            $from = \sprintf('PHP Censor <%s>', $from);
        }

        return Address::create($from);
    }

    public function send(EmailInterface $email): void
    {
        $smtpAddress = $this->configuration->get('php-censor.email_settings.smtp_address');

        $this->logger->logDebug(
            \sprintf("SMTP: '%s'", !empty($smtpAddress) ? 'true' : 'false')
        );

        $mailer = $this->getSwiftMailerFromConfig();

        $message = (new Email())
            ->subject($email->getSubject())
            ->from($this->getFrom())
            ->to(...$email->getEmailTo());

        if ($email->isHtml()) {
            $message->html($email->getBody());
        } else {
            $message->text($email->getBody());
        }

        $carbonCopyEmails = $email->getCarbonCopyEmails();
        if (\count($carbonCopyEmails) > 0) {
            $message->cc(...$carbonCopyEmails);
        }

        try {
            $mailer->send($message);
        } catch (\Throwable $e) {
            $this->logger->logWarning($e->getMessage());
        }
    }
}
