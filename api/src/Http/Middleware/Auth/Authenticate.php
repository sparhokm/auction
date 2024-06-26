<?php

declare(strict_types=1);

namespace App\Http\Middleware\Auth;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use LogicException;
use Override;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final readonly class Authenticate implements MiddlewareInterface
{
    private const string ATTRIBUTE = 'identity';

    public function __construct(private ResourceServer $server, private ResponseFactoryInterface $response) {}

    public static function identity(ServerRequestInterface $request): ?Identity
    {
        $identity = $request->getAttribute(self::ATTRIBUTE);

        if ($identity !== null && !$identity instanceof Identity) {
            throw new LogicException('Invalid identity.');
        }

        return $identity;
    }

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('authorization')) {
            return $handler->handle($request);
        }

        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException $exception) {
            return $exception->generateHttpResponse($this->response->createResponse());
        }

        $identity = new Identity(
            id: (string)$request->getAttribute('oauth_user_id'),
            role: (string)$request->getAttribute('oauth_user_role')
        );

        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $identity));
    }
}
