<template>
    <div class="single-filter radio-filter">
        <div class="dropdown keep-inside-clicks-open">
            <button
                class="btn btn-filter"
                :class="{'applied': isApply}"
                type="button"
                :id="filterId"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false">
                {{ !isUndefined(activeOption) ? activeOption[listValueField] : label }}
                <span class="clear-icon"
                      v-if="isApply"
                      @click.prevent="clearAndApply">
                    <app-icon :name="'x'"/>
                </span>
            </button>
            <div class="dropdown-menu" :aria-labelledby="filterId">
                <div class="btn-dropdown-close d-sm-none">
                    <span class="title">
                        {{ !isUndefined(activeOption) ? activeOption[listValueField] : label }}
                    </span>
                    <span class="back float-right" @click.prevent="closeDropDown">
                        <app-icon :name="'x'"/>
                    </span>
                </div>
                <div v-if="header.title || header.description" class="dropdown-item pb-0">
                    <h6 v-if="header.title">{{ header.title }}</h6>
                    <p v-if="header.description" class="text-justify mb-0 filter-subtitle-text">
                        {{ header.description }}
                    </p>
                </div>
                <div class="dropdown-item">
                    <label
                        class="customized-radio radio-default d-block"
                        v-for="(item,index) in filterOption"
                        :class="{'mb-3': (filterOption.length - 1) !== index, 'disabled':item.disabled}"
                        :key="index"
                        @click.prevent="updateValue(item.id)">
                        <input
                            class="radio-inline"
                            type="radio"
                            name="customRadio"
                            :id="'input-radio-'+item.id"
                            :disabled="item.disabled"
                            :checked="checkedValue(item.id)"
                        />
                        <span class="outside">
                            <span class="inside"/>
                        </span>
                        {{ item[listValueField] }}
                    </label>
                </div>
                <div class="dropdown-divider d-none d-sm-block"/>

                <filter-action
                    :is-clear-disabled="isClearDisabled"
                    @apply="applyFilter"
                    @clear="clearAndApply"
                />
            </div>
        </div>
    </div>
</template>

<script>
import {FilterMixin} from './mixins/FilterMixin';
import CoreLibrary from "../../helpers/CoreLibrary";
import FilterAction from "./FilterAction";

export default {
    name: "RadioFilter",
    mixins: [FilterMixin],
    extends: CoreLibrary,
    components: {FilterAction},
    props: {
        filterOption: {
            type: Array,
            required: true
        },
        label: {
            type: String,
            default: function () {
                return this.$t('select_an_option');
            }
        },
        header: {
            type: Object
        },
        listValueField: {
            type: String,
            default: 'value'
        },
        active: {}
    },
    data() {
        return {
            value: '',
            activeId: null,
            initialActiveId: this.active
        }
    },
    watch: {
        active: {
            handler: function (value) {
                if (value) {
                    this.activeId = value;
                    this.isApply = true;
                    this.value = value;
                }
            },
            immediate: true
        }
    },
    computed: {
        activeOption() {
            return this.filterOption.find((item) => item.id === this.activeId);
        },
        isClearDisabled() {
            return this.value === '';
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.clearAndInitiate();
        });
    },
    methods: {
        clearAndInitiate() {
            this.clearFilter();
            if (this.initialActiveId) {
                this.value = this.initialActiveId;
                this.activeId = this.initialActiveId;
                this.isApply = true;
            }
        },
        clearFilter() {
            this.value = '';
            this.isApply = false;
            this.activeId = null;
            this.removeDropdownShow();
        },
        clearAndApply(e) {
            e.stopPropagation();
            this.clearFilter();
            this.sendFilterValue();
        },
        sendFilterValue() {
            this.returnValue(this.value);
        },
        updateValue(id) {
            this.value = id;
        },
        checkedValue(id) {
            return this.value === id;
        },
        applyFilter() {
            if (this.value) {
                this.isApply = true;
                this.activeId = this.value;
                this.sendFilterValue();
            }
            this.closeDropDown();
            this.removeDropdownShow();
        }
    },
}
</script>
