import './eventlisteners'

document.addEventListener('vue:loaded', (event) => {
   window.app.config.globalProperties.custom.pay_issuer = window.app.config.globalProperties.custom?.pay_issuer || null;
});
