<?php

namespace Xepozz\BreadcrumbsBundle\Model;

use ArrayAccess;
use Countable;
use InvalidArgumentException;
use Iterator;
use Symfony\Component\Routing\RouterInterface;

class Breadcrumbs implements Iterator, ArrayAccess, Countable
{
    public const DEFAULT_NAMESPACE = 'default';

    private $breadcrumbs = [
        self::DEFAULT_NAMESPACE => [],
    ];

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param string $text #TranslationKey
     * @param string $url
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addItem($text, $url = '', array $translationParameters = [], $translate = true): Breadcrumbs
    {
        return $this->addNamespaceItem(self::DEFAULT_NAMESPACE, $text, $url, $translationParameters, $translate);
    }

    /**
     * @param $namespace
     * @param string $text #TranslationKey
     * @param string $url
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addNamespaceItem(
        $namespace,
        $text,
        $url = '',
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        $b = new SingleBreadcrumb($text, $url, $translationParameters, $translate);
        $this->breadcrumbs[$namespace][] = $b;

        return $this;
    }

    /**
     * @param string $text #TranslationKey
     * @param string $url
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function prependItem($text, $url = '', array $translationParameters = [], $translate = true): Breadcrumbs
    {
        return $this->prependNamespaceItem(self::DEFAULT_NAMESPACE, $text, $url, $translationParameters, $translate);
    }

    /**
     * @param $namespace
     * @param string $text #TranslationKey
     * @param string $url
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function prependNamespaceItem(
        $namespace,
        $text,
        $url = '',
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        $b = new SingleBreadcrumb($text, $url, $translationParameters, $translate);
        array_unshift($this->breadcrumbs[$namespace], $b);

        return $this;
    }

    /**
     * @param string $text #TranslationKey
     * @param string $route #Route
     * @param array $parameters
     * @param int $referenceType
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addRouteItem(
        $text,
        $route,
        array $parameters = [],
        $referenceType = RouterInterface::ABSOLUTE_PATH,
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        return $this->addNamespaceRouteItem(
            self::DEFAULT_NAMESPACE,
            $text,
            $route,
            $parameters,
            $referenceType,
            $translationParameters,
            $translate
        );
    }

    /**
     * @param $namespace
     * @param string $text #TranslationKey
     * @param string $route #Route
     * @param array $parameters
     * @param int $referenceType
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addNamespaceRouteItem(
        $namespace,
        $text,
        $route,
        array $parameters = [],
        $referenceType = RouterInterface::ABSOLUTE_PATH,
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        $url = $this->router->generate($route, $parameters, $referenceType);

        return $this->addNamespaceItem($namespace, $text, $url, $translationParameters, $translate);
    }

    /**
     * @param string $text #TranslationKey
     * @param string $route #Route
     * @param array $parameters
     * @param int $referenceType
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function prependRouteItem(
        $text,
        $route,
        array $parameters = [],
        $referenceType = RouterInterface::ABSOLUTE_PATH,
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        return $this->prependNamespaceRouteItem(
            self::DEFAULT_NAMESPACE,
            $text,
            $route,
            $parameters,
            $referenceType,
            $translationParameters,
            $translate
        );
    }

    /**
     * @param $namespace
     * @param string $text #TranslationKey
     * @param string $route #Route
     * @param array $parameters
     * @param int $referenceType
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function prependNamespaceRouteItem(
        $namespace,
        $text,
        $route,
        array $parameters = [],
        $referenceType = RouterInterface::ABSOLUTE_PATH,
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        $url = $this->router->generate($route, $parameters, $referenceType);

        return $this->prependNamespaceItem($namespace, $text, $url, $translationParameters, $translate);
    }

    /**
     * @param array $objects
     * @param string $text #TranslationKey
     * @param string $url
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addObjectArray(
        array $objects,
        $text,
        $url = '',
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        return $this->addNamespaceObjectArray(
            self::DEFAULT_NAMESPACE,
            $objects,
            $text,
            $url,
            $translationParameters,
            $translate
        );
    }

    /**
     * @param $namespace
     * @param array $objects
     * @param string $text #TranslationKey
     * @param string $url
     * @param array $translationParameters
     * @param bool $translate
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addNamespaceObjectArray(
        $namespace,
        array $objects,
        $text,
        $url = '',
        array $translationParameters = [],
        $translate = true
    ): Breadcrumbs {
        foreach ($objects as $object) {
            $itemText = $this->validateArgument($object, $text);
            if ($url !== '') {
                $itemUrl = $this->validateArgument($object, $url);
            } else {
                $itemUrl = '';
            }
            $this->addNamespaceItem($namespace, $itemText, $itemUrl, $translationParameters, $translate);
        }

        return $this;
    }

    public function clear($namespace = '')
    {
        if ($namespace !== '') {
            $this->breadcrumbs[$namespace] = [];
        } else {
            $this->breadcrumbs = [
                self::DEFAULT_NAMESPACE => [],
            ];
        }

        return $this;
    }

    /**
     * @param $object
     * @param string $text #TranslationKey
     * @param string $url
     * @param string $parent
     * @param array $translationParameters
     * @param int $firstPosition
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addObjectTree(
        $object,
        $text,
        $url = '',
        $parent = 'parent',
        array $translationParameters = [],
        $firstPosition = -1
    ): Breadcrumbs {
        return $this->addNamespaceObjectTree(
            self::DEFAULT_NAMESPACE,
            $object,
            $text,
            $url,
            $parent,
            $translationParameters,
            $firstPosition
        );
    }

    /**
     * @param $namespace
     * @param $object
     * @param string $text #TranslationKey
     * @param string $url
     * @param string $parent
     * @param array $translationParameters
     * @param int $firstPosition
     * @return \Xepozz\BreadcrumbsBundle\Model\Breadcrumbs
     */
    public function addNamespaceObjectTree(
        $namespace,
        $object,
        $text,
        $url = '',
        $parent = 'parent',
        array $translationParameters = [],
        $firstPosition = -1
    ): Breadcrumbs {
        $itemText = $this->validateArgument($object, $text);
        if ($url !== '') {
            $itemUrl = $this->validateArgument($object, $url);
        } else {
            $itemUrl = '';
        }
        $itemParent = $this->validateArgument($object, $parent);
        if ($firstPosition === -1) {
            $firstPosition = count($this->breadcrumbs);
        }
        $b = new SingleBreadcrumb($itemText, $itemUrl, $translationParameters);
        array_splice($this->breadcrumbs[$namespace], $firstPosition, 0, [$b]);
        if ($itemParent) {
            $this->addNamespaceObjectTree(
                $namespace,
                $itemParent,
                $text,
                $url,
                $parent,
                $translationParameters,
                $firstPosition
            );
        }

        return $this;
    }

