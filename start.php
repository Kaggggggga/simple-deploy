<?php
require_once __DIR__ . '/vendor/autoload.php';

(new \Dotenv\Dotenv(__DIR__))->load();
function env($key, $default = null)
{
    $value = getenv($key);

    if ($value === false) {
        return $default;
    }

    switch (strtolower($value)) {
        case 'true':
        case '(true)':
            return true;

        case 'false':
        case '(false)':
            return false;

        case 'empty':
        case '(empty)':
            return '';

        case 'null':
        case '(null)':
            return null;
    }

    return $value;
}

function redis()
{
    $redis = new Predis\Client([
        'host'   => env("REDIS_HOST"),
        'prefix' => env("APP_KEY"),
    ]);
    return $redis;
}