<?php

namespace Rapidez\Paynl\View\Composers;

use Illuminate\View\View;

class PaymentMethodComposer
{
    /**
     * Create a new profile composer.
     */
    public function __construct() {}

    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->getFactory()->startPush('payment_methods', view('paynl::pin-payment')->render());
    }
}
