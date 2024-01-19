import { token } from 'Vendor/rapidez/core/resources/js/stores/useUser'
import { mask } from 'Vendor/rapidez/core/resources/js/stores/useMask'

document.addEventListener('turbo:load', () => {
    async function placeOrder() {
        window.pay_cart_id ??= mask.value;

        let options = {
            headers: {
                'Store': window.config.store_code
            }
        }

        if (token.value) {
            options['headers']['Authorization'] = `Bearer ${token.value}`
        }


        if (token.value && !isNaN(window.pay_cart_id)) {
            // Get a cart mask since we don't have a proper one
            window.pay_cart_id = await axios.post(config.magento_url + '/graphql', {
                query: `query { customerCart { id } }`
            }, options).then(response => {
                return response.data.data.customerCart.id
            })
        }

        if (!token.value && window.app.guestEmail) {
            await axios.post(config.magento_url + '/graphql', {
                query: `mutation setGuestEmailOnCart($cart_id: String!, $email: String!) {
                    setGuestEmailOnCart(input: {
                        cart_id: $cart_id
                        email: $email
                    }) {
                        cart {
                            email
                          }
                      }
                  }`,
                variables: {
                    cart_id: window.pay_cart_id,
                    email: window.app.guestEmail
                }
            }, options)
        }

        await axios.post(config.magento_url + '/graphql', {
            query:
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
            variables: {
                cart_id: window.pay_cart_id,
                code: window.app.checkout.payment_method,
                pay_issuer: window.app.checkout.pay_issuer
            }
        }, options)

        await axios.post(
            config.magento_url + '/graphql',
            {
                query: `mutation payPlaceOrder($cart_id: String!, $pay_return_url: String, $pay_send_increment_id: Boolean) {
                    placeOrder(
                      input: {
                          cart_id: $cart_id,
                          pay_return_url: $pay_return_url,
                          pay_send_increment_id: $pay_send_increment_id
                      }
                    ){
                      order {
                        pay_redirect_url
                      }
                    }
                  }`,
                variables: {
                    'cart_id': window.pay_cart_id,
                    'pay_return_url': window.url('/paynl/finish'),
                    'pay_send_increment_id': true
                }
            },
            options
        ).then(response => {
            if (response?.data?.errors) {
                throw new axios.AxiosError('Graphql Errors', null, response.config, response.request, response)
            }

            if (response?.data?.data?.placeOrder?.order?.pay_redirect_url) {
                window.location.replace(response.data.data.placeOrder.order.pay_redirect_url)
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
