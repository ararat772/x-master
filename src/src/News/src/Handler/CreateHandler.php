<?php

declare(strict_types=1);

namespace News\Handler;

use Fig\Http\Message\StatusCodeInterface;
use Laminas\Diactoros\Response\JsonResponse;
use News\Contract\NewsServiceInterface;
use News\NewsService;
use News\Service\NewsRequestService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CreateHandler implements RequestHandlerInterface
{

    public function __construct(
        protected NewsServiceInterface $newsService,
        protected NewsRequestService $newsRequestService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $data = $this->newsRequestService->handleRequest($request);

            $news = $this->newsService->create($data['title'], $data['text']);

            $response = [
                'id'      => $news->getId(),
                'title'   => $news->getTitle(),
                'text'    => $news->getText(),
                'created' => $news->getCreated()->format('c')
            ];

            return new JsonResponse($response, StatusCodeInterface::STATUS_CREATED);
        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], StatusCodeInterface::STATUS_BAD_REQUEST);
        }
    }
}
