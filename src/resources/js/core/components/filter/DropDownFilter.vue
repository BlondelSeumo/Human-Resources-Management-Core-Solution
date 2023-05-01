<template>
    <smart-select
        key="smart-select-filter"
        :data="$props"
        v-model="searchAndSelect"
        @input="changed"
    />
</template>

<script>
import {FilterMixin} from './mixins/FilterMixin';
import SmartSelect from "../input/SmartSelect";

export default {
    name: "DropDownFilter",
    mixins: [FilterMixin],
    components: {SmartSelect},
    props: {
        id: {
            type: String,
            default: 'drop-down-filter'
        },
        list: Array,
        label: {
            type: String,
            default: ""
        },
        listValueField: {
            type: String,
            default: 'value'
        },
        active: {}
    },
    data() {
        return {
            searchAndSelect: null,
            initialActiveId: this.active
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.searchAndSelect = this.initialActiveId ? this.initialActiveId : null;
        });
    },
    methods: {
        changed(value) {
            this.returnValue(value);
        },
    },
    watch: {
        active: {
            handler: function (active) {
                this.searchAndSelect = active;
            },
            immediate: true
        }
    }
}
</script>
