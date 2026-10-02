<?php

namespace Tests\Unit;

use App\Support\JobCategories;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class JobCategoriesTest extends TestCase
{
    public function test_approved_categories_are_loaded_without_duplicates(): void
    {
        $categories = JobCategories::values();

        $this->assertNotEmpty($categories);
        $this->assertCount(count($categories), array_unique($categories));
        $this->assertContains('Bookkeeping & General Accounting', $categories);
    }

    public function test_category_validation_accepts_only_approved_values_or_the_unchanged_legacy_value(): void
    {
        $approved = Validator::make(
            ['category' => 'Bookkeeping & General Accounting'],
            ['category' => ['required', 'string', JobCategories::validationRule()]]
        );
        $unapproved = Validator::make(
            ['category' => 'Unapproved category'],
            ['category' => ['required', 'string', JobCategories::validationRule()]]
        );
        $unchangedLegacy = Validator::make(
            ['category' => 'Old category'],
            ['category' => ['required', 'string', JobCategories::validationRule('Old category')]]
        );

        $this->assertTrue($approved->passes());
        $this->assertFalse($unapproved->passes());
        $this->assertTrue($unchangedLegacy->passes());
    }
}