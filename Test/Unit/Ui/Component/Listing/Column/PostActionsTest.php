<?php

declare(strict_types=1);

namespace MageMastery\Blog\Test\Unit\Ui\Component\Listing\Column;

use MageMastery\Blog\Ui\Component\Listing\Column\PostActions;
use Magento\Framework\Escaper;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

class PostActionsTest extends TestCase
{
    private PostActions $object;

    private MockObject $context;
    private MockObject $uiComponentFactory;
    private MockObject $urlBuilder;
    private MockObject $escaper;


    protected function setUp(): void
    {
        $this->context = $this->getMockForAbstractClass(
            ContextInterface::class,
            [],
            '',
            false,
            false,
            true,
            []
        );
        $this->uiComponentFactory = $this->getMockBuilder(UiComponentFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->urlBuilder = $this->getMockForAbstractClass(
            UrlInterface::class,
            [],
            '',
            false,
            false,
            true,
            ['getUrl']
        );
        $this->escaper = $this->getMockBuilder(Escaper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->object = new PostActions(
            $this->context,
            $this->uiComponentFactory,
            $this->urlBuilder,
            $this->escaper
        );
    }

    public function testPrepareDataSourceEmpty(): void
    {
        $dataSource = [];
        $this->assertEmpty($this->object->prepareDataSource($dataSource));
    }

    public function testPrepareDataSourceLinks(): void
    {
        $dataSource = [
            'data' => [
                'items' => [
                    [
                        'post_id' => 1,
                        'title' => 'Post 1',
                    ],
                    [
                        'post_id' => 2,
                        'title' => 'Post 2',
                    ]
                ]
            ]
        ];
        $componentName = 'action';
        $this->object->setData('name', $componentName);

        $this->urlBuilder
            ->method('getUrl')
            ->withConsecutive(
                [$this->equalTo('magemastery_blog/post/edit'), $this->equalTo(['post_id' => 1])],
                [$this->equalTo('magemastery_blog/post/delete'), $this->equalTo(['post_id' => 1])],
                [$this->equalTo('magemastery_blog/post/edit'), $this->equalTo(['post_id' => 2])],
                [$this->equalTo('magemastery_blog/post/delete'), $this->equalTo(['post_id' => 2])],
            )
            ->willReturnOnConsecutiveCalls(
                'magemastery_blog/post/edit/post_id/1',
                'magemastery_blog/post/delete/post_id/1',
                'magemastery_blog/post/edit/post_id/2',
                'magemastery_blog/post/delete/post_id/2',
            );

        $result = $this->object->prepareDataSource($dataSource);

        $itemOneResult = array_pop($result['data']['items']);
        $itemTwoResult = array_pop($result['data']['items']);

        $this->assertArrayHasKey('edit', $itemOneResult[$componentName]);
        $this->assertArrayHasKey('delete', $itemOneResult[$componentName]);
        $this->assertArrayHasKey('edit', $itemTwoResult[$componentName]);
        $this->assertArrayHasKey('delete', $itemTwoResult[$componentName]);
    }
}
