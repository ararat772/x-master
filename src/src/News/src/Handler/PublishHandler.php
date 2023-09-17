<?php

namespace News\Handler;

use Ramsey\Uuid\Uuid;
use News\Entity\Status;
use Psr\Http\Message\ResponseInterface;
use News\Contract\NewsServiceInterface;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Ramsey\Uuid\Exception\InvalidUuidStringException;

/**
 * @OA\Info(title="News API", version="1.0.0")
 * @OA\Patch(
 *     path="/news/{id}/publish",
 *     summary="Publish a news item",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID of the news item",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response=200, description="News successfully published."),
 *     @OA\Response(response=400, description="Bad Request, Invalid ID format."),
 *     @OA\Response(response=404, description="Not Found, News item not found.")
 * )
 */
class PublishHandler implements RequestHandlerInterface
{

    public function __construct(protected NewsServiceInterface $newsService)
    {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $newsId = $request->getAttribute('id');

        try {
            $uuid = Uuid::fromString($newsId);

            $this->newsService->updateStatus($uuid, Status::Publicated);

            return new JsonResponse(['message' => 'News successfully published.'], StatusCodeInterface::STATUS_OK);
        } catch (InvalidUuidStringException $e) {
            return new JsonResponse(['error' => 'Invalid ID format'], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], StatusCodeInterface::STATUS_NOT_FOUND);
        }
    }
}
