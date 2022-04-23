<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Common\External;

use PHPCensor\Common\Application\ConfigurationInterface;
use PHPCensor\Common\Build\BuildLoggerInterface;
use PHPCensor\Common\Email\Email;
use PHPCensor\Common\External\EmailSender;
use PHPCensor\Common\ParameterBag;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Email as MimeEmail;

class TestConfiguration extends ParameterBag implements ConfigurationInterface
{
    public function load(): void
    {
    }
}

class TestBuildLogger implements BuildLoggerInterface
{
    public function logWarning(string $message): void
    {
    }

    public function logSuccess(string $message): void
    {
    }

    public function logDebug(string $message): void
    {
    }

    public function logNormal(string $message): void
    {
    }

    public function logFailure(
        string $message,
        ?\Exception $exception = null
    ): void {
    }
}

class TestMailer implements MailerInterface
{
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
    }
}

class TestMailerFailed implements MailerInterface
{
    public function send(RawMessage $message, Envelope $envelope = null): void
    {
        throw new TransportException('Boom!');
    }
}

class EmailSenderTest extends TestCase
{
    /**
     * @dataProvider fromProvider
     */
    public function testSendSuccess(string $from, string $expectedFrom, ?bool $isHtml): void
    {
        $email = (new Email())
            ->setBody('Body')
            ->setSubject('Subject');

        if (null !== $isHtml) {
            $email->setIsHtml($isHtml);
        }

        $email
            ->addEmailTo('test-1@test.test', 'Test 1')
            ->addCarbonCopyEmail('test-cc-1@test.test', 'Test CC 1');

        $sender = new EmailSender(
            new TestConfiguration([
                'php-censor' => [
                    'email_settings' => [
                        'from_address' => $from,
                    ],
                ],
            ]),
            new TestBuildLogger(),
            new TestMailer()
        );

        $success = $sender->send($email);
        $message = $sender->getLastMessage();

        self::assertTrue($success);
        self::assertInstanceOf(MimeEmail::class, $message);

        if ($isHtml) {
            self::assertEquals('Body', $message->getHtmlBody());
            self::assertEquals('', $message->getTextBody());
        } else {
            self::assertEquals('', $message->getHtmlBody());
            self::assertEquals('Body', $message->getTextBody());
        }
        self::assertEquals('Subject', $message->getSubject());

        self::assertEquals($expectedFrom, ($message->getFrom())[0]->getName() . ' <' . ($message->getFrom())[0]->getAddress() . '>');
    }

    public function testSendFailed(): void
    {
        $email = (new Email())
            ->setBody('Body')
            ->setSubject('Subject')
            ->addEmailTo('test-1@test.test', 'Test 2')
            ->addCarbonCopyEmail('test-cc-2@test.test', 'Test CC 2');

        $sender = new EmailSender(
            new TestConfiguration([
                'php-censor' => [
                    'email_settings' => [
                        'from_address' => 'test-from-2@test.test',
                    ],
                ],
            ]),
            new TestBuildLogger(),
            new TestMailerFailed()
        );

        $success = $sender->send($email);

        self::assertFalse($success);
    }

    public function fromProvider(): \Traversable
    {
        yield 'Long address' => ['Test PHP Censor <test-from-1@test.test>', 'Test PHP Censor <test-from-1@test.test>', true];
        yield 'Short address' => ['test-from-2@test.test', 'PHP Censor <test-from-2@test.test>', false];
        yield 'Short address without isHtml' => ['test-from-2@test.test', 'PHP Censor <test-from-2@test.test>', null];
    }
}
