<?php

declare(strict_types=1);

namespace PHPCensor\Common\External;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Email\EmailInterface;
use PHPCensor\Common\Email\EmailSenderInterface;
use Symfony\Component\Mailer\MailerInterface;
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
    private ConfigurationInterface $configuration;
    private BuildLoggerInterface $logger;
    private MailerInterface $mailer;

    public function __construct(
        ConfigurationInterface $configuration,
        BuildLoggerInterface $logger,
        MailerInterface $mailer
    ) {
        $this->configuration = $configuration;
        $this->logger        = $logger;
        $this->mailer        = $mailer;
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
            $this->mailer->send($message);
        } catch (\Throwable $e) {
            $this->logger->logWarning($e->getMessage());
        }
    }
}
