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

class ListHandler implements RequestHandlerInterface
{
    public function __construct(
       protected NewsServiceInterface $newsService,
        protected NewsRequestService $newsRequestService
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $params = $this->newsRequestService->handleListRequest($request);

            $news = $this->newsService->findAll($params['page'], $params['limit']);

            $response = [];

            foreach ($news as $item) {
                $response[] = [
                    'id'      => $item->getId(),
                    'title'   => $item->getTitle(),
                    'text'    => $item->getText(),
                    'created' => $item->getCreated()->format('c')
                ];
            }

            return new JsonResponse($response, StatusCodeInterface::STATUS_OK);

        } catch (\InvalidArgumentException $e) {
            return new JsonResponse(['error' => $e->getMessage()], StatusCodeInterface::STATUS_BAD_REQUEST);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Something went wrong!'],
                StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR);
        }
    }
}
