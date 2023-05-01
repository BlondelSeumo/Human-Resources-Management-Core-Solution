<template>
    <ul class="nav mb-primary tab-filter-menu">
        <li class="nav-item" v-for="(item, index) in filterOption" :key="index">
            <a href="#"
               class="nav-link py-0"
               :class="[value == item.id ? 'active' : index === 0 && value === '' ? 'active': '']"
               @click="getFilterValue(item.id)">
                {{ item.value }}
            </a>
        </li>
    </ul>
</template>

<script>
    import CoreLibrary from "../../helpers/CoreLibrary";

    export default {
        name: "TabFilter",
        extends: CoreLibrary,
        props: {
            filterOption: {
                type: Array,
                required: true
            },
            filterKey: {
                type: String
            }
        },
        data() {
            return {
                value: ''
            }
        },
        methods: {
            getFilterValue(item) {
                this.value = item;
                this.$emit('get-value', {'key': this.filterKey, 'value': this.value})
            }
        }
    }
</script>
