<?php

declare(strict_types=1);

namespace Test\Functional;

use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @internal
 */
final class NotFoundTest extends WebTestCase
{
    use ArraySubsetAsserts;

    public function testNotFound(): void
    {
        $response = $this->app()->handle(self::json('GET', '/not-found'));

        self::assertSame(404, $response->getStatusCode());
        self::assertJson($body = (string)$response->getBody());

        self::assertArraySubset([
            'message' => '404 Not Found',
        ], Json::decode($body));
    }
}
