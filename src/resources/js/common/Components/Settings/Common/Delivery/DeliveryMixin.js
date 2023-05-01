export default {
    props: {
        value: {
            type: Object,
            required: true
        },
        errors: {
            type: Object
        }
    },
    data() {
        return {
            delivery: {}
        }
    },
    watch: {
        delivery: {
            handler: function (delivery) {
                this.$emit('input', delivery)
            },
            deep: true
        },
        value: {
            handler: function (value) {
                this.delivery = value;
            },
            immediate: true
        }
    },
    created() {
        this.delivery = {...this.value}
    }
}