    public function getNamespaceBreadcrumbs($namespace = self::DEFAULT_NAMESPACE): array
    {
        // Check whether requested namespace breadcrumbs is exists
        if (!$this->hasNamespaceBreadcrumbs($namespace)) {
            throw new InvalidArgumentException(
                sprintf(
                    'The breadcrumb namespace "%s" does not exist',
                    $namespace
                )
            );
        }

        return $this->breadcrumbs[$namespace];
    }

    /**
     * @param string $namespace
     * @return bool
     */
    public function hasNamespaceBreadcrumbs($namespace = self::DEFAULT_NAMESPACE): bool
    {
        return isset($this->breadcrumbs[$namespace]);
    }

    /**
     * @param RouterInterface $router
     */
    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function rewind($namespace = self::DEFAULT_NAMESPACE)
    {
        return reset($this->breadcrumbs[$namespace]);
    }

    public function current($namespace = self::DEFAULT_NAMESPACE)
    {
        return current($this->breadcrumbs[$namespace]);
    }

    public function key($namespace = self::DEFAULT_NAMESPACE)
    {
        return key($this->breadcrumbs[$namespace]);
    }

    public function next($namespace = self::DEFAULT_NAMESPACE)
    {
        return next($this->breadcrumbs[$namespace]);
    }

    public function valid($namespace = self::DEFAULT_NAMESPACE)
    {
        return null !== key($this->breadcrumbs[$namespace]);
    }

    public function offsetExists($offset, $namespace = self::DEFAULT_NAMESPACE)
    {
        return isset($this->breadcrumbs[$namespace][$offset]);
    }

    public function offsetSet($offset, $value, $namespace = self::DEFAULT_NAMESPACE)
    {
        $this->breadcrumbs[$namespace][$offset] = $value;
    }

    public function offsetGet($offset, $namespace = self::DEFAULT_NAMESPACE)
    {
        return $this->breadcrumbs[$namespace][$offset] ?? null;
    }

    public function offsetUnset($offset, $namespace = self::DEFAULT_NAMESPACE)
    {
        unset($this->breadcrumbs[$namespace][$offset]);
    }

    public function count($namespace = self::DEFAULT_NAMESPACE)
    {
        return count($this->breadcrumbs[$namespace]);
    }

    private function validateArgument($object, $argument)
    {
        if (is_callable($argument)) {
            return $argument($object);
        }

        $getter = 'get' . ucfirst($argument);
        if (method_exists($object, $getter)) {
            return call_user_func([&$object, $getter], $getter);
        }

        throw new InvalidArgumentException(
            sprintf(
                'Neither a valid callback function passed nor a method with the name %s() is exists',
                $getter
            )
        );
    }
}
