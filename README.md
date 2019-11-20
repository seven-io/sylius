<p align="center">
    <a href="https://sms77.io" target="_blank">
        <img src="https://www.sms77.io/wp-content/uploads/2016/12/sms77-logo-onblack-1.png" />
    </a>
</p>

<h1 align="center">Sms77.io API plugin</h1>

<p align="center">
    Programatically send SMS by subscribing to Sylius events.<br>
    Make it even easier for your customers to get informed about order updates.
</p>

## Installation

1. Add to config/bundles.php
    ```Sms77\SyliusPlugin\Sms77SyliusPlugin::class => ['all' => true],```

2. Add to config/routes.yaml
    ```yaml
    sms77_sylius_plugin:
        resource: "@Sms77SyliusPlugin/Resources/config/admin_routing.yml"
    ```

3. Add to config/services.yaml
    ```yaml
    imports:
        - { resource: "@Sms77SyliusPlugin/Resources/config/config.yml" }
    ```

4. Make migrations
    ```php bin/console doctrine:migrations:diff```
    ```php bin/console doctrine:migrations:execute --up XXXXXXXXXXXX```

5. Add configuration fixtures
     ```php bin/console doctrine:fixtures:load --fixtures=vendor/sms77/sylius-plugin/src/DataFixtures/ORM/ConfigurationFixtures.php --append```
     
 A new administration menu entry "Sms77" has been added.
 Check out its content to explore this plugins possibilities.
 
 ### ToDo
 - Add more events
 - Add more translations
 - Add tests