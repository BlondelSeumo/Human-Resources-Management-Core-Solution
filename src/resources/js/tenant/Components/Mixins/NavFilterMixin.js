export default {
    methods: {
        setFilterValues(filterValue) {
            this.$store.dispatch('updateFilterObject', filterValue)
        },
        setSearchValue(search) {
            this.$store.dispatch('updateFilterObject', {search})
        },
        setPagination(page) {
            this.$store.dispatch('updateFilterObject', {page})
        },
    }
}