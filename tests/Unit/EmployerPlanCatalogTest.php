<?php

namespace Tests\Unit;

use Tests\TestCase;

class EmployerPlanCatalogTest extends TestCase
{
    public function test_standard_plan_prices_allowances_and_validity_are_authoritative(): void
    {
        $packages = config('employer_plans');

        $this->assertSame(3000, $packages['basic']['amount']);
        $this->assertSame(3, $packages['basic']['job_allowance']);
        $this->assertSame(['days', 30], [$packages['basic']['duration_unit'], $packages['basic']['duration_value']]);

        $this->assertSame(10000, $packages['starter']['amount']);
        $this->assertSame(10, $packages['starter']['job_allowance']);
        $this->assertSame(['days', 60], [$packages['starter']['duration_unit'], $packages['starter']['duration_value']]);

        $this->assertSame(20000, $packages['growth']['amount']);
        $this->assertSame(20, $packages['growth']['job_allowance']);
        $this->assertSame(['days', 90], [$packages['growth']['duration_unit'], $packages['growth']['duration_value']]);

        $this->assertSame(50000, $packages['business']['amount']);
        $this->assertSame(50, $packages['business']['job_allowance']);
        $this->assertSame(['months', 6], [$packages['business']['duration_unit'], $packages['business']['duration_value']]);
    }

    public function test_free_plan_is_removed_growth_is_popular_and_enterprise_is_custom(): void
    {
        $packages = config('employer_plans');

        $this->assertArrayNotHasKey('free', $packages);
        $this->assertTrue($packages['growth']['most_popular']);
        $this->assertNull($packages['enterprise']['amount']);
        $this->assertSame(50, $packages['enterprise']['job_allowance']);
        $this->assertSame('Custom', $packages['enterprise']['price']);
    }
}