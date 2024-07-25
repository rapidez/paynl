<?php

namespace Rapidez\Paynl\Listeners\Healthcheck;

use Rapidez\Core\Listeners\Healthcheck\Base;
use Rapidez\Core\Models\OauthToken;

class PaynlHealthcheck extends Base
{
    public function handle()
    {
        $response = [
            'healthy'  => true,
            'messages' => [],
        ];

        if (!config('magento.connections.default.base_url')) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('Your Laravel Magento client base url is missing! don\'t forget to set "MAGENTO_BASE_URL". See: :link', ['link' => 'https://github.com/justbetter/laravel-magento-client#configuration']),
            ];
        }

        if (config('magento.connections.default.authentication_method') === 'token') {
            $response = $this->performTokenMethodHealthChecks($response);
        }

        return $response;
    }

    protected function performTokenMethodHealthChecks(array $response) : array
    {
        if(!config('magento.connections.default.access_token')) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('Your Laravel Magento client access token is missing! don\'t forget to set "MAGENTO_ACCESS_TOKEN". See: :link', ['link' => 'https://github.com/justbetter/laravel-magento-client#authentication']),
            ];
        }

        /** @var OauthToken $oauthModel */
        $oauthModel = config('rapidez.models.oauth_token');
        if (!$oauthModel::query()->where('token', config('magento.connections.default.access_token'))->exists()) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('Your Laravel Magento client access token was not found in the database! Are you sure the integration is created and granted? See: :link', ['link' => 'https://github.com/justbetter/laravel-magento-client#authentication']),
            ];
        }

        $configModel = config('rapidez.models.config');
        if (!$configModel::getCachedByPath('oauth/consumer/enable_integration_as_bearer', 0)) {
            $response['healthy'] = false;
            $response['messages'][] = [
                'type'  => 'error',
                'value' => __('Your Laravel Magento client authentication method is "token", but your Magento settings prevent this! See: :link', ['link' => 'https://github.com/justbetter/laravel-magento-client#authentication']),
            ];
        }

        return $response;
    }
}
