export const ChartMixin = {
    props: {
        data: {
            default: null
        }
    },
    computed: {
        darkMode() {
            return this.$store.state.theme.darkMode;
        }
    },
};
