<?php

namespace Tests\Unit;

use App\Models\Job;
use App\Support\JobDescriptionSanitizer;
use Tests\TestCase;

class JobDescriptionSanitizerTest extends TestCase
{
    private JobDescriptionSanitizer $sanitizer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->sanitizer = app(JobDescriptionSanitizer::class);
    }

    public function test_preserves_professional_formatting_and_safe_links(): void
    {
        $sanitized = $this->sanitizer->sanitize(
            '<h2>Responsibilities</h2><p><strong>Lead the team</strong> and <em>deliver results</em>.</p><ul><li>Plan work</li></ul><p><a href="https://example.com/apply">Apply</a></p>'
        );

        $this->assertStringContainsString('<h2>Responsibilities</h2>', $sanitized);
        $this->assertStringContainsString('<strong>Lead the team</strong>', $sanitized);
        $this->assertStringContainsString('<em>deliver results</em>', $sanitized);
        $this->assertStringContainsString('<ul><li>Plan work</li></ul>', $sanitized);
        $this->assertStringContainsString('href="https://example.com/apply"', $sanitized);
        $this->assertSame($sanitized, $this->sanitizer->sanitize($sanitized));
        $this->assertStringContainsString(
            'href="http://example.com/role"',
            $this->sanitizer->sanitize('<a href="http://example.com/role">Role</a>')
        );
    }

    public function test_removes_scripts_event_handlers_unsafe_links_and_embeds(): void
    {
        $sanitized = $this->sanitizer->sanitize(
            '<p onclick="alert(1)" style="color:red">Safe text</p><script>alert(1)</script><a href="javascript:alert(1)" target="_blank" onclick="alert(1)">Unsafe link</a><a href="data:text/html,evil">Data link</a><a href="vbscript:msgbox(1)">VBScript link</a><a href="/relative/path">Relative link</a><iframe src="https://evil.example"></iframe><object data="x"></object><embed src="x"><form action="https://evil.example"><input name="x"></form><svg onload="alert(1)"><script>alert(2)</script></svg>'
        );

        $this->assertStringContainsString('Safe text', $sanitized);
        $this->assertStringNotContainsString('<script', strtolower($sanitized));
        $this->assertStringNotContainsString('<iframe', strtolower($sanitized));
        $this->assertStringNotContainsString('<object', strtolower($sanitized));
        $this->assertStringNotContainsString('<embed', strtolower($sanitized));
        $this->assertStringNotContainsString('<form', strtolower($sanitized));
        $this->assertStringNotContainsString('<svg', strtolower($sanitized));
        $this->assertStringNotContainsString('onclick', strtolower($sanitized));
        $this->assertStringNotContainsString('style=', strtolower($sanitized));
        $this->assertStringNotContainsString('javascript:', strtolower($sanitized));
        $this->assertStringNotContainsString('data:text/html', strtolower($sanitized));
        $this->assertStringNotContainsString('vbscript:', strtolower($sanitized));
        $this->assertStringNotContainsString('href="/relative/path"', strtolower($sanitized));
        $this->assertStringNotContainsString('target=', strtolower($sanitized));
    }

    public function test_plain_text_descriptions_remain_readable_and_empty_markup_has_no_text(): void
    {
        $this->assertSame(
            'A plain-text description remains readable.',
            $this->sanitizer->plainText('A plain-text description remains readable.')
        );
        $this->assertSame('', $this->sanitizer->plainText('<p><br></p><p>&nbsp;</p>'));
        $this->assertFalse($this->sanitizer->hasMinimumText('<p><br></p>', 50));
        $this->assertFalse($this->sanitizer->hasMinimumText(str_repeat("\u{200B}", 50), 50));
        $this->assertFalse($this->sanitizer->hasMinimumText(str_repeat("\u{FEFF}", 50), 50));
    }

    public function test_job_model_sanitizes_descriptions_when_assigned_and_read(): void
    {
        $job = new Job;
        $job->description = '<p>Safe description</p><script>alert(1)</script>';

        $this->assertSame('<p>Safe description</p>', $job->getAttributes()['description']);

        $legacyJob = new Job;
        $legacyJob->setRawAttributes([
            'description' => '<h3>Legacy format</h3><script>alert(1)</script>',
        ]);

        $this->assertSame('<h3>Legacy format</h3>', $legacyJob->description);
        $this->assertSame('Legacy format', $legacyJob->description_text);
    }
}