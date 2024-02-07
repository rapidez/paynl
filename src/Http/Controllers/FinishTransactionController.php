<?php

namespace Rapidez\Paynl\Http\Controllers;

use Illuminate\Http\Client\RequestException;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class FinishTransactionController extends Controller
{
    /**
     * @throws RequestException
     */
    public function __invoke(Request $request)
    {
        $orderId = $request->get('orderId');
        if (empty($orderId)) {
            return redirect(config('rapidez.paynl.fail_url', 'cart'));
        }

        $url = config('rapidez.magento_url').'/graphql';
        $response = Http::withHeaders(['Store' => config('rapidez.store_code')])
            ->post($url, [
                'query' => view('paynl::graphql.finish-transaction')->render(),
                'variables' => [
                    'pay_order_id' => $orderId
                ]
            ])
            ->throw()
            ->object();

        if (!optional($response->data->paynlFinishTransaction)->isSuccess ?? false) {
            $stateMessage = __(config('rapidez.paynl.state_message.'.$response->data->paynlFinishTransaction->state, 'Payment failed'));
            return redirect(config('rapidez.paynl.fail_url', 'cart'))->with(['notification' => [
                'message' => $stateMessage,
                'type' => 'info',
                'show' => true
            ]]);
        }

        return view('paynl::success');
    }
}
