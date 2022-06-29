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
    private ?Email $lastMessage = null;

    public function __construct(
        ConfigurationInterface $configuration,
        BuildLoggerInterface $logger,
        MailerInterface $mailer
    ) {
        $this->configuration = $configuration;
        $this->logger        = $logger;
        $this->mailer        = $mailer;
    }

    private function getFrom(): Address
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

    private function createEmail(EmailInterface $email): Email
    {
        $message = (new Email())
            ->subject($email->getSubject())
            ->from($this->getFrom())
            ->to(...$email->getEmailsTo());

        if ($email->isHtml()) {
            $message->html($email->getBody());
        } else {
            $message->text($email->getBody());
        }

        $carbonCopyEmails = $email->getCarbonCopyEmails();
        if (\count($carbonCopyEmails) > 0) {
            $message->cc(...$carbonCopyEmails);
        }

        return $message;
    }

    public function getLastMessage(): ?Email
    {
        return $this->lastMessage;
    }

    public function send(EmailInterface $email, bool $verbose = false): bool
    {
        if ($verbose) {
            $smtpAddress = $this->configuration->get('php-censor.email_settings.smtp_address');

            $this->logger->logDebug(
                \sprintf("SMTP: '%s'", !empty($smtpAddress) ? 'true' : 'false')
            );
        }

        $this->lastMessage = $this->createEmail($email);

        try {
            $this->mailer->send(
                $this->lastMessage
            );
        } catch (\Throwable $e) {
            if ($verbose) {
                $this->logger->logWarning($e->getMessage());
            }

            return false;
        }

        return true;
    }
}
