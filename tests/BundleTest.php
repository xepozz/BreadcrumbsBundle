<?php

namespace WhiteOctober\BreadcrumbsBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use WhiteOctober\BreadcrumbsBundle\Templating\Helper\BreadcrumbsHelper;

class BundleTest extends WebTestCase
{
    public function testInitBundle(): void
    {
        $client = static::createClient();

        $container = $client->getContainer();

        $this->assertTrue($container->has('white_october_breadcrumbs'));
        $this->assertInstanceOf(Breadcrumbs::class, $container->get('white_october_breadcrumbs'));

        $this->assertTrue($container->has('white_october_breadcrumbs.helper'));
        $this->assertInstanceOf(BreadcrumbsHelper::class, $container->get('white_october_breadcrumbs.helper'));
    }

    public function testRendering(): void
    {
        $client = static::createClient();

        $container = $client->getContainer();

        /** @var \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs $service */
        $service = $container->get(Breadcrumbs::class);
        $service->addItem('foo');

        /** @var \WhiteOctober\BreadcrumbsBundle\Twig\Extension\BreadcrumbsExtension $breadcrumbsExtension */
        $breadcrumbsExtension = $container->get('white_october_breadcrumbs.twig');

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
