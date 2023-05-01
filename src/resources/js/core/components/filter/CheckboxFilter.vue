<template>
    <div class="single-filter checkbox-filter">
        <div class="dropdown keep-inside-clicks-open">
            <button class="btn btn-filter"
                    :class="{'applied': isApply}"
                    :id="filterId"
                    data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                {{ label }}
                <span class="clear-icon" v-if="isApply" @click.prevent="clearAndApply">
                    <app-icon :name="'x'"/>
                </span>
            </button>
            <div class="dropdown-menu" :aria-labelledby="filterId">
                <div class="btn-dropdown-close d-sm-none">
                    <span class="title">
                        {{ label }}
                    </span>
                    <span class="back float-right" @click.prevent="closeDropDown">
                        <app-icon :name="'x'"/>
                    </span>
                </div>
                <div class="dropdown-item">
                    <div class="row">
                        <div class="col-6" v-for="(item, index) in filterOption">
                            <div class="form-group">
                                <div class="customized-checkbox checkbox-default" :class="{'disabled':item.disabled}">
                                    <input type="checkbox"
                                           :id="'input-checkbox-'+item.id"
                                           :disabled="item.disabled"
                                           :checked="checkedValues(item.id)"/>
                                    <label :for="'input-checkbox-'+item.id"
                                           @click.prevent="updateValue(item.id)">
                                        {{ item[listValueField] }}
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider d-none d-sm-block"/>
                <filter-action :is-clear-disabled="isClearDisabled" @apply="applyFilter" @clear="clearAndApply"/>
            </div>
        </div>
    </div>
</template>

<script>
    import {FilterMixin} from './mixins/FilterMixin';
    import CoreLibrary from "../../helpers/CoreLibrary";
    import FilterAction from "./FilterAction";

    export default {
        name: "CheckboxFilter",
        components: {FilterAction},
        mixins: [FilterMixin],
        extends: CoreLibrary,
        props: {
            filterOption: {
                type: Array,
                required: true
            },
            label: {
                type: String,
            },
            listValueField: {
                type: String,
                default: 'value'
            }
        },
        data() {
            return {
                value: [],
                isApply: false
            }
        },
        computed: {
            isClearDisabled() {
                return this.value.length < 1;
            }
        },
        mounted() {
            this.$hub.$on('clearAllFilter-' + this.tableId, () => {
                this.clear();
            });
        },
        methods: {
            checkedValues(item) {
                return this.value.includes(item);
            },
            updateValue(item) {
                if (this.value.includes(item)) {
                    let selectedTimeIndex = this.value.indexOf(item);
                    this.value.splice(selectedTimeIndex, 1);
                } else {
                    this.value.push(item);
                }
            },
            clear() {
                this.removeDropdownShow();
                this.value = [];
                this.isApply = false;
            },
            clearAndApply(e) {
                e.stopPropagation();
                this.clear();
                this.returnValue(this.value.toString());
            },
            applyFilter() {
                if (this.value.length > 0) {
                    this.isApply = true;
                    this.returnValue(this.value.toString());
                }
                this.removeDropdownShow();
                this.closeDropDown();

            }
        }
    }
</script>
