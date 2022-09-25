<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

const ALLOWED_LANGUAGES = [
    'en',
    'pl',
];

/**
 * @param Throwable $throwable
 * @return void
 */
function reportError(Throwable $throwable): void
{
    Log::error(
        $throwable->getMessage() . PHP_EOL
        . 'in line: ' . $throwable->getFile() . PHP_EOL
        . 'in file: ' . $throwable->getFile()
    );
}

/**
 * @param Request $request
 * @return string
 */
function getRequestLang(Request $request): string
{
    $lang = 'en';
    $requestLang = strtolower($request->header('Accept-Language'));

    foreach (ALLOWED_LANGUAGES as $allowed) {
        if (strpos($requestLang, $allowed) !== false) {
            $lang = $allowed;
        }
    }
    return $lang;
}
