# Rapidez PayNL

## Requirements

You need to have the [PayNL Magento 2 module](https://github.com/paynl/magento2-plugin) and the [PayNL Magento 2 GraphQL module](https://github.com/paynl/magento2-graphql) installed and configured within your Magento 2 installation.

## Installation

```bash
composer require rapidez/paynl
```

### Configuration

Because the payment endpoints require authentication we use [justbetter/laravel-magento-client](https://github.com/justbetter/laravel-magento-client)
The folowing .env variables need to be set:

```env
MAGENTO_BASE_URL=${MAGENTO_URL}
# See: https://developer.adobe.com/commerce/webapi/get-started/authentication/gs-authentication-token/#integration-tokens
MAGENTO_ACCESS_TOKEN=
```

By default the access token is used as bearer token, to use oauth for this instead see the [authentication section](https://github.com/justbetter/laravel-magento-client#authentication)

Only the `Paynl_Payment::graphql_capture` (GraphQL - Capture) resource access is required.

### Views

You can publish the views with:
```bash
php artisan vendor:publish --provider="Rapidez\Paynl\PaynlServiceProvider" --tag=views
```

### Pin payments

If you want to enable the pin terminal dropdown you will have to add the following snippet

```
pay_issuers {
    id
    name
}
```

to the `available_payment_methods` field in `rapidez/core/resources/views/cart/queries/fragments/cart.graphql`

## License

GNU General Public License v3. Please see [License File](LICENSE) for more information.
