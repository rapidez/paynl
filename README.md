# Rapidez PayNL

## Requirements

You need to have the [PayNL Magento 2 module](https://github.com/paynl/magento2-plugin) and the [PayNL Magento 2 GraphQL module](https://github.com/indykoning/magento2-paynl-graphql) installed and configured within your Magento 2 installation.

## Installation

```bash
composer require rapidez/paynl
```

### Views

You can publish the views with:
```bash
php artisan vendor:publish --provider="Rapidez\Paynl\PaynlServiceProvider" --tag=views
```

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
