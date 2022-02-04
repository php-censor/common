<?php

declare(strict_types=1);

namespace Tests\PHPCensor\Common\Email;

use PHPCensor\Common\Email\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    public function testConstruct(): void
    {
        $email = (new Email())
            ->setBody('Body')
            ->setSubject('Subject')
            ->setIsHtml(true)
            ->addEmailTo('test-1@test.test', 'Test 1')
            ->addEmailTo('test-2@test.test')
            ->addCarbonCopyEmail('test-cc-1@test.test', 'Test CC 1')
            ->addCarbonCopyEmail('test-cc-2@test.test');

        self::assertEquals('Body', $email->getBody());
        self::assertEquals('Subject', $email->getSubject());
        self::assertEquals(true, $email->isHtml());
        self::assertEquals([
            'Test 1 <test-1@test.test>',
            'test-2@test.test',
        ], $email->getEmailsTo());
        self::assertEquals([
            'Test CC 1 <test-cc-1@test.test>',
            'test-cc-2@test.test',
        ], $email->getCarbonCopyEmails());
    }
}
