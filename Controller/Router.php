<?php declare(strict_types=1);

namespace MageMastery\Blog\Controller;

use MageMastery\Blog\Service\PostIdChecker;
use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    /**
     * @param PostIdChecker $postIdChecker
     * @param ActionFactory $actionFactory
     */
    public function __construct(
        private PostIdChecker $postIdChecker,
        private ActionFactory $actionFactory
    ) {
    }

    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request)
    {
        $pathInfo = trim((string) $request->getPathInfo(), '/');

        $parts = explode('/', $pathInfo);
        if (!empty($parts[0]) && 'blog' === $parts[0] && !empty($parts[1])) {
            $urlKey = $parts[1];
        } else {
            return null;
        }

        $postId = $this->postIdChecker->checkUrlKey($urlKey);

        if (!$postId) {
            return null;
        }

        $request
            ->setModuleName('blog')
            ->setControllerName('post')
            ->setActionName('view')
            ->setParam('post_id', $postId);
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $pathInfo);
        $request->setPathInfo($urlKey);

        return $this->actionFactory->create(Forward::class);
    }
}
