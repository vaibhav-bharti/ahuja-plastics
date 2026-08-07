<?php

namespace Tests\Unit;

use App\Services\InventoryService;
use PHPUnit\Framework\TestCase;

class InventoryServiceTest extends TestCase
{
    public function test_it_allocates_consumption_proportionally_without_rounding_drift(): void
    {
        $calculation = InventoryService::materialConsumption([
            ['raw_material_id' => 1, 'quantity' => 10],
            ['raw_material_id' => 2, 'quantity' => 20],
        ], 0.030, 900);

        $this->assertSame(30.0, $calculation['prepared']);
        $this->assertSame(27.0, $calculation['consumption']);
        $this->assertSame([9.0, 18.0], $calculation['allocations']);
        $this->assertSame($calculation['consumption'], array_sum($calculation['allocations']));
    }

    public function test_the_last_line_receives_the_fractional_rounding_remainder(): void
    {
        $calculation = InventoryService::materialConsumption([
            ['raw_material_id' => 1, 'quantity' => 1],
            ['raw_material_id' => 2, 'quantity' => 1],
            ['raw_material_id' => 3, 'quantity' => 1],
        ], 0.001, 1);

        $this->assertSame([0.0, 0.0, 0.001], $calculation['allocations']);
        $this->assertSame(0.001, array_sum($calculation['allocations']));
    }

}
