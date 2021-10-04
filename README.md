![Sms77 Logo](https://www.sms77.io/wp-content/uploads/2019/07/sms77-Logo-400x79.png "Sms77 Logo")

# Official Sylius Plugin for Sms77.io
Supports sending SMS and making text-to-speech calls.
Programmatically send messages by subscribing to Sylius events.<br>
Send out bulk messages to all of your customers at once.<br>
Make it even easier for your customers to get informed about order updates and shop related information.

## Installation

1. Require the package via composer
    ```composer require sms77/sylius-plugin```

2. Add to config/bundles.php
    ```Sms77\SyliusPlugin\Sms77SyliusPlugin::class => ['all' => true],```

3. Add to config/routes.yaml
    ```yaml
    sms77_sylius_plugin:
        resource: "@Sms77SyliusPlugin/Resources/config/admin_routing.yml"
    ```

4. Add to config/services.yaml
    ```yaml
    imports:
        - { resource: "@Sms77SyliusPlugin/Resources/config/config.yml" }
    ```

5. Make and execute migrations
    ```php bin/console doctrine:migrations:diff```
    ```php bin/console doctrine:migrations:migrate```
     
6. Navigate to Sms77->Configurations and create your first configuration.

### Screenshots
![Write SMS](screenshots/write_sms.png "Write SMS")
![Write Voice](screenshots/write_voice.png "Write Voice")
![Sent SMS](screenshots/sms.png "Sent SMS")
![Sent Voice](screenshots/voice.png "Sent Voice")
![Create Configuration](screenshots/config_edit.png "Create Configuration")
![Configuration Overview](screenshots/config_list.png "Configuration Overview")

### Support

Need help? Feel free to [contact us](https://www.sms77.io/en/company/contact/).

[![MIT](https://img.shields.io/badge/License-MIT-teal.svg)](LICENSE)
