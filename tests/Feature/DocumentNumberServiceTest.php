<?php

namespace Tests\Feature;

use App\Services\DocumentNumberService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocumentNumberServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_numbers_are_sequential_per_prefix_and_reset_per_day(): void
    {
        $service = new DocumentNumberService;
        $day = Carbon::parse('2026-09-24');

        $this->assertSame('PB-20260924-0001', $service->next('PB', $day));
        $this->assertSame('PB-20260924-0002', $service->next('PB', $day));
        $this->assertSame('PJ-20260924-0001', $service->next('PJ', $day));
        $this->assertSame('PB-20260925-0001', $service->next('PB', $day->copy()->addDay()));
        $this->assertSame('PB-20260924-0003', $service->next('PB', $day));
    }
}
