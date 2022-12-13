<?php

namespace Rapidez\Paynl\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class FinishTransactionController extends Controller
{
    public function __invoke(Request $request)
    {
        $url = config('rapidez.magento_url').'/graphql';
    
        $response = Http::withHeaders(['Store' => config('rapidez.store_code')])
            ->post($url, [
                'query' => view('paynl::graphql.finish-transaction')->render(),
                'variables' => [
                    'pay_order_id' => $request->get('orderId')
                ]
            ])
            ->throw()
            ->object();
    
        if (!optional($response->data->paynlFinishTransaction)->isSuccess ?? false) {
            return redirect('cart');
        }
    
        return view('paynl::success');
    }
}

