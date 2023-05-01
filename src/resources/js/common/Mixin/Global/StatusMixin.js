import {mapState} from "vuex";

export default {

    computed: {
        ...mapState({
            statuses: state => state.support.statuses
        }),
        statusObjectWatcher() {
            return this.statuses.length
        }
    },
    watch: {
        statusObjectWatcher: {
            handler: function (length) {
                if (length) {
                    this.options.filters.find(({key, option}) => {
                        if (key === 'status') option.push(...this.statuses)
                    })
                }
            },
            immediate: true
        }
    }

}
