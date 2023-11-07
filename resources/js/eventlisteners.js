document.addEventListener('turbo:load', () => {
    window.app.$on('checkout-payment-saved', (data) => {
        if (!data.order.payment_method_code.includes('paynl_')) {
            return;
        }
        window.app.checkout.doNotGoToTheNextStep = true

        const query = {
            query:
            `mutation StartTransaction(
                $order_id: String
                $return_url: String
            ) {
              paynlStartTransaction (
                order_id: $order_id
                return_url: $return_url
              ) {
                redirectUrl
              }
            }`,
            variables: {
                order_id: data.order.id,
                return_url: window.url('/paynl/finish')
            }
        }

        const options = {
            headers: { 'Store': window.config.store_code }
        }

        axios.post(config.magento_url + '/graphql', query, options).then(response => {
            window.location.replace(response.data.data.paynlStartTransaction.redirectUrl)
        })
    });
})
