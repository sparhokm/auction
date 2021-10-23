<?php

declare(strict_types=1);

use Middlewares\ContentLanguage;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use App\Http\Middleware;

return static function (App $app): void {
    $app->add(Middleware\DomainExceptionHandler::class);
    $app->add(Middleware\ValidationExceptionHandler::class);
    $app->add(Middleware\ClearEmptyInput::class);
    $app->add(Middleware\TranslatorLocale::class);
    $app->add(ContentLanguage::class);
    $app->addBodyParsingMiddleware();
    $app->add(ErrorMiddleware::class);
};
