import './eventlisteners'

document.addEventListener('vue:loaded', (event) => {
   Vue.set(window.app.custom, 'pay_issuer', null)
});
