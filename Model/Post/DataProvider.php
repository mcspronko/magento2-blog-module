<?php

declare(strict_types=1);

namespace MageMastery\Blog\Model\Post;

use MageMastery\Blog\Model\Post;
use MageMastery\Blog\Model\PostFactory;
use MageMastery\Blog\Model\ResourceModel\Post as PostResource;
use MageMastery\Blog\Model\ResourceModel\Post\CollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File\Mime;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\ReadInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var array
     */
    private array $loadedData;

    /**
     * @var ReadInterface 
     */
    private ReadInterface $mediaDirectory;

    /**
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PostResource $resource
     * @param PostFactory $postFactory
     * @param RequestInterface $request
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @param Mime $mime
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
        Filesystem $filesystem,
        private StoreManagerInterface $storeManager,
        private Mime $mime,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $collectionFactory->create();
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData(): array
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $post = $this->getCurrentPost();
        $postData = $post->getData();

        $image = $postData['featured_image'];

        $imgDir = 'tmp/imageUploader/images';
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA);

        $fullImagePath = $this->mediaDirectory->getAbsolutePath($imgDir) . '/' . $image;
        $imageUrl = $baseUrl . $imgDir . '/' . $image;
        $stat = $this->mediaDirectory->stat($fullImagePath);
        
        $postData['featured_image'] = null;
        $postData['featured_image'][0]['url'] = $imageUrl;
        $postData['featured_image'][0]['name'] = $image;
        $postData['featured_image'][0]['size'] = $stat['size'];
        $postData['featured_image'][0]['type'] = $this->mime->getMimeType($fullImagePath);

        $this->loadedData[$post->getId()] = $postData;

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
