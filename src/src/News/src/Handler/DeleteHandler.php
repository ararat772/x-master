<?php

declare(strict_types=1);

namespace News\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use News\Contract\NewsServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;

class DeleteHandler implements RequestHandlerInterface
{

    public function __construct(
       protected NewsServiceInterface $newsService,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $newsId = $request->getAttribute('id');

        try {
            $uuid = Uuid::fromString($newsId);
            $this->newsService->delete($uuid);

        } catch (InvalidUuidStringException $e) {
            return new JsonResponse(['error' => 'Invalid ID format'], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], StatusCodeInterface::STATUS_NOT_FOUND);
        }

        return new JsonResponse(['message' => 'News with ID ' . $newsId . ' successfully deleted'],
            StatusCodeInterface::STATUS_OK
        );
    }
}
