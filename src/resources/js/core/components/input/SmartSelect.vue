<template>
    <div class="single-filter search-filter-dropdown">
        <div class="dropdown" :class="{'disabled':data.disabled}">
            <a href="#"
               class="btn btn-filter px-3"
               :class="{'applied': value}"
               :id="inputFieldId"
               @click="startNavigation"
               data-toggle="dropdown"
               aria-haspopup="true"
               aria-expanded="false">
                {{ data.label }}
                <span v-if="data.visibleValue">{{ selectedValue }}</span>
                <span class="clear-icon" v-if="value" @click.prevent="clear">
                    <app-icon :name="'x'"/>
                </span>
            </a>

            <div class="dropdown-menu py-0" :class="data.listClass" :aria-labelledby="inputFieldId">
                <div class="btn-dropdown-close d-sm-none">
                    <span class="title">
                        {{ data.label }}
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
                           :class="'form-control '+data.listItemInputClass"
                           :placeholder="$t('search')"
                           v-bind:value="searchValue"
                           @input="getSearchValue($event)"
                           @keydown.down="navigateDown"
                           @keydown.up="navigateUp"
                           @keydown.enter.prevent="enterSelectedValue"
                           :autofocus="startNavigation">
                </div>
                <div class="dropdown-divider my-0"/>
                <div class="dropdown-search-result-wrapper custom-scrollbar" ref="optionList">
                    <a class="dropdown-item"
                       href="#"
                       v-for="(item,index) in options"
                       :class="{'active':index==activeIndex, [data.listItemClass]: !isUndefined(data.listItemClass),'selected':item.id === value, 'disabled':item.disabled}"
                       @click.prevent="changeSelectedValue(item.id)"
                       :key="`${inputFieldId}-${index}`">
                        {{ item[data.listValueField] }}
                        <span class="check-sign float-right">
                            <app-icon name="check" class="menu-icon"/>
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';
import CoreLibrary from "../../helpers/CoreLibrary";
import {FilterCloseMixin} from "../filter/mixins/FilterCloseMixin";
import {NavigationMixin} from "./mixin/NavigationMixin";

export default {
    name: "SmartSelect",
    mixins: [InputMixin, FilterCloseMixin, NavigationMixin],
    extends: CoreLibrary,
    data() {
        return {
            selectedValue: '',
            searchValue: ""
        }
    },
    computed: {
        options() {
            this.activeIndex = -1;
            if (!_.isEmpty(this.searchValue)) {
                return this.data.list.filter(item => {
                    return item[this.data.listValueField].toLowerCase().includes(this.searchValue.toLowerCase());
                });
            } else return this.data.list;
        }
    },
    methods: {
        getSearchValue($event) {
            this.searchValue = $event.target.value;
        },
        changed() {
            this.$emit('input', this.selectedValue);
        },
        changeSelectedValue(value) {
            this.selectedValue = value;
            this.searchValue = "";
            this.navigationStart = false;
            this.changed();
        },
        enterSelectedValue() {
            this.options.filter((item, index) => {
                if (index == this.activeIndex) {
                    this.changeSelectedValue(item.id)
                }
            });
            this.endNavigation();
        },
        clear(event) {
            event.stopPropagation();
            this.selectedValue = '';
            this.changed();
        }
    }
}
</script>

