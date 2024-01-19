import './eventlisteners'

document.addEventListener('turbo:load', (event) => {
    Vue.set(window.app.checkout, 'pay_issuer', null)
});
