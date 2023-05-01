export default {
    watch: {
        darkMode: function () {
            let htmlElement = document.documentElement;

            if (this.darkMode) {
                localStorage.setItem('theme', 'dark');
                htmlElement.setAttribute('theme', 'dark');
            } else {
                localStorage.setItem('theme', 'light');
                htmlElement.setAttribute('theme', 'light');
            }
        }
    },
    mounted() {
        // Check for active theme
        let body = $('body'),
            htmlElement = document.documentElement,
            theme = localStorage.getItem('theme');

        if (theme === 'dark') {
            htmlElement.setAttribute('theme', 'dark');
            this.darkMode = true;
            this.$store.state.theme.darkMode = true;
        } else {
            htmlElement.setAttribute('theme', 'light');
            this.darkMode = false;
            this.$store.state.theme.darkMode = false;
        }
    },
    methods: {
        toggleDarkMode() {
            this.darkMode = !this.darkMode;
            this.$store.state.theme.darkMode = !this.$store.state.theme.darkMode;
        }
    }
}
