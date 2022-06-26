<?php

declare(strict_types=1);

namespace MageMastery\Blog\Model;

use Magento\Framework\Model\AbstractModel;
use MageMastery\Blog\Model\ResourceModel\Post as PostResource;

class Post extends AbstractModel
{
    protected function _construct()
    {
        $this->_init(PostResource::class);
    }
}
