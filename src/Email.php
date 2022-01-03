<?php

declare(strict_types=1);

namespace PHPCensor\Common;

/**
 * @package    PHP Censor
 * @subpackage Common Library
 *
 * @author Dmitry Khomutov <poisoncorpsee@gmail.com>
 */
class Email implements EmailInterface
{
    public const DEFAULT_FROM = 'PHP Censor <no-reply@php-censor.local>';

    private array $emailTo = [];
    private array $carbonCopyEmails = [];
    private string $subject = 'Email from PHP Censor';
    private string $body = '';
    private bool $isHtml = false;

    public function setEmailTo(string $email, ?string $name = null): self
    {
        $this->emailTo[$email] = $name;

        return $this;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setIsHtml(bool $isHtml = false): self
    {
        $this->isHtml = $isHtml;

        return $this;
    }

    public function addCarbonCopyEmail(string $email, ?string $name = null): self
    {
        $this->carbonCopyEmails[$email] = $name;

        return $this;
    }

    public function getEmailTo(): array
    {
        return $this->emailTo;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function isHtml(): bool
    {
        return $this->isHtml;
    }

    public function getCarbonCopyEmails(): array
    {
        return $this->carbonCopyEmails;
    }
}
