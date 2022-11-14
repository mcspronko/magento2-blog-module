<?php

declare(strict_types=1);

namespace MageMastery\Blog\ViewModel;

use MageMastery\Blog\Model\ResourceModel\Post\Collection;
use MageMastery\Blog\Service\PostsProvider;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Posts implements ArgumentInterface
{
    public function __construct(
        private PostsProvider $postsProvider,
        private RequestInterface $request
    ) {}

    public function getPosts(int $limit): Collection
    {
        return $this->postsProvider->getPosts($limit, $this->getCurrentPage());
    }

    private function getCurrentPage(): int
    {
        return (int) $this->request->getParam('page');
    }
}
