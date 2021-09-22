<script>
    import GetCart from './../../../../../core/resources/js/components/Cart/mixins/GetCart'

    export default {
        mixins: [GetCart],

        render() {
            return this.$scopedSlots.default()
        },

        created() {
            this.clearCart()
        }
    }
</script>
