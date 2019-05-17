# LuckyCart integration module for Magento 2

## Installation

The extension must be installed via `composer`. To proceed, run these commands in your terminal:

```
composer require luckycart/magento2
php bin/magento module:enable Yuukoo_Luckycart
php bin/magento setup:upgrade 
php bin/magento setup:di:compile 
php bin/magento setup:static-content:deploy 
```

## Update

To update the extension to the latest available version (depending on your `composer.json`), run these commands in your terminal:

```
composer update luckycart/magento2 --with-dependencies
php bin/magento setup:di:compile
php bin/magento setup:upgrade
php bin/magento setup:static-content:deploy
```

#### What to do when the module does not get updated

Sometimes, running `composer update` does not actually update the module to the desired version, for example because it does not match the [version constraints](https://getcomposer.org/doc/articles/versions.md#versions-and-constraints) specified in your `composer.json` (run this command to know the exact reason why: `composer prohibits luckycart/magento2`).

When this happens, simply adapt your `composer.json` to point to the new version you want to install, before re-running the update commands (and if that would still not be sufficient, do not hesitate to [post an issue](https://github.com/luckycart/magento2/issues/new) so that we can have a look at it).

## Maintenance mode

You may want to enable the maintenance mode when installing or updating the module, __especially when working on a production website__. To do so, run the two commands below before and after running the other setup commands:

```
php bin/magento maintenance:enable
# Other setup commands
php bin/magento maintenance:disable
```

## Uninstallation

To uninstall the extension from your system, run the following command in your terminal:

```
magento module:uninstall Yuukoo_Luckycart
```

Your website will be switched automatically to maintenance mode during uninstallation and took out of it after.
For more information about uninstalling modules please refer to [Magento Documentation](https://devdocs.magento.com/guides/v2.3/install-gde/install/cli/install-cli-uninstall-mods.html)

