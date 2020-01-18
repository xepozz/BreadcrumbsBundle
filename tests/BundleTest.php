<?php

namespace Xepozz\BreadcrumbsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Xepozz\BreadcrumbsBundle\Templating\Helper\BreadcrumbsHelper;

class BundleTest extends WebTestCase
{
    public function testInitBundle(): void
    {
        $client = static::createClient();

        $container = $client->getContainer();

        $this->assertTrue($container->has('breadcrumbs'));
        $this->assertInstanceOf(Breadcrumbs::class, $container->get('breadcrumbs'));

        $this->assertTrue($container->has('breadcrumbs.helper'));
        $this->assertInstanceOf(BreadcrumbsHelper::class, $container->get('breadcrumbs.helper'));
    }

    public function testRendering(): void
    {
        $client = static::createClient();

        $container = $client->getContainer();

        /** @var \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs $service */
        $service = $container->get(Breadcrumbs::class);
        $service->addItem('foo');

        /** @var \Xepozz\BreadcrumbsBundle\Twig\Extension\BreadcrumbsExtension $breadcrumbsExtension */
        $breadcrumbsExtension = $container->get('breadcrumbs.twig');

        $this->assertSame(
            '<ol id="wo-breadcrumbs" class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList"><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><span itemprop="name">foo</span><meta itemprop="position" content="1" /></li></ol>',
            $breadcrumbsExtension->renderBreadcrumbs()
        );
        $script = <<<SCRIPT
<script type="application/ld+json">
{
    "@context": "http://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement":
    [
        {
            "@type": "ListItem",
            "position": 1,
            "item":
            {
                "@id": "",
                "name": "foo"
            }
        }
    ]
}
</script>
SCRIPT;

        $this->assertSame(
            $script,
            $breadcrumbsExtension->renderBreadcrumbsSchema()
        );
    }

    public static function getKernelClass()
    {
        return TestKernel::class;
    }
}
