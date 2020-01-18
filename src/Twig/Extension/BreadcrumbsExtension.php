<?php

namespace Xepozz\BreadcrumbsBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;
use Xepozz\BreadcrumbsBundle\Model\SingleBreadcrumb;

/**
 * Provides an extension for Twig to output breadcrumbs
 */
class BreadcrumbsExtension extends AbstractExtension
{
    protected $container;
    /**
     * @var \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    protected $breadcrumbs;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->breadcrumbs = $container->get('breadcrumbs');
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('wo_breadcrumbs', [$this, 'getBreadcrumbs']),
            new TwigFunction('wo_breadcrumbs_exists', [$this, 'hasBreadcrumbs']),
            new TwigFunction('wo_render_breadcrumbs', [$this, 'renderBreadcrumbs'], ['is_safe' => ['html']]),
            new TwigFunction('wo_render_breadcrumbs_schema', [$this, 'renderBreadcrumbsSchema'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('wo_is_final_breadcrumb', [$this, 'isLastBreadcrumb']),
        ];
    }

    /**
     * Returns the breadcrumbs object
     *
     * @param string $namespace
     * @return array
     */
    public function getBreadcrumbs($namespace = Breadcrumbs::DEFAULT_NAMESPACE): array
    {
        return $this->breadcrumbs->getNamespaceBreadcrumbs($namespace);
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function hasBreadcrumbs($namespace = Breadcrumbs::DEFAULT_NAMESPACE): bool
    {
        return $this->breadcrumbs->hasNamespaceBreadcrumbs($namespace);
    }

    /**
     * Renders the breadcrumbs in a list
     *
     * @param array $options
     * @return string
     */
    public function renderBreadcrumbs(array $options = []): string
    {
        /** @var $helper \Symfony\Component\Templating\Helper\HelperInterface */
        $helper = $this->container->get('breadcrumbs.helper');

        return $helper->breadcrumbs($options);
    }

    /**
     * Renders the breadcrumbs in a list
     *
     * @param array $options
     * @return string
     */
    public function renderBreadcrumbsSchema(array $options = []): string
    {
        /** @var $helper \Symfony\Component\Templating\Helper\HelperInterface */
        $helper = $this->container->get('breadcrumbs.helper');
        $options = array_merge($options, ['viewTemplate' => '@Breadcrumbs/json-ld.html.twig']);

        return $helper->breadcrumbs($options);
    }

    /**
     * Checks if this breadcrumb is the last one in the collection
     *
     * @param SingleBreadcrumb $crumb
     * @param string $namespace
     * @return bool
     */
    public function isLastBreadcrumb(SingleBreadcrumb $crumb, $namespace = Breadcrumbs::DEFAULT_NAMESPACE): bool
    {
        $offset = $this->breadcrumbs->count($namespace) - 1;

        return $crumb === $this->breadcrumbs->offsetGet($offset, $namespace);
    }
}
