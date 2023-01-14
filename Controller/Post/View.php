<?php declare(strict_types=1);

namespace MageMastery\Blog\Controller\Post;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url;
use Magento\Framework\View\Result\PageFactory;

class View implements HttpGetActionInterface
{
    public function __construct(
        private PageFactory $pageFactory,
        private RequestInterface $request
    ) {
    }

    public function execute()
    {
        var_dump($this->request->getAlias(Url::REWRITE_REQUEST_PATH_ALIAS));
        var_dump($this->request->getPathInfo());
        var_dump($this->request->getParam('post_id'));
        return $this->pageFactory->create();
    }
}
