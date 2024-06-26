<?php

declare(strict_types=1);

namespace App\FeatureToggle;

use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class FeaturesMiddleware implements MiddlewareInterface
{
    public function __construct(
        private FeatureSwitch $switch,
        private string $header = 'X-Features'
    ) {}

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeaderLine($this->header);
        $features = array_filter(preg_split('/\s*,\s*/', $header));

        foreach ($features as $feature) {
            if (str_starts_with($feature, '!')) {
                $this->switch->disable(substr($feature, 1));
            } else {
                $this->switch->enable($feature);
            }
        }

        return $handler->handle($request);
    }
}
