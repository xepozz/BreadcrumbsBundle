<?php

namespace WhiteOctober\BreadcrumbsBundle\Twig\Extension;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use WhiteOctober\BreadcrumbsBundle\Model\SingleBreadcrumb;

/**
 * Provides an extension for Twig to output breadcrumbs
 */
class BreadcrumbsExtension extends AbstractExtension
{
    protected $container;
    protected $breadcrumbs;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->breadcrumbs = $container->get('white_october_breadcrumbs');
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
     * @return \WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function getBreadcrumbs($namespace = Breadcrumbs::DEFAULT_NAMESPACE)
    {
        return $this->breadcrumbs->getNamespaceBreadcrumbs($namespace);
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function hasBreadcrumbs($namespace = Breadcrumbs::DEFAULT_NAMESPACE)
    {
        return $this->breadcrumbs->hasNamespaceBreadcrumbs($namespace);
    }

    /**
     * Renders the breadcrumbs in a list
     *
     * @param array $options
     * @return string
     */
    public function renderBreadcrumbs(array $options = [])
    {
        return $this->container->get("white_october_breadcrumbs.helper")->breadcrumbs($options);
    }

    /**
     * Checks if this breadcrumb is the last one in the collection
     *
     * @param SingleBreadcrumb $crumb
     * @param string $namespace
     * @return bool
     */
    public function isLastBreadcrumb(SingleBreadcrumb $crumb, $namespace = Breadcrumbs::DEFAULT_NAMESPACE)
    {
        $offset = $this->breadcrumbs->count($namespace) - 1;

        return $crumb === $this->breadcrumbs->offsetGet($offset, $namespace);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return "breadcrumbs";
    }
}
