<?php

declare(strict_types=1);

namespace MageMastery\Blog\Controller\Adminhtml\Post;

use MageMastery\Blog\Model\PostFactory;
use MageMastery\Blog\Model\ResourceModel\Post as PostResource;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends Action implements HttpPostActionInterface
{
    public function __construct(
        Context $context,
        private PostResource $resource,
        private PostFactory $postFactory
    ) {
        parent::__construct($context);
    }

    public function execute(): ResultInterface
    {
        $data = $this->getRequest()->getPostValue();

        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->postFactory->create();
            if (empty($data['post_id'])) {
                $data['post_id'] = null;
            }

            if (!empty($data['featured_image'][0]['name']) && isset($data['featured_image'][0]['tmp_name'])) {
                $data['featured_image'] = $data['featured_image'][0]['name'];
            } else {
                unset($data['featured_image']);
            }

            $data['update_time'] = null;

            $model->setData($data);

            try {
                $this->resource->save($model);
                $this->messageManager->addSuccessMessage(__('You saved the post.'));
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $exception) {
                $this->messageManager->addExceptionMessage($exception);
            } catch (\Throwable $e) {
               $this->messageManager->addErrorMessage(__('Something went wrong while saving the post.'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
