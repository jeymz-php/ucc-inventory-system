<?php

namespace Tests\Unit;

use App\Http\Controllers\ConsumableRequestController;
use PHPUnit\Framework\TestCase;

class ConsumableRequestControllerTest extends TestCase
{
    public function test_it_parses_full_first_name_and_last_name_from_user_name(): void
    {
        $controller = new ConsumableRequestController();

        $result = $controller->parseRecipientName('James Ryan Gregorio');

        $this->assertSame('James Ryan', $result['first_name']);
        $this->assertSame('Gregorio', $result['last_name']);
    }

    public function test_it_builds_blank_receipt_payload_when_requested(): void
    {
        $controller = new ConsumableRequestController();
        $method = new \ReflectionMethod($controller, 'buildReportPayload');
        $method->setAccessible(true);

        $payload = $method->invoke($controller, null, true);

        $this->assertTrue($payload['blankReceipt']);
        $this->assertSame('', $payload['referenceNo']);
        $this->assertSame('', $payload['recipientName']);
    }
}
