<template>
    <div class="single-filter range-filter-dropdown">
        <div class="dropdown keep-inside-clicks-open">
            <button class="btn btn-filter"
                    :class="{'applied': isApply}"
                    :id="filterId"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false">
                {{ filterLabel }}
                <span class="clear-icon" v-if="isApply" @click.prevent="clear">
                    <app-icon :name="'x'"/>
                </span>
            </button>
            <div class="dropdown-menu" :aria-labelledby="filterId">
                <div class="btn-dropdown-close d-sm-none">
                    <span class="title">
                        {{ filterLabel }}
                    </span>
                    <span class="back float-right" @click.prevent="closeDropDown">
                        <app-icon :name="'x'"/>
                    </span>
                </div>
                <div class="dropdown-item pt-5">
                    <div :id="'slider-'+filterId"/>
                    <div class="rate-status mt-primary d-flex justify-content-center">
                        <div class="min-rate">
                            <p class="text-center">{{ minTitle }}</p>
                            <h5 class="text-center">{{ `${sliderMinRange}${sign ? sign : ''}` }}</h5>
                        </div>
                        <hr>
                        <div class="max-rate">
                            <p class="text-center">{{ maxTitle }}</p>
                            <h5 class="text-center">{{ `${sliderMaxRange}${sign ? sign : ''}` }}</h5>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider d-none d-sm-block"/>

                <filter-action :is-clear-disabled="clearFilterDisabled" @apply="applyFilter" @clear="clear"/>

            </div>
        </div>
    </div>

</template>

<script>
import noUiSlider from 'nouislider/distribute/nouislider.min';
import {FilterMixin} from './mixins/FilterMixin';
import CoreLibrary from "../../helpers/CoreLibrary";
import FilterAction from "./FilterAction";

export default {
    name: "RangeFilter",
    components: {FilterAction},
    mixins: [FilterMixin],
    extends: CoreLibrary,
    props: {
        label: String,
        sign: {
            type: String,
            default: ''
        },
        maxTitle: {
            type: String,
            default: function () {
                return this.$t('maximum_rate')
            }
        },
        minTitle: {
            type: String,
            default: function () {
                return this.$t('minimum_rate')
            }
        },
        maxRange: {
            default: 100
        },
        minRange: {
            default: 0
        }
    },
    data() {
        return {
            sliderMinRange: Number(this.minRange),
            sliderMaxRange: Number(this.maxRange),
            rangeSlider: null,
            clearFilterDisabled: true
        }
    },
    mounted() {
        this.initRangeFilter();
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.resetRangeFilter();
        });
    },
    watch: {
        maxRange: function () {
            this.updateSlider();
        },
        minRange: function () {
            this.updateSlider();
        }
    },
    computed: {
        isFraction() {
            return !((Number(this.minRange) % 1 === 0) && (Number(this.maxRange) % 1 === 0))
        },
        filterLabel() {
            return this.isApply ? `${this.sliderMinRange}${this.sign} - ${this.sliderMaxRange}${this.sign}` : this.label
        }
    },
    methods: {
        initRangeFilter() {
            this.rangeSlider = document.getElementById('slider-' + this.filterId);
            noUiSlider.create(this.rangeSlider, {
                start: [Number(this.minRange), Number(this.maxRange)],
                step: 1,
                connect: true,
                disabled: true,
                range: {
                    min: [Number(this.minRange)],
                    max: [Number(this.maxRange)]
                },
                // tooltips: true
            });

            this.rangeSlider.noUiSlider.on('change', (value) => {
                this.clearFilterDisabled = false;
                this.sliderMinRange = this.isFraction ? value[0] : Math.round(value[0]);
                this.sliderMaxRange = this.isFraction ? value[1] : Math.round(value[1]);
                this.value = {'min': this.sliderMinRange, 'max': this.sliderMaxRange}
            });
        },
        applyFilter() {
            if (this.value) {
                this.isApply = true;
                this.returnValue(this.value);
            }
            this.closeDropDown();
            this.removeDropdownShow();
        },
        resetRangeFilter() {
            this.rangeSlider.noUiSlider.reset();
            this.sliderMinRange = Number(this.minRange);
            this.sliderMaxRange = Number(this.maxRange);
            this.isApply = false;
            this.value = null;
            this.clearFilterDisabled = true;
        },
        updateSlider() {
            this.resetRangeFilter();
            this.rangeSlider.noUiSlider.updateOptions({
                start: [Number(this.minRange), Number(this.maxRange)],
                range: {
                    min: [Number(this.minRange)],
                    max: [Number(this.maxRange)]
                }
            })
        },
        clear(event) {
            event.stopPropagation();
            this.removeDropdownShow();
            this.resetRangeFilter();
            this.returnValue(this.value);
        }
    }
}
</script>
