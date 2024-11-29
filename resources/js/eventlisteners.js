import { token } from 'Vendor/rapidez/core/resources/js/stores/useUser'
import { mask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

document.addEventListener('vue:loaded', () => {
    async function placeOrder() {
        if (!token.value && window.app.guestEmail) {
            await window.magentoGraphQL(
                `mutation setGuestEmailOnCart($cart_id: String!, $email: String!) {
                    setGuestEmailOnCart(input: {
                        cart_id: $cart_id
                        email: $email
                    }) {
                        cart {
                            email
                        }
                    }
                }`,
                {
                    cart_id: mask.value,
                    email: window.app.guestEmail
                }
            )
        }

        await window.magentoGraphQL(
            `mutation setPaymentMethodOnCart($cart_id: String!, $code: String!, $pay_issuer: String) {
                setPaymentMethodOnCart(input: {
                    cart_id: $cart_id
                    payment_method: {
                        code: $code,
                        pay_issuer: $pay_issuer
                    }
                }) {
                    cart {
                      selected_payment_method {
                        code
                      }
                    }
                  }
            }`,
            {
                cart_id: mask.value,
                code: window.app.checkout.payment_method,
                pay_issuer: window.app.checkout.pay_issuer
            }
        )

        await window.magentoGraphQL(
                `mutation payPlaceOrder($cart_id: String!, $pay_return_url: String, $pay_send_increment_id: Boolean) {
                    placeOrder(
                      input: {
                          cart_id: $cart_id,
                          pay_return_url: $pay_return_url,
                          pay_send_increment_id: $pay_send_increment_id
                      }
                    ) {
                        order {
                            pay_redirect_url
                        }
                    }
                }`,
                {
                    'cart_id': mask.value,
                    'pay_return_url': window.url('/paynl/finish'),
                    'pay_send_increment_id': true
                }
        ).then(response => {
            if (response?.data?.placeOrder?.order?.pay_redirect_url) {
                window.location.replace(response.data.placeOrder.order.pay_redirect_url)
            }
        })
    }

    window.app.$on('before-checkout-payment-saved', (data) => {
        if (!data.order.payment_method_code.includes('paynl_')) {
            return;
        }
        window.app.checkout.preventOrder = true
        window.app.checkout.doNotGoToTheNextStep = true

        placeOrder(data);
    });
})
