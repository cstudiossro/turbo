# Turbo plugin for Craft CMS 3.x

This plugin utilizes the PageCache filter from yii2 into your craft 3 instance

![Screenshot](resources/img/plugin-logo.png)

<div>Logo partially made by <a href="https://www.flaticon.com/authors/monkik" title="monkik">monkik</a> from <a href="https://www.flaticon.com/" title="Flaticon">www.flaticon.com</a></div>

## Requirements

This plugin requires Craft CMS 3.0.0-beta.23 or later.
This plugins also supports Craft 4.

## Installation

```
composer require cstudios/turbo
```

And add the following lines to your `app.php` or `app.web.php` config file

```php
'components' => [
    'view' => [
        'class' => 'craft\web\View',
        'allowEval' => true
    ]
]
```

You can now exclude urls using the following configuration ( inside your `app.php` file )

```php
'params' => [
    'turbo' => [
        'excludedUrls' => [
            '/index',
            '/channel/*'
        ]
    ]
]
```

You can use wildcarded urls as well with the asterisk (*) character

## Note:

If you have some dynamic contents on your site, you want to be excluded from page caching then you can use the
following code:

```twig
{{ craft.turbo.renderDynamic('csrfInput()') | raw }}
```

To make it easier for you, we've already implemented csrfInput into this plugin, so you just have to use:

```twig
{{ craft.turbo.csrfInput() | raw }}
```

Brought to you by [Gergely Horvath](https://github.com/hunwalk)
<br>
Supported by ❤️ [Cstudios s.r.o.](https://cstudios.sk)
