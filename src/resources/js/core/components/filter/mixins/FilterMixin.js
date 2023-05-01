import {FilterCloseMixin} from "./FilterCloseMixin";

export const FilterMixin = {
    mixins: [FilterCloseMixin],
    props: {
        filterKey: String,
        tableId: String
    },
    data() {
        return {
            isApply: false,
            value: null,
            filterId: null,
        }
    },
    created() {
        this.filterId = `${this.filterKey}-${this.tableId}`;
    },
    methods: {
        returnValue(value) {
            this.$emit('get-value', {'key': this.filterKey, 'value': value})
        }
    }
};
