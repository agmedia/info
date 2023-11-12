<template>
    <button class="btn btn-outline-primary btn-shadow btn-sm d-sm-block d-none" @click="addToCart()" type="button">+<i class="ci-cart fs-base ms-1"></i></button>

</template>

<script>
export default {
    props: {
        id: String,
        available: String
    },

    data() {
        return {
            quantity: 1,
            has_in_cart: false
        }
    },

    mounted() {
        let cart = this.$store.state.storage.getCart();

        for (const key in cart.items) {
            if (this.id == cart.items[key].id) {
                this.has_in_cart = true;
                this.quantity = cart.items[key].quantity;
            }
        }
    },

    methods: {
        add() {
            if (this.has_in_cart) {
                this.updateCart();
            } else {
                this.add();
            }
        },
        /**
         *
         */
        addToCart() {
            let item = {
                id: this.id,
                quantity: this.quantity
            }

            this.$store.dispatch('addToCart', item);
        },

        /**
         *
         */
        updateCart() {
            if (this.available != 'undefined' && this.quantity > this.available) {
                this.quantity = this.available;
            }

            let item = {
                id: this.id,
                quantity: this.quantity
            }
            this.$store.dispatch('updateCart', item);
        },
    }
};
</script>
