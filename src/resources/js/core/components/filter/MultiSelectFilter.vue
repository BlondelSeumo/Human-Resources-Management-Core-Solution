<template>
    <div class="single-filter multi-select-filter search-filter-dropdown">
        <div class="dropdown dropdown-with-animation chips-dropdown">
            <a href="#"
               class="btn btn-filter px-3"
               :class="{'applied': value.length}"
               data-toggle="dropdown"
               @click="startNavigation"
               aria-haspopup="true"
               aria-expanded="false">
                {{ label ? label : $t('multi_select') }}
                <span class="clear-icon" v-if="value.length" @click.prevent="clearAndApply">
                        <app-icon :name="'x'"/>
                </span>
            </a>
            <div class="dropdown-menu py-0" aria-labelledby="dropdownMenuLink">
                <div class="btn-dropdown-close d-sm-none">
                        <span class="title">
                            {{ label ? label : $t('multi_select') }}
                        </span>
                    <span class="back float-right" @click.prevent="closeDropDown">
                            <app-icon name="x"/>
                        </span>
                </div>
                <div class="form-group form-group-with-search">
                        <span class="form-control-feedback">
                            <app-icon name="search"/>
                        </span>

                    <input type="text"
                           ref="searchInput"
                           :class="'form-control'"
                           v-bind:value="searchValue"
                           @input="getSearchValue($event)"
                           :placeholder="$t('search')"
                           @keydown.down="navigateDown"
                           @keydown.up="navigateUp"
                           @keydown.enter.prevent="enterSelectedValue"
                           :autofocus="startNavigation">
                </div>

                <div class="dropdown-divider my-0"/>
                <div class="dropdown-search-result-wrapper custom-scrollbar" ref="optionList">
                    <a v-for="(option, index) in options"
                       :key="index"
                       class="dropdown-item"
                       :class="{'active':index==activeIndex, 'selected': isChipSelected(option.id), 'disabled':option.disabled}"
                       href="#" @click.prevent="addChips(option)">
                        {{ option[listValueField] }}
                        <span class="check-sign float-right">
                            <app-icon :name="'check'" class="menu-icon"/>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {FilterMixin} from './mixins/FilterMixin';
import {NavigationMixin} from "../input/mixin/NavigationMixin";
import CoreLibrary from "../../helpers/CoreLibrary";
import FilterAction from "./FilterAction";

export default {
    name: "MultiSelectFilter",
    mixins: [FilterMixin, NavigationMixin],
    extends: CoreLibrary,
    components: {FilterAction},
    data() {
        return {
            searchValue: "",
            value: [],
        }
    },
    props: {
        list: {
            type: Array,
            require: true
        },
        label: {
            type: String,
            default: ''
        },
        listValueField: {
            type: String,
            default: 'value'
        },
    },
    computed: {
        options() {
            this.activeIndex = -1;
            if (!_.isEmpty(this.searchValue)) {
                return this.list.filter(option => {
                    return option[this.listValueField].toLowerCase().includes(this.searchValue.toLowerCase());
                });
            } else return this.list;
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.clearAndInitiate();
        });
    },
    methods: {
        enterSelectedValue() {
            this.options.filter((item, index) => {
                if (index == this.activeIndex) {
                    this.addChips(item)
                }
            });
            this.endNavigation();
        },
        getSearchValue($event) {
            this.searchValue = $event.target.value;
        },
        isChipSelected(value) {
            return this.value.includes(value);
        },
        addChips(chip) {
            if (this.value.includes(chip.id)) {
                let selectedTimeIndex = this.value.indexOf(chip.id);
                this.value.splice(selectedTimeIndex, 1);
            } else
                this.value.push(chip.id);
            this.changed();
            this.searchValue = "";
        },
        deleteChips(event, index) {
            event.stopPropagation();
            this.value.splice(index, 1);
            this.changed();
        },
        changed() {
            this.returnValue(this.value.toString());
        },
        clear() {
            this.closeDropDown();
            this.value = [];
        },
        clearAndInitiate() {
            this.clear();
        },
        clearAndApply(e) {
            e.stopPropagation();
            this.clear();
            this.returnValue(this.value.toString());
        },
    }
}
</script>
