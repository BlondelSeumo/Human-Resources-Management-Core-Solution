export const NavigationMixin = {
    data() {
        return {
            navigationStart: false,
            activeIndex: -1
        }
    },
    methods: {
        startNavigation() {
            this.activeIndex = -1;
            this.navigationStart = true;
            setTimeout(() => {
                this.$refs.searchInput.focus();
                this.$refs.optionList.scrollTop = 0;
            }, 300);
        },
        navigateUp() {
            if (this.activeIndex >= 1) {

                this.activeIndex--;
                // this.selectedValue = this.options[this.activeIndex];

                let sH = this.$refs.optionList.scrollHeight;
                let aH = this.$refs.optionList.offsetHeight;
                if (sH > aH) {
                    //need to adjust scroll height
                    let adS = this.activeIndex == 0 ? 0 : this.$refs.optionList.scrollTop - (sH - aH) / (this.options.length);
                    this.$refs.optionList.scrollTo(0, adS)
                }
            }
        },
        navigateDown() {
            if (this.activeIndex < this.options.length - 1 && this.activeIndex > -2) {

                this.activeIndex++;
                // this.selectedValue = this.options[this.activeIndex];

                let sH = this.$refs.optionList.scrollHeight;
                let aH = this.$refs.optionList.offsetHeight;

                if (sH > aH && this.activeIndex > 0) {
                    //need to adjust scroll height
                    let adS = this.activeIndex == this.options.length - 1 ? (sH - aH) : this.$refs.optionList.scrollTop + (sH - aH) / (this.options.length);
                    this.$refs.optionList.scrollTo(0, adS)
                }
            }
        },
        endNavigation() {
            this.activeIndex = -1;
            this.navigationStart = false;
            $(".dropdown-menu").removeClass('show');
        }
    }
};