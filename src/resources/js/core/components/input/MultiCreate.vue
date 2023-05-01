<template>
    <div class="dropdown-search-select">
        <div class="search-filter-dropdown" :class="{'disabled': data.disabled}">
            <div class="dropdown dropdown-with-animation" :class="{'chips-dropdown': data.isAnimatedDropdown}">
                <div id="dropdownMenuLink"
                     class="p-2 chips-container custom-scrollbar"
                     data-toggle="dropdown"
                     @click="startNavigation">
                    <app-overlay-loader v-if="data.multiCreatePreloader"/>
                    <template v-else>
                        <span class="chips d-inline-flex align-items-center justify-content-center mr-2 mb-2"
                              v-for="(chip, index) in selectedOptions"
                              :key="index">
                            <span class="mr-3">{{ chip[data.listValueField] }}</span>
                            <span class="delete-chips d-inline-flex align-items-center justify-content-center"
                                  @click.prevent="deleteChips($event, chip.id)">
                                <app-icon name="x" class="size-14"/>
                            </span>
                        </span>
                        <span class="add">+ {{ data.placeholder ? data.placeholder : $t('add') }}</span>
                    </template>
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
                               :autofocus="startNavigation"
                               @keydown.up="navigateUp"
                               @keydown.down="navigateDown"
                               @keydown.enter.prevent="enterSelectedValue">
                    </div>
                    <div class="dropdown-divider my-0"/>
                    <div class="dropdown-search-result-wrapper custom-scrollbar" ref="optionList">
                        <a v-for="(option, index) in options"
                           :key="index"
                           class="dropdown-item"
                           :class="{'active': index === activeIndex, 'disabled selected': isChipSelected(option.id), 'disabled': option.disabled}"
                           href="#" @click.prevent="addChips(option)">
                            {{ option[data.listValueField] }}
                            <span class="check-sign float-right">
                                <app-icon name="check" class="menu-icon"/>
                            </span>
                        </a>
                        <div v-if="!options.length" class="text-center text-muted text-size-13 py-primary">
                            <span v-html="hintText"/>
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
    name: "MultiCreate",
    mixins: [InputMixin, NavigationMixin],
    data() {
        return {
            multiCreateValue: [],
            searchValue: ''
        }
    },
    watch: {
        value: {
            handler: function (value) {
                this.multiCreateValue = Array.isArray(value) ? value : [];
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
            this.multiCreateValue.forEach(item => {
                let tempChip = this.data.list.find(chip => chip.id === item);
                if(tempChip) chipsList.push(tempChip)
            })
            return chipsList;
        },
        hintText() {
            let validText = !this.data.list.length ? this.$t('no_options_found') : (!this.options.length ? this.$t('did_not_match_anything') : '');
            return `${validText} <br/><span class="text-size-13 default-font-color">${this.$t('enter_to_add_new')}</span>`
        }
    },
    methods: {
        enterSelectedValue() {
            if (this.searchValue !== '' && this.activeIndex < 0) {
                if (!this.isUndefined(this.data.storeData))
                    this.data.storeData(this.searchValue);
                this.$emit('storeData', this.searchValue);
                this.searchValue = '';
            }
            this.options.filter((option, index) => {
                if (index === this.activeIndex && (!option.disabled || this.isUndefined(option.disabled))) {
                    this.addChips(option)
                }
            });
            this.endNavigation();
        },
        isChipSelected(value) {
            return this.multiCreateValue.includes(value);
        },
        addChips(chip) {
            this.multiCreateValue.push(chip.id);
            this.changed();
            this.searchValue = '';
        },
        deleteChips(event, id) {
            event.stopPropagation();
            let index = this.multiCreateValue.indexOf(id)
            this.multiCreateValue.splice(index, 1);
            this.changed();
        },
        changed() {
            this.$emit('input', this.multiCreateValue);
        }
    },
    mounted() {
        $('.dropdown').on('hide.bs.dropdown', () => {
            this.searchValue = ''
        });
    }
}
</script>
