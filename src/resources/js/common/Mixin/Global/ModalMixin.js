export default {
    props: {
        value: {
            required: true,
        }
    },
    data() {
        return {
            showModal: false,
            preloader: false
        }
    },
    watch: {
        showModal: function (value) {
            if (!value) {
                this.$emit('input', value)
            }
        }
    },
    created() {
        this.showModal = this.value;
    }
}
