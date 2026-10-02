<?php

namespace App\Support;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use RuntimeException;

class JobCategories
{
    public static function grouped(): array
    {
        static $groups = null;

        if ($groups !== null) {
            return $groups;
        }

        $path = base_path('comprehensive_job_categories.csv');
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException('The approved job category CSV could not be opened.');
        }

        $groups = [];
        $seenCategories = [];

        try {
            fgetcsv($handle, null, ',', '"', '');

            while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
                if (count($row) < 2) {
                    continue;
                }

                [$group, $category] = array_map('trim', array_slice($row, 0, 2));

                if ($group === '' || $category === '' || isset($seenCategories[$category])) {
                    continue;
                }

                $groups[$group][] = $category;
                $seenCategories[$category] = true;
            }
        } finally {
            fclose($handle);
        }

        return $groups;
    }

    public static function values(): array
    {
        return array_merge(...array_values(self::grouped()));
    }

    public static function validationRule(?string $unchangedLegacyCategory = null): In
    {
        $categories = self::values();

        if ($unchangedLegacyCategory !== null && $unchangedLegacyCategory !== '') {
            $categories[] = $unchangedLegacyCategory;
        }

        return Rule::in(array_unique($categories));
    }
}