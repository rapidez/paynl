document.addEventListener('turbolinks:load', () => {
    window.app.$on('CheckoutPaymentSaved', (data) => {
        if (!data.order.payment_method_code.includes('paynl_')) {
            return;
        }
        window.app.checkout.doNotGoToTheNextStep = true

        axios.post(config.magento_url + '/graphql', {
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
                return_url: window.location.protocol + '//' + window.location.hostname + '/paynl/finish'
            }
        }).then(response => {
            window.location.replace(response.data.data.paynlStartTransaction.redirectUrl)
        })
    });
})

Vue.component('paynl-success', require('./components/Checkout/PaynlSuccess.vue').default)
