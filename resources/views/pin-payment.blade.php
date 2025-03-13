<template v-else-if="method.code === 'paynl_payment_instore' && method?.pay_issuers?.length > 0">
    <x-rapidez::input.radio.base
        name="payment_method"
        v-model="variables.code"
        v-bind:value="method.code"
        v-bind:dusk="'payment-method-'+index"
        v-on:change="mutate"
        required
    />
    <span class="flex items-center text-sm text-neural">
        <span class="ml-2.5">@{{ method.title }}</span>
        <img
            class="absolute right-5 size-8"
            v-bind:src="`/payment-icons/${method.code}.svg`"
            onerror="this.onerror=null; this.src=`/payment-icons/default.svg`"
        >
    </span>
    <div class="ml-2" v-if="variables.code === method.code">
        <x-rapidez::input.select
            name="paynl_issuer"
            v-model="$root.custom.pay_issuer"
            required
            v-on:change="mutate"
        >
            <option v-for="issuer in method.pay_issuers" v-bind:value="issuer.id">@{{ issuer.name }}</option>
        </x-rapidez::input.select>
    </div>
</template>
