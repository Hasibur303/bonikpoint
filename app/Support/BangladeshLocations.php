<?php

namespace App\Support;

use RuntimeException;

class BangladeshLocations
{
    private const DISTRICT_NAME_OVERRIDES = [
        'Nawabganj' => 'Chapainawabganj',
        'Sirajgonj' => 'Sirajganj',
        'Khagrachari' => 'Khagrachhari',
        'Maulvibazar' => 'Moulvibazar',
    ];

    private static ?array $locations = null;

    public static function districts(): array
    {
        return array_keys(self::thanasByDistrict());
    }

    public static function thanasByDistrict(): array
    {
        if (self::$locations !== null) {
            return self::$locations;
        }

        $districtData = self::readJson('bd-districts.json', 'districts');
        $upazilaData = self::readJson('bd-upazilas.json', 'upazilas');
        $dhakaCityData = self::readJson('dhaka-city.json', 'dhaka');

        $districtsById = [];
        $locations = [];

        foreach ($districtData as $district) {
            $name = self::DISTRICT_NAME_OVERRIDES[$district['name']] ?? $district['name'];
            $districtsById[(string) $district['id']] = $name;
            $locations[$name] = [];
        }

        foreach ($upazilaData as $upazila) {
            $district = $districtsById[(string) $upazila['district_id']] ?? null;

            if ($district) {
                $locations[$district][] = trim($upazila['name']);
            }
        }

        foreach ($dhakaCityData as $area) {
            $locations['Dhaka'][] = trim($area['name']);
        }

        ksort($locations, SORT_NATURAL | SORT_FLAG_CASE);

        foreach ($locations as &$thanas) {
            $thanas = array_values(array_unique(array_filter($thanas)));
            sort($thanas, SORT_NATURAL | SORT_FLAG_CASE);
        }

        return self::$locations = $locations;
    }

    public static function thanas(string $district): array
    {
        $district = self::canonicalDistrict($district) ?? '';

        return self::thanasByDistrict()[$district] ?? [];
    }

    public static function canonicalDistrict(?string $district): ?string
    {
        return self::canonicalValue($district, self::districts());
    }

    public static function canonicalThana(?string $thana, ?string $district): ?string
    {
        $district = self::canonicalDistrict($district);

        return $district ? self::canonicalValue($thana, self::thanas($district)) : null;
    }

    private static function canonicalValue(?string $value, array $options): ?string
    {
        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        foreach ($options as $option) {
            if (strcasecmp($option, $value) === 0) {
                return $option;
            }
        }

        return $value;
    }

    private static function readJson(string $file, string $key): array
    {
        $path = resource_path('data/'.$file);
        $data = json_decode((string) file_get_contents($path), true);

        if (! is_array($data[$key] ?? null)) {
            throw new RuntimeException("Invalid Bangladesh location data: {$file}");
        }

        return $data[$key];
    }
}
