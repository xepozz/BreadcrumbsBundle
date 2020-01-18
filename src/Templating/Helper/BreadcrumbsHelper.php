<?php

namespace Xepozz\BreadcrumbsBundle\Templating\Helper;

use Symfony\Component\Templating\Helper\Helper;
use Twig\Environment;
use Xepozz\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbsHelper extends Helper
{
    /**
     * @var Environment
     */
    protected $templating;
    /**
     * @var Breadcrumbs
     */
    protected $breadcrumbs;
    /**
     * @var array The default options load from config file
     */
    protected $options = [];

    /**
     * @param \Twig\Environment $templating
     * @param \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs $breadcrumbs
     * @param array $options The default options load from config file
     */
    public function __construct(Environment $templating, Breadcrumbs $breadcrumbs, array $options)
    {
        $this->templating = $templating;
        $this->breadcrumbs = $breadcrumbs;
        $this->options = array_merge(
            $options,
            [
                'namespace' => Breadcrumbs::DEFAULT_NAMESPACE, // inject default namespace to options
            ]
        );
    }

    /**
     * Returns the HTML for the namespace breadcrumbs
     *
     * @param array $options The user-supplied options from the view
     * @return string A HTML string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function breadcrumbs(array $options = []): string
    {
        $options = $this->resolveOptions($options);

        // Assign namespace breadcrumbs
        $options['breadcrumbs'] = $this->breadcrumbs->getNamespaceBreadcrumbs($options['namespace']);

        return $this->templating->render($options['viewTemplate'], $options);
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return 'breadcrumbs';
    }

    /**
     * Merges user-supplied options from the view
     * with base config values
     *
     * @param array $options The user-supplied options from the view
     * @return array
     */
    private function resolveOptions(array $options = []): array
    {
        return array_merge($this->options, $options);
    }
}
