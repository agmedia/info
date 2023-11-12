<template>
    <div>
        <star-rating v-model="rating"
                     :increment="Number(increment)"
                     :fixed-points="Number(decimals)"
                     :star-size="Number(size)"
                     :active-color="color"
                     :padding="Number(padding)">
        </star-rating>
        <input type="hidden" name="stars" v-model="rating">
    </div>
</template>

<script>
    export default {
        props: {
            value: {
                type: String,
                required: false,
                default: '0'
            },
            increment: {
                type: String,
                required: false,
                default: '0.01'
            },
            decimals: {
                type: String,
                required: false,
                default: '1'
            },
            size: {
                type: String,
                required: false,
                default: '50'
            },
            color: {
                type: String,
                required: false,
                default: '#ffd055'
            },
            padding: {
                type: String,
                required: false,
                default: '0'
            }
        },
        //
        data() {
            return {
                rating: 0
            }
        },
        //
        mounted() {
            this.rating = Number(this.value);
        },
        //
        methods: {
            /**
             *
             */
        }
    };
</script>
<style>
</style>
