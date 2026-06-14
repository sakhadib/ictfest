<?php

namespace Tests\Unit;

use App\Jobs\SendRegistrationSms;
use App\Models\Registration;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;

class SendRegistrationSmsTest extends TestCase
{
    public function test_it_normalizes_bangladesh_phone_numbers_for_bulk_sms_bd(): void
    {
        $job = new SendRegistrationSms(new Registration(), 'api-key', 'sender-id', 'http://example.test');
        $method = new ReflectionMethod($job, 'normalizePhoneNumber');

        $this->assertSame('8801645534121', $method->invoke($job, '+8801645534121'));
        $this->assertSame('8801645534121', $method->invoke($job, '8801645534121'));
        $this->assertSame('8801645534121', $method->invoke($job, '01645534121'));
        $this->assertSame('8801645534121', $method->invoke($job, '1645534121'));
    }
}
