<?php declare(strict_types=1);

namespace MageMastery\Blog\ViewModel\Post;

use MageMastery\Blog\Model\Post;
use MageMastery\Blog\Model\ResourceModel\Post\Collection;
use MageMastery\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class PostView implements ArgumentInterface
{
    public function __construct(
        private RequestInterface $request,
        private CollectionFactory $collectionFactory
    ) {
    }

    public function getPost(): Post
    {
        /** @var Collection $collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('post_id', (int)$this->request->getParam('post_id'));

        return $collection->getFirstItem();
    }
}
