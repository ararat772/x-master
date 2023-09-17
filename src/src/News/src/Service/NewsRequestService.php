<?php

namespace News\Service;

use Psr\Http\Message\ServerRequestInterface;
use Ramsey\Uuid\Exception\InvalidUuidStringException;
use Ramsey\Uuid\Uuid;

class NewsRequestService
{
    public function handleRequest(ServerRequestInterface $request): array
    {
        $data = $request->getParsedBody();

        if (!isset($data['title']) || empty(trim($data['title'])) ||
            !isset($data['text']) || empty(trim($data['text']))) {
            throw new \InvalidArgumentException('Title and text are required fields.');
        }

        return $data;
    }

    public function handleUpdateRequest(ServerRequestInterface $request): array
    {
        $newsId = $request->getAttribute('id');

        if (!Uuid::isValid($newsId)) {
            throw new InvalidUuidStringException("Invalid UUID format");
        }

        $data = $request->getParsedBody();

        if (!isset($data['title']) || empty(trim($data['title'])) ||
            !isset($data['text']) || empty(trim($data['text']))) {
            throw new \InvalidArgumentException('Title and text are required fields.');
        }

        return [
            'uuid' => Uuid::fromString($newsId),
            'data' => $data
        ];
    }

    public function handleListRequest(ServerRequestInterface $request): array
    {
        $data = $request->getQueryParams();

        $page = (isset($data['page']) && $data['page']) ? $data['page'] : 1;
        $limit = (isset($data['limit']) && $data['limit']) ? $data['limit'] : 10;

        return [
            'page' => $page,
            'limit' => $limit,
        ];
    }
}