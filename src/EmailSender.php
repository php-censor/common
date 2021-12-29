<?php

declare(strict_types=1);

namespace PHPCensor\Common;

use PHPCensor\Common\Build\BuildLoggerInterface;

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

    private function getSwiftMailerFromConfig(): \Swift_Mailer
    {
        $smtpAddress = (string)$this->configuration->get('php-censor.email_settings.smtp_address', '');
        if ($smtpAddress) {
            $transport = new \Swift_SmtpTransport(
                $smtpAddress,
                (int)$this->configuration->get('php-censor.email_settings.smtp_port', 25),
                $this->configuration->get('php-censor.email_settings.smtp_encryption')
            );

            $transport->setUsername(
                (string)$this->configuration->get('php-censor.email_settings.smtp_username', '')
            );
            $transport->setPassword(
                (string)$this->configuration->get('php-censor.email_settings.smtp_password', '')
            );
        } else {
            $transport = new \Swift_SendmailTransport();
        }

        return new \Swift_Mailer($transport);
    }

    public function getFrom(): array
    {
        $from = (string)$this->configuration->get('php-censor.email_settings.from_address', Email::DEFAULT_FROM);
        if (false === \strpos($from, '<')) {
            return [\trim($from) => 'PHP Censor'];
        }

        \preg_match('#^(.*?)<(.*?)>$#ui', $from, $fromParts);

        return [
            \trim($fromParts[2]) => \trim($fromParts[1])
        ];
    }

    public function send(EmailInterface $email): int
    {
        $smtpAddress = $this->configuration->get('php-censor.email_settings.smtp_address');

        $this->logger->logDebug(
            \sprintf("SMTP: '%s'", !empty($smtpAddress) ? 'true' : 'false')
        );

        $mailer = $this->getSwiftMailerFromConfig();

        $message = new \Swift_Message($email->getSubject());
        $message
            ->setFrom($this->getFrom())
            ->setTo($email->getEmailTo())
            ->setBody($email->getBody());

        if ($email->isHtml()) {
            $message->setContentType('text/html');
        }

        $carbonCopyEmails = $email->getCarbonCopyEmails();
        if (\is_array($carbonCopyEmails) && \count($carbonCopyEmails)) {
            $message->setCc($carbonCopyEmails);
        }

        \ob_start();

        $result = $mailer->send($message);

        $rawOutput = \ob_get_clean();

        if ($rawOutput) {
            $this->logger->logWarning($rawOutput);
        }

        return $result;
    }
}
