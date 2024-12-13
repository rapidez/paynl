<?php

namespace Rapidez\Paynl\Actions;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use JustBetter\MagentoClient\Client\Magento;

class CheckSuccessfulOrder
{
    /**
     * @throws RequestException
     */
    public function __invoke(Request $request, Magento $magento)
    {
        $orderId = $request->get('orderId');
        $incrementId = $request->get('incrementId');
        if (empty($orderId)) {
            return true;
        }

        $magento
            ->store(config('rapidez.store_code'))
            ->graphql(
                view('paynl::graphql.capture-transaction')->render(),
                [
                    'pay_order_id' => $orderId,
                    'order_number' => $incrementId
                ]
            );

        $response = $magento
            ->store(config('rapidez.store_code'))
            ->graphql(
                view('paynl::graphql.get-transaction')->render(),
                [
                    'pay_order_id' => $orderId,
                    'order_number' => $incrementId
                ]
            )
            ->throw()
            ->object();

        if (!data_get($response, 'data.paynlGetTransaction.isSuccess', false)) {
            // https://github.com/paynl/magento2-graphql/blob/dcc3df5efceb43f6b8ec2c26833de7c52da0e564/Model/Resolver/RestoreCart.php#L66
            config('rapidez.models.sales_order')::query()
                ->whereHas('sales_order_payments', fn($query) => $query
                    ->where('additional_information->transactionId', $orderId)
                )
                ->with([
                    'quote' => fn($query) => $query
                        ->withoutGlobalScopes()
                ])
                ->first()
                ->quote
                ->update(['is_active' => 1]);

            return false;
        }

        return true;
    }
}
