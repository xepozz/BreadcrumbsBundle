Installation
============

```bash
composer require xepozz/breadcrumbs-bundle
```
    
That's it for basic configuration. For more options check the [Configuration](#configuration) section.

Usage
=====

In your application controller methods:

```php
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller extends AbstractController
{
    public function action(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        // Simple example
        $breadcrumbs->addItem('Home', $urlGenerator->generate('index'));
    
        // Example without URL
        $breadcrumbs->addItem('Some text without link');
    
        // Example with parameter injected into translation "user.profile"
        $breadcrumbs->addItem('Account: %user%', '', ['%user%' => 'Username']);
    }
}
```
 

Then, in your template:

```twig
{{ wo_render_breadcrumbs() }}
```
to render html view of breadcrumbs.

The last item in the breadcrumbs collection will automatically be rendered
as plain text rather than a `<a>...</a>` tag.

The `addItem()` method adds an item to the *end* of the breadcrumbs collection.
You can use the `prependItem()` method to add an item to the *beginning* of
the breadcrumbs collection.  This is handy when used in conjunction with
hierarchical data (e.g. Doctrine Nested-Set).  This example uses categories in
a product catalog:

```php
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller extends AbstractController
{
    public function action(Category $category, Breadcrumbs $breadcrumbs)
    {
        $node = $category;
    
        while ($node) {
            $breadcrumbs->prependItem($node->getName(), '<category URL>');
    
            $node = $node->getParent();
        }
    }
}
```

If you do not want to generate a URL manually, you can easily add breadcrumb items
passing only the route name with any required parameters, using the `addRouteItem()`
and `prependRouteItem()` methods:

```php
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller extends AbstractController
{
    public function action(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        // Pass "_demo" route name without any parameters
        $breadcrumbs->addRouteItem('Demo', '_demo');
    
        // Pass "_demo_hello" route name with route parameters
        $breadcrumbs->addRouteItem('Hello Breadcrumbs', '_demo_hello', [
            'name' => 'Breadcrumbs',
        ]);
    
        // Add "homepage" route link at the start of the breadcrumbs
        $breadcrumbs->prependRouteItem('Home', 'homepage');
    }
}
```

## Schema.org/BreadcrumbList
If you need to render breadcrumbs for [Schema.org/BreadcrumbList](https://schema.org/BreadcrumbList):
```twig
{{ wo_render_breadcrumbs_schema() }}
```


Configuration
=============

The following *default* parameters can be overriden in your `config.yml` or similar:

```yaml
# config/services.yml
white_october_breadcrumbs:
    separator:          '/'
    separatorClass:     'separator'
    listId:             'wo-breadcrumbs'
    listClass:          'breadcrumb'
    itemClass:          ''
    linkRel:            ''
    locale:             ~ # defaults to null, so the default locale is used
    translation_domain: ~ # defaults to null, so the default domain is used
    viewTemplate:       '@WhiteOctoberBreadcrumbs/microdata.html.twig'
```

These can also be passed as parameters in the view when rendering the
breadcrumbs - for example:

```twig
{{ wo_render_breadcrumbs({separator: '>', listId: 'breadcrumbs'}) }}
```

> **NOTE:** If you need more than one set of breadcrumbs on the same page you can use namespaces.
By default, breadcrumbs use the `default` namespace, but you can add more.
To add breadcrumbs to your custom namespace use `addNamespaceItem` / `prependNamespaceItem`
or `addNamespaceRouteItem` / `prependNamespaceRouteItem` methods respectively, for example:

```php
use WhiteOctober\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Controller extends AbstractController
{
    public function action(Breadcrumbs $breadcrumbs, UrlGeneratorInterface $urlGenerator)
    {
        // Simple example
        $breadcrumbs->prependNamespaceItem('subsection', 'Home', $urlGenerator->generate('index'));
    
        // Example without URL
        $breadcrumbs->addNamespaceItem('subsection', 'Some text without link');
    
        // Example with parameter injected into translation "user.profile"
        $breadcrumbs->addNamespaceItem('subsection', $txt, $url, ['%user%' => $user->getName()]);
        
        // Example with route name with required parameters
        $breadcrumbs->addNamespaceRouteItem('subsection', $user->getName(), 'user_show', ['id' => $user->getId()]);
    }
}
```

Then to render the `subsection` breadcrumbs in your templates, specify this namespace in the options:

```twig
{{ wo_render_breadcrumbs({namespace: "subsection"}) }}
```

Advanced Usage
==============

You can add a whole array of objects at once

```php
$breadcrumbs->addObjectArray(array $objects, $text, $url, $translationParameters);
```

```
objects:            array of objects
text:               name of object property or closure
url:                name of URL property or closure
```

Example:

```php
$that = $this;
$breadcrumbs->addObjectArray($selectedPath, 'name', function($object) use ($that) {
    return $that->generateUrl('_object_index', ['slug' => $object->getSlug()]);
});
```

You can also add a tree path

```php
$breadcrumbs->addObjectTree($object, $text, $url = '', $parent = 'parent', array $translationParameters = [], $firstPosition = -1)
```

```
object:             object to start with
text:               name of object property or closure
url:                name of URL property or closure
parent:             name of parent property or closure
firstPosition:      position to start inserting items (-1 = determine automatically)
```

> **NOTE:** You can use `addNamespaceObjectArray` and `addNamespaceObjectTree` respectively
for work with multiple breadcrumbs on the same page.

Overriding the template
=======================

There are two methods for doing this.

1. You can override the template used by copying the
    `Resources/views/microdata.html.twig` file out of the bundle and placing it
    into `templates/WhiteOctoberBreadcrumbs/views`, then customising
    as you see fit. Check the [Overriding bundle templates][1] documentation section
    for more information.

2. Use the `viewTemplate` configuration parameter:
    
    ```twig
    {{ wo_render_breadcrumbs({ viewTemplate: 'templates/breadcrumbs/view.html.twig' }) }}
    ```
   
> **NOTE:** If you want to use the JSON-LD format, there's already an existing template 
at `@WhiteOctoberBreadcrumbs/json-ld.html.twig`. Just set this template as the value for 
`viewTemplate` either in your Twig function call (see Step 2 above) or in your bundle [configuration](#configuration).

[1]: http://symfony.com/doc/current/book/templating.html#overriding-bundle-templates
[2]: https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx
