<?php

declare(strict_types=1);

namespace App\FeatureToggle;

use Monolog\Processor\ProcessorInterface;

/**
 * @psalm-import-type Record from \Monolog\Logger
 */
class FeaturesMonologProcessor implements ProcessorInterface
{
    private FeaturesContext $context;

    public function __construct(FeaturesContext $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke(array $record): array
    {
        $record['extra']['features'] = $this->context->getAllEnabled();
        return $record;
    }
}
