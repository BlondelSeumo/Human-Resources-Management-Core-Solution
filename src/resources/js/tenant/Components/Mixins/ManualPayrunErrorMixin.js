export default {
    data() {
        return {
            errors: {}
        }
    },
    computed: {
        validationError(){
            return this.$store.state.payrun.payrunErrors;
        },
    },
    watch: {
        validationError: {
            handler: function (errors) {
                if (!errors) {
                    this.errors = {}
                }
                this.errors = errors
            },
        }
    },
}