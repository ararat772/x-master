<?php

declare(strict_types=1);

namespace News\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use News\Contract\NewsServiceInterface;
use News\Service\NewsRequestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;

/**
 * @OA\Info(title="News API", version="1.0")
 * @OA\Put(
 *     path="/news/{id}",
 *     summary="Update a news item",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="UUID of the news item",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\RequestBody(
 *         description="News data to update",
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="application/json",
 *             @OA\Schema(
 *                 @OA\Property(property="title", type="string"),
 *                 @OA\Property(property="text", type="string")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=200, description="News updated successfully"),
 *     @OA\Response(response=400, description="Bad Request, Invalid ID format."),
 *     @OA\Response(response=404, description="Not Found, News item not found.")
 * )
 */
class UpdateHandler implements RequestHandlerInterface
{

    public function __construct(
        protected NewsServiceInterface $newsService,
        protected NewsRequestService $newsRequestService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $requestData = $this->newsRequestService->handleUpdateRequest($request);
            $this->newsService->update($requestData['uuid'], $requestData['data']['title'], $requestData['data']['text']);

            return new JsonResponse(['message' => 'News updated successfully.'], StatusCodeInterface::STATUS_OK);
        } catch (InvalidUuidStringException $e) {
            return new JsonResponse(['error' => 'Invalid ID format'], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }
}
