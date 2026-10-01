<?php

namespace App\Support;

use HTMLPurifier;
use HTMLPurifier_Config;

class JobDescriptionSanitizer
{
    private const ALLOWED_HTML = 'p,br,strong,b,em,i,u,h2,h3,h4,ul,ol,li,a[href],blockquote,hr';

    private HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', self::ALLOWED_HTML);
        $config->set('URI.AllowedSchemes', [
            'http' => true,
            'https' => true,
        ]);
        $config->set('HTML.Trusted', false);
        $config->set('Attr.EnableID', false);
        $config->getDefinition('URI', true)->registerFilter(
            new HttpOnlyUriFilter
        );

        $this->purifier = new HTMLPurifier($config);
    }

    public function sanitize(?string $html): string
    {
        return $this->purifier->purify($html ?? '');
    }

    public function plainText(?string $html): string
    {
        $sanitized = $this->sanitize($html);
        $withWordSeparators = preg_replace(
            '~</?(?:p|br|h[2-4]|li|blockquote|hr)\b[^>]*>~i',
            ' ',
            $sanitized
        ) ?? $sanitized;
        $text = html_entity_decode(
            strip_tags($withWordSeparators),
            ENT_QUOTES | ENT_HTML5,
            'UTF-8'
        );
        $normalized = preg_replace('/[\s\p{Z}\p{Cf}]+/u', ' ', $text);

        return trim($normalized ?? $text);
    }

    public function hasMinimumText(?string $html, int $minimumCharacters): bool
    {
        return mb_strlen($this->plainText($html)) >= $minimumCharacters;
    }
}