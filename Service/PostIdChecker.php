<?php declare(strict_types=1);

namespace MageMastery\Blog\Service;

use MageMastery\Blog\Model\ResourceModel\Post;

class PostIdChecker
{
    public function __construct(private Post $post)
    {
    }

    public function checkUrlKey(string $urlKey): int
    {
        $connection = $this->post->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName('magemastery_blog_post'), 'post_id')
            ->where('url_key = ?', $urlKey);

        return (int) $connection->fetchOne($select);
    }
}
