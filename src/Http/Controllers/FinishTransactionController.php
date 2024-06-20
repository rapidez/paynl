<?php

namespace Rapidez\Paynl\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use JustBetter\MagentoClient\Client\Magento;
use Rapidez\Core\Models\Scopes\IsActiveScope;

class FinishTransactionController extends Controller
{
    /**
     * @throws RequestException
     */
    public function __invoke(Request $request, Magento $magento)
    {
        $orderId = $request->get('orderId');
        $incrementId = $request->get('incrementId');
        if (empty($orderId)) {
            return redirect(config('rapidez.paynl.fail_url'));
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
            config('rapidez.models.sales_order')::where('increment_id', $incrementId)->with(['quote' => fn($builder) => $builder->withoutGlobalScopes()])->first()->quote->update(['is_active' => 1]);

            return redirect(config('rapidez.paynl.fail_url'));
        }

        return view('paynl::success');
    }
}
