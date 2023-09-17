<?php

declare(strict_types=1);

namespace News;
use News\Entity\News;
use News\Entity\Status;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\EntityRepository;
use News\Contract\NewsServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class NewsService implements NewsServiceInterface
{

    public function __construct(
        private EntityManagerInterface $em
    )
    {

    }

    public function findById(UuidInterface $id): News
    {
        return $this->getRepository()->findById($id);
    }


    public function findAll(int $page = 1, int $limit = 10): iterable
    {
        $offset =  ($page - 1) * $limit;

        return $this->getRepository()->findBy([
           'status' => Status::Publicated
        ], [
            'created' => 'DESC'
        ], $limit, $offset);
    }

    public function create(string $title, string $text): News
    {
        $news = new News($title, $text);
        $this->em->persist($news);
        $this->em->flush();
        return $news;
    }


    public function delete(UuidInterface $id): void
    {
        $news = $this->getRepository()->find($id);

        if (!$news) {
            throw new \InvalidArgumentException("News with ID {$id->toString()} not found.");
        }

        $this->em->remove($news);
        $this->em->flush();
    }

    /**
     * Update an existing news entry by ID.
     *
     * @param string $id
     * @param string $title
     * @param string $text
     *
     * @return void
     */
    public function update(UuidInterface $id, string $title, string $text): void
    {
        $news = $this->getRepository()->find($id);

        if (!$news) {
            throw new \InvalidArgumentException("News with ID {$id->toString()} not found.");
        }

        $news->setTitle($title);
        $news->setText($text);

        $this->em->persist($news);
        $this->em->flush();
    }

    public function updateStatus(UuidInterface $id, Status $status): void
    {
        $news = $this->getRepository()->find($id);

        if (!$news) {
            throw new \InvalidArgumentException("News with ID {$id->toString()} not found.");
        }

        $news->setStatus($status);

        $this->em->persist($news);
        $this->em->flush();
    }

    private function getRepository(): EntityRepository
    {
        return $this->em->getRepository(News::class);
    }
}