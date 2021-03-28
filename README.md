# ssi-bundle
Symfony bundle for extend security and logging

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require ics/ssi-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require ics/ssi-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    ICS\SsiBundle\SsiBundle::class => ['all' => true],
];
```
Adding bundle routing
---------------------

Add routes in applications `config/routes.yaml`

```yaml
# config/routes.yaml

# ...
ssi_bundle:
    resource: '@SsiBundle/config/routes.yaml'
    prefix: /ssi
# ...
```
Adding bundle to EasyAdmin
-------------------

### Step 1: Add entities to dashboard

Add this MenuItems in your dashboard `Controller/Admin/DashboardController.php`

```php
    // Controller/Admin/DashboardController.php

    // ...
    yield MenuItem::section('Security', 'fa fa-shield');
    yield MenuItem::linkToCrud('Users', 'fa fa-users', User::class);
    yield MenuItem::linkToCrud('Logs', 'fa fa-newspaper', Log::class);
    // ...
```
### Step 2: Add twig widgets to dashboard

in progress....
