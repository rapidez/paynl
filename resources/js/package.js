import './eventlisteners'

document.addEventListener('vue:loaded', (event) => {
    Vue.set(window.app.checkout, 'pay_issuer', null)
});
