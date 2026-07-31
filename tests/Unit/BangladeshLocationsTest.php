<?php

namespace Tests\Unit;

use App\Support\BangladeshLocations;
use Tests\TestCase;

class BangladeshLocationsTest extends TestCase
{
    public function test_it_loads_all_districts_and_upazilas(): void
    {
        $this->assertCount(64, BangladeshLocations::districts());
        $this->assertGreaterThanOrEqual(494, array_sum(array_map(
            'count',
            BangladeshLocations::thanasByDistrict()
        )));
    }

    public function test_it_includes_dhaka_upazilas_and_city_areas(): void
    {
        $thanas = BangladeshLocations::thanas('Dhaka');

        $this->assertContains('Savar', $thanas);
        $this->assertContains('Dhanmondi', $thanas);
        $this->assertContains('Uttara', $thanas);
    }

    public function test_it_canonicalizes_location_names_case_insensitively(): void
    {
        $this->assertSame('Dhaka', BangladeshLocations::canonicalDistrict('dhaka'));
        $this->assertSame('Dhanmondi', BangladeshLocations::canonicalThana('dhanmondi', 'DHAKA'));
        $this->assertSame('Chapainawabganj', BangladeshLocations::canonicalDistrict('chapainawabganj'));
    }
}
