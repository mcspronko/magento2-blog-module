<?php

declare(strict_types=1);

namespace MageMastery\Blog\Model\Post;

use MageMastery\Blog\Model\Post;
use MageMastery\Blog\Model\PostFactory;
use MageMastery\Blog\Model\ResourceModel\Post as PostResource;
use MageMastery\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var array
     */
    private array $loadedData;

    /**
     * DataProvider constructor.
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PostResource $resource
     * @param PostFactory $postFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        private PostResource $resource,
        private PostFactory $postFactory,
        private RequestInterface $request,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $collectionFactory->create();
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $post = $this->getCurrentPost();
        $this->loadedData[$post->getId()] = $post->getData();

        return $this->loadedData;
    }

    /**
     * @return Post
     */
    private function getCurrentPost(): Post
    {
        $postId = $this->getPostId();
        $post = $this->postFactory->create();
        if (!$postId) {
            return $post;
        }

        $this->resource->load($post, $postId);

        return $post;
    }

    /**
     * @return int
     */
    private function getPostId(): int
    {
        return (int) $this->request->getParam($this->getRequestFieldName());
    }
}
