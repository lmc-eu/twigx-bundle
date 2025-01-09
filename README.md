TwigX Bundle
=================
[![Latest Stable Version](https://img.shields.io/packagist/v/lmc/twigx-bundle.svg)](https://packagist.org/packages/lmc/twigx-bundle)
[![Coverage Status](https://coveralls.io/repos/github/lmc-eu/twigx-bundle/badge.svg?branch=main)](https://coveralls.io/github/lmc-eu/twigx-bundle?branch=main)

Symfony bundle extending Twig template engine with [JSX]-like markup.

## Requirements
- PHP ^8.1
- Symfony ^5.4 || ^6.4 || ^7.2
- Twig ^3

## Changelog
See [CHANGELOG](./CHANGELOG.md)

## How to install

### Step 1


Download using *composer*

 Install package

```bash
composer require lmc/twigx-bundle
```

### Step 2

Add `TwigXBundle` into bundles (under `all` bundles). If you use Symfony flex, it will be configured automatically.

**bundles.php**

```php
    return [
        ...,
        Lmc\TwigXBundle\TwigXBundle::class => ['all' => true],
    ];
```

### Step 3 (optional)

If you want to change the default settings, create a config

**config/packages/twigx.yml**
```yaml
    # all parameters are optional
    twigx:
        # define one or more paths to expand or overload components (uses glob patterns)
        paths:
            - "%kernel.project_dir%/templates/components"
        paths_alias: 'jobs-ui' # alias for twig paths above (default is 'spirit')
```

## Usage
Now you can use Twig components with HTML-like syntax in your Symfony project. You only need to remember that, unlike in HTML, component tags must always start with a capital letter:

```html
<ComponentName attr="value">Some other content</ComponentName>
  ...
<ComponentName attr="value" />
```

You can pass attributes like this:

```html
<ComponentName
:any="'any' ~ 'prop'" // this return as "any" prop with value "anyprop"
other="{{'this' ~ 'works' ~ 'too'}}"
anotherProp="or this still work"
not-this="{{'this' ~ 'does'}}{{ 'not work' }}" // this returns syntax as plain text but prop with dash work
ifCondition="{{ variable == 'success' ? 'true' : 'false' }}"  // condition can only be written via the ternary operator
jsXCondition={ variable == 'success' ? 'true' : 'false' }  // condition can only be written via the ternary operator
isBoolean={false}  // if value is false
numberValue={11}  // if value is number 11
isOpen  // if no value is defined, it is set to true
>
    Submit
</ComponentName>
```

or pure original implementation

```twig
{% embed "@spirit/componentName.twig" with { props: {
    attr: 'value'
}} %}
    {% block content %}
        Some other content
    {% endblock %}
{% endembed %}
```

### Allowed parenthesis

You can pass variables to props using two syntaxes.
JSX-like syntax uses single `{...}` parentheses or Twig-like syntax that uses `{{...}}` parentheses.
In both cases, there can be a whitespace around the value that is used.
See the examples below.

JSX-like syntax example:

```html
<ComponentName variable={value} anotherVariable={ value } />
```

Twig like syntax example:

```html
<ComponentName variable={{value}} anotherVariable={{ value }} />
```

if you want to extend these components, an example guide is [here](./docs/extendComponents.md).
if you want to contribute, read guide [here](./docs/contribution.md).

[Spirit Design System]: https://github.com/lmc-eu/spirit-design-system
[JSX]: https://reactjs.org/docs/introducing-jsx.html
