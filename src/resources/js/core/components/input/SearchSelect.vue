<template>
    <div class="single-filter search-filter-dropdown">
        <div class="dropdown dropdown-with-animation" :class="{'disabled':data.disabled}">
            <div :id="inputFieldId"
                 data-toggle="dropdown"
                 aria-haspopup="true"
                 aria-expanded="false">
                <input type="text"
                       class="form-control cursor-pointer"
                       :placeholder="data.placeholder"
                       :disabled="data.disabled"
                       @click="startNavigation"
                       :value="showValue">
            </div>

            <div class="dropdown-menu py-0 my-1" :class="data.listClass" :aria-labelledby="inputFieldId">
                <div class="form-group form-group-with-search">
                    <span class="form-control-feedback">
                        <app-icon name="search"/>
                    </span>
                    <input type="text"
                           ref="searchInput"
                           :class="'form-control '+data.listItemInputClass"
                           :placeholder="$t('search')"
                           :value="searchValue"
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
                       :class="{'active': index === activeIndex, [data.listItemClass]: !isUndefined(data.listItemClass), 'selected': item.id === value, 'disabled': item.disabled}"
                       @click.prevent="changeSelectedValue(item)"
                       :key="`${inputFieldId}-${index}`">
                        {{ item[data.listValueField] }}
                        <span class="check-sign float-right">
                            <app-icon name="check" class="menu-icon"/>
                        </span>
                    </a>
                    <div v-if="!options.length" class="text-center text-muted text-size-13 py-primary">
                        {{ hintText }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';
import {NavigationMixin} from "./mixin/NavigationMixin";
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "SearchSelect",
    mixins: [InputMixin, NavigationMixin],
    extends: CoreLibrary,
    data() {
        return {
            selectedValue: '',
            searchValue: "",
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
        },
        showValue() {
            let item = this.data.list.filter(item => {
                return item.id == this.value;
            });
            return item.length === 1 ? item[0][this.data.listValueField] : '';
        },
        hintText() {
            return !this.data.list.length ? this.$t('no_options_found') : (!this.options.length ? this.$t('did_not_match_anything') : '');
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
            this.selectedValue = value.id;
            this.searchValue = "";
            this.navigationStart = false;
            this.changed();
        },
        enterSelectedValue() {
            this.options.filter((item, index) => {
                if (index == this.activeIndex) {
                    this.changeSelectedValue(item)
                }
            });
            this.endNavigation();
        },
        clear() {
            this.selectedValue = '';
            this.changed();
        }
    }
}
</script>
