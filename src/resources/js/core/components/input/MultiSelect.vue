<template>
    <div class="dropdown-search-select">
        <div class="search-filter-dropdown" :class="{'disabled': data.disabled}">
            <div class="dropdown dropdown-with-animation" :class="{'chips-dropdown': data.isAnimatedDropdown}">
                <div class="p-2 chips-container custom-scrollbar"
                     id="dropdownMenuLink"
                     data-toggle="dropdown"
                     @click="startNavigation">
                    <span class="chips d-inline-flex align-items-center justify-content-center mr-2 mb-2"
                          v-for="(chip, index) in selectedOptions"
                          :key="index">
                        <span class="mr-3">{{ chip[data.listValueField] }}</span>
                        <span class="delete-chips d-inline-flex align-items-center justify-content-center"
                              @click.prevent="deleteChips($event, chip)">
                            <app-icon name="x" class="size-14"/>
                        </span>
                    </span>
                    <span class="add">+ {{ $t("add") }}</span>
                </div>
                <div class="dropdown-menu chips-dropdown-menu py-0"
                     :class="data.listClass"
                     aria-labelledby="dropdownMenuLink">
                    <div class="form-group form-group-with-search">
                        <span class="form-control-feedback">
                            <app-icon name="search"/>
                        </span>
                        <input type="text"
                               ref="searchInput"
                               :class="'form-control '+data.listItemInputClass"
                               v-model="searchValue"
                               :placeholder="data.placeholder"
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
                           :class="{'active': index === activeIndex, 'disabled selected': isChipSelected(option.id), 'disabled': option.disabled}"
                           href="#"
                           @click.prevent="addChips(option)">
                            {{ option[data.listValueField] }}
                            <span class="check-sign float-right">
                                <app-icon :name="'check'" class="menu-icon"/>
                            </span>
                        </a>
                        <div v-if="!options.length" class="text-center text-muted text-size-13 py-primary">
                            {{ hintText }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';
import {NavigationMixin} from "./mixin/NavigationMixin";

export default {
    name: "MultiSelect",
    mixins: [InputMixin, NavigationMixin],
    data() {
        return {
            searchValue: '',
            multiSelectValue: []
        }
    },
    watch: {
        value: {
            handler: function (value) {
                this.multiSelectValue = Array.isArray(value) ? value : [];
            },
            immediate: true
        }
    },
    computed: {
        options() {
            this.activeIndex = -1;
            if (this.searchValue) {
                return this.data.list.filter(option => {
                    return option[this.data.listValueField].toLowerCase().includes(this.searchValue.toLowerCase());
                });
            } else return this.data.list;
        },
        selectedOptions() {
            let chipsList = [];
            this.multiSelectValue.forEach(item => {
                let tempChip = this.data.list.find(chip => chip.id === item);
                if(tempChip) chipsList.push(tempChip)
            })
            return chipsList;
            // return this.data.list.filter(item => this.multiSelectValue.includes(item.id))
        },
        hintText() {
            return !this.data.list.length ? this.$t('no_options_found') : (!this.options.length ? this.$t('did_not_match_anything') : '');
        }
    },
    methods: {
        enterSelectedValue() {
            this.options.filter((option, index) => {
                if (index === this.activeIndex && (!option.disabled || this.isUndefined(option.disabled))) {
                    this.addChips(option)
                }
            });
            this.endNavigation();
        },
        isChipSelected(value) {
            return this.multiSelectValue.includes(value);
        },
        addChips(chip) {
            this.multiSelectValue.push(chip.id);
            this.$emit('addedId', chip.id);
            this.changed();
            this.searchValue = '';
        },
        deleteChips(event, chip) {
            event.stopPropagation();
            let index = this.multiSelectValue.indexOf(chip.id)
            this.multiSelectValue.splice(index, 1);
            this.$emit('deletedId', chip.id);
            this.changed();
        },
        changed() {
            this.$emit('input', this.multiSelectValue);
        }
    },
    mounted() {
        $('.dropdown').on('hide.bs.dropdown', () => {
            this.searchValue = ''
        });
    }
}
</script>

