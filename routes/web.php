<?php

Route::middleware('web')->group(function () {
    Route::get('paynl/finish', function () {
        $url = config('rapidez.magento_url').'/graphql';

        $response = Http::post($url, [
            'query' => view('paynl::graphql.finish-transaction')->render(),
            'variables' => [
                'pay_order_id' => request('orderId')
            ]
        ])->throw()->object();

        if (!optional($response->data->paynlFinishTransaction)->isSuccess ?? false) {
            return redirect('cart');
        }

        return view('paynl::success');
    });
});
