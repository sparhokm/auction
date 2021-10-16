<?php

declare(strict_types=1);

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use App\Http\Middleware;

return static function (App $app): void {
    $app->add(Middleware\DomainExceptionHandler::class);
    $app->add(Middleware\ClearEmptyInput::class);
    $app->addBodyParsingMiddleware();
    $app->add(ErrorMiddleware::class);
};
