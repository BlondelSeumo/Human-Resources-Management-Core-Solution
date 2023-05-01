<template>
    <div class="single-filter radio-filter">
        <div class="dropdown keep-inside-clicks-open">
            <button class="btn btn-filter"
                    :class="{'applied': isApply}"
                    type="button"
                    :id="filterId"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
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
                <div class="dropdown-item pb-0">
                    <h6 v-if="header.title">{{ header.title }}</h6>
                    <p v-if="header.description" class="text-justify mb-0 filter-subtitle-text">
                        {{ header.description }}
                    </p>
                </div>
                <div class="dropdown-item">
                    <div class="d-flex justify-content-start">
                        <label class="custom-control border-switch mb-0 mr-3">
                            <input type="checkbox"
                                   :checked="isApply"
                                   @click="applyToggleFilter($event)"
                                   class="border-switch-control-input">
                            <span class="border-switch-control-indicator"></span>
                        </label>
                        <div class="pt-1">
                            {{ showLabel }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {FilterMixin} from "./mixins/FilterMixin";
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "ToggleFilter",
    mixins: [FilterMixin],
    extends: CoreLibrary,
    props: {
        label: {
            type: String
        },
        header: {
            type: Object
        },
        buttonLabel: {
            type: Object
        }
    },
    computed: {
        showLabel() {
            return this.isApply ? (this.buttonLabel?.active ? this.buttonLabel.active : this.$t('yes'))
                : (this.buttonLabel?.inactive ? this.buttonLabel.inactive : this.$t('no'))
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.clear();
        });
    },
    methods: {
        applyToggleFilter(e) {
            let toggleValue = e.target.checked;
            this.isApply = toggleValue;
            if (!toggleValue) this.removeDropdownShow();
            this.closeDropDown();
            this.returnValue(toggleValue);
        },
        clearAndApply(e) {
            e.stopPropagation();
            this.clear();
            this.returnValue('')
        },
        clear() {
            this.isApply = false;
            this.closeDropDown();
            this.removeDropdownShow();
        }
    }
}
</script>
