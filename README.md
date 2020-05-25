![Sms77 Logo](https://www.sms77.io/wp-content/uploads/2019/07/sms77-Logo-400x79.png)

# Sms77.io API Plugin for Sylius
Programatically send SMS by subscribing to Sylius events.<br>
Send out Bulk SMS to all of your customers at once.<br>
Make it even easier for your customers to get informed about order updates and shop related information.

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
     
5. Navigate to Sms77->Configurations and create your first configuration.

### Screenshots
![Write Message](https://user-images.githubusercontent.com/12965261/82817149-c414b680-9e9c-11ea-9a41-337f62835cdb.png)
![Message Overview](https://user-images.githubusercontent.com/12965261/82816566-a8f57700-9e9b-11ea-91b9-4c32882657f0.png)
![Create Configuration](https://user-images.githubusercontent.com/12965261/82817148-c37c2000-9e9c-11ea-987c-d1d9c0bdb179.png)
![Configuration Overview](https://user-images.githubusercontent.com/12965261/82816574-aabf3a80-9e9b-11ea-8a14-f2395e251189.png)
