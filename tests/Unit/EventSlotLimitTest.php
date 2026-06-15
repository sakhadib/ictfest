<?php

namespace Tests\Unit;

use App\Models\Event;
use PHPUnit\Framework\TestCase;

class EventSlotLimitTest extends TestCase
{
    public function test_fifa_and_valorant_have_slot_limits(): void
    {
        $fifa = new Event(['code' => '05']);
        $valorant = new Event(['code' => '06']);
        $datathon = new Event(['code' => '03']);

        $this->assertSame(64, $fifa->slotLimit());
        $this->assertSame(32, $valorant->slotLimit());
        $this->assertTrue($fifa->hasSlotLimit());
        $this->assertTrue($valorant->hasSlotLimit());
        $this->assertFalse($datathon->hasSlotLimit());
    }
}
