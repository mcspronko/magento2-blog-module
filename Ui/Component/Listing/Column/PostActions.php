<?php

declare(strict_types=1);

namespace MageMastery\Blog\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class PostActions extends Column
{
    private const URL_PATH_EDIT = 'magemastery_blog/post/edit';

    /**
     * PostActions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        private UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                if (isset($item['post_id'])) {
                    $name = $this->getData('name');
                    $item[$name]['edit'] = [
                        'href' => $this->getEditUrl($item),
                        'label' => __('Edit')
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param array $item
     * @return string
     */
    private function getEditUrl(array $item): string
    {
        return $this->urlBuilder->getUrl(self::URL_PATH_EDIT, ['post_id' => $item['post_id']]);
    }
}
