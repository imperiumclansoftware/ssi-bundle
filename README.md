# Imperium Clan Software - Ssi Bundle

Symfony bundle for extend security and logging

## Installation


Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
composer require ics/ssi-bundle
```

### Applications that don't use Symfony Flex

#### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require ics/ssi-bundle
```

#### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    ICS\SsiBundle\SsiBundle::class => ['all' => true],
];
```

#### Step 3: Adding bundle routing

Add routes in applications `config/routes.yaml`

```yaml
# config/routes.yaml

# ...
ssi_bundle:
    resource: '@SsiBundle/config/routes.yaml'
    prefix: /ssi
# ...
```

#### Step 4: Install Database

For install database :

```bash
# Installer la base de données

php bin/console doctrine:schema:create

```

For update database :

```bash
# Mise a jour la base de données

php bin/console doctrine:schema:update -f

```

### Adding bundle to [EasyAdmin](https://symfony.com/doc/current/bundles/EasyAdminBundle/index.html)

#### Step 1: Add entities to dashboard

Add this MenuItems in your dashboard `Controller/Admin/DashboardController.php`

```php
    // Controller/Admin/DashboardController.php
    use ICS\SsiBundle\Entity\Account;
    use ICS\SsiBundle\Entity\Log;

    class DashboardController extends AbstractDashboardController
    {
        public function configureMenuItems(): iterable
        {
            // ...
            yield MenuItem::section('Security', 'fa fa-shield');
            yield MenuItem::linkToCrud('Accounts', 'fa fa-user-circle', Account::class);
            yield MenuItem::linkToCrud('Logs', 'fa fa-newspaper', Log::class);
            // ...
        }
    }
```

#### Step 2: Add twig widgets to dashboard

```twig
    {# templates/admin/dashboard.html.twig #}

    {% extends "@EasyAdmin/page/content.html.twig" %}

    {% block page_content %}

        {% include "@Ssi/admin/logs.html.twig" %}

    {% endblock %}

```

## Install bundle fixtures


```bash
# Every data in database will destruct

php bin/console doctrine:fixture:load

```

The Passwords for created users are :

- admin : `adminPassword`
- user[1~10] : `userPassword`

## Log Entity

For log an entity just add `@Log` Annotation on entity declaration
you must define the `actions` and `property` properties

value for `actions` can :

- "add" _On add entity in database_
- "update" _On update entity in database_
- "delete" _On delete entity in database_
- "all" _On all action of entity in database_

for `property` make a property than return the log message you want

```php

    use Doctrine\ORM\Mapping as ORM;
    use ICS\SsiBundle\Annotation\Log;
    /**
     * @ORM\Entity()
     * @ORM\Table()
     * @Log(actions={"all"},property="logMessage")
     */
    class Account implements UserInterface
    {
        /**
         * @ORM\Column(type="string", length=180, unique=true)
         */
        private $username;

        public function getLogMessage()
        {
            return $this->username.' (#'.$this->getId().')';
        }

    }
```
