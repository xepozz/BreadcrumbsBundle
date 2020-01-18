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

    public static function getKernelClass()
    {
        return TestKernel::class;
    }
}
