export default {
    methods: {
        toastAndReload(message, id = null) {
            this.loading = false;
            this.errors = {};
            this.$toastr.s('', message);
            if (id) this.$hub.$emit(`reload-${id}`)
        },
        toastException(data) {
            this.loading = false;
            this.$toastr.e('', data.message);
        },
        generateModalTitle(subject) {
            return this.$fieldTitle(this.selectedUrl ? 'edit' : 'add', subject, true);
        },
        scrollToTop(afterRender = true) {
            const handler = () => window.scroll({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });

            if (afterRender) {
                setTimeout(() => handler())
            } else {
                handler();
            }

        }
    }
}
