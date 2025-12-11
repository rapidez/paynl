import { cart } from 'Vendor/rapidez/core/resources/js/stores/useCart'
import { addBeforePaymentMethodHandler, addBeforePlaceOrderHandler, addAfterPlaceOrderHandler } from 'Vendor/rapidez/core/resources/js/stores/usePaymentHandlers'

addBeforePaymentMethodHandler(async function (query, variables, options) {
    if (!variables.code.includes('paynl_') || !window?.app?.config?.globalProperties?.custom?.pay_issuer)
    {
        return [query, variables, options];
    }

    // Add pay_issuers to setPaymentMethodOnCart
    query = config.fragments.cart +
    `

    mutation setPayPaymentMethodOnCart(
        $cart_id: String!,
        $code: String!,
        $pay_issuer: String
    ) {
        setPaymentMethodOnCart(input: {
            cart_id: $cart_id,
            payment_method: {
                code: $code,
                pay_issuer: $pay_issuer
            }
        }) {
            cart { ...cart }
        }
    }`

    variables.pay_issuer = window.app.config.globalProperties.custom.pay_issuer

    return [query, variables, options];
});

addBeforePlaceOrderHandler(async function (query, variables, options) {
    if (!cart.value?.selected_payment_method?.code?.includes('paynl_')) {
        return [query, variables, options];
    }

    // Add pay_return_url to placeorder
    query = config.fragments.order + config.fragments.orderV2 +
    `

    mutation payPlaceOrder($cart_id: String!, $pay_return_url: String) {
        placeOrder(
            input: {
                cart_id: $cart_id,
                pay_return_url: $pay_return_url,
                pay_send_increment_id: true
            }
        ) {
            order {
                ...order
            }
            orderV2 {
                ...orderV2
            }
            errors {
                code
                message
            }
        }
    }`

    variables.pay_return_url = url('/checkout/success');

    return [query, variables, options]
});

addAfterPlaceOrderHandler(async function (response, mutationComponent) {
    mutationComponent.redirectUrl = response?.data?.placeOrder?.order?.pay_redirect_url || mutationComponent.redirectUrl;
});
