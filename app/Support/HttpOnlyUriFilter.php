<?php

namespace App\Support;

class HttpOnlyUriFilter extends \HTMLPurifier_URIFilter
{
    public $name = 'HttpOnlyJobDescription';

    public $always_load = true;

    public $post = true;

    public function filter(&$uri, $config, $context): bool
    {
        return in_array($uri->scheme, ['http', 'https'], true)
            && is_string($uri->host)
            && $uri->host !== '';
    }
}