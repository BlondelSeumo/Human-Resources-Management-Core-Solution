<template>
    <div>
        <div class="row">
            <div class="col-6 col-sm-8 col-md-9 col-lg-10 col-xl-10">
                <div v-if="properties.showFilter" class="filters-wrapper d-flex justify-content-start flex-wrap">

                    <!--Open Filters Button For Mobile-->
                    <button class="btn d-block d-sm-none btn-toggle-filters"
                            type="button"
                            @click.prevent="toggleFilters">
                        <app-icon :name="'filter'"/>
                        {{ $t('filters') }}
                    </button>

                    <span v-show="isFiltersOpen"
                          class="mobile-filters-wrapper">
                        <app-filter
                            :table-id="id"
                            :filters="properties.filters"
                            @get-values="getFilterValues"
                        />

                        <button type="button"
                                class="btn btn-primary btn-with-shadow d-sm-none btn-close-filter-wrapper d-flex justify-content-center align-items-center"
                                @click="toggleFilters">
                            {{ $t('close') }}
                        </button>
                    </span>
                    <div v-if="properties.showClearFilter" class="d-flex align-items-center single-filter ml-3">
                        <a v-if="clearFilterVisible"
                           href="#"
                           @click.prevent="clearAllCardViewFilter">
                            {{ $t('clear_all_filters') }}
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-4 col-md-3 col-lg-2 col-xl-2">
                <div v-if="properties.showSearch"
                     class="mr-0 single-filter single-search-wrapper">
                    <div class="form-group form-group-with-search d-flex justify-content-end align-items-center">
                        <app-search @input="getSearchValue"/>
                    </div>
                </div>
            </div>
        </div>

        <card-view
            :id="id"
            :properties="properties"
            :filtered-data="filterValues"
            :search-value="searchValue"
        />
    </div>
</template>

<script>
import CardView from "./CardView";

export default {
    name: "AppCardView",
    components: {CardView},
    data() {
        return {
            filterValues: {},
            isFiltersOpen: true,
            clearFilterVisible: false,
            searchValue: ''
        }
    },
    props: {
        properties: {
            type: Object,
            require: true
        },
        id: {
            type: String,
            require: true
        }
    },
    mounted() {
        this.isFiltersActive();
        window.onresize = () => {
            this.isFiltersActive();
        };
        this.getAction();
        this.selectedTemplateCard();
    },
    methods: {
        /**
         * emit from filter
         * */
        getFilterValues(value) {
            this.filterValues = value;
            if (this.properties.showClearFilter)
                this.checkClearFilterVisibility(value);
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id);
            });
        },

        /**
         * emit from search
         * */
        getSearchValue(value) {
            this.searchValue = value;
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id);
            });
        },

        /**
         * for filter options
         * */
        isFiltersActive() {
            this.isFiltersOpen = window.innerWidth > 575;
        },
        toggleFilters() {
            this.isFiltersOpen = !this.isFiltersOpen;
        },

        /**
         * actions
         * */
        getAction() {
            this.$hub.$on('action-' + this.id, (card, actionObj, active) => {
                this.$emit('action', card, actionObj, active);
            });
        },
        selectedTemplateCard() {
            this.$hub.$on('TemplateCard-' + this.id, (card) => {
                this.$emit('selectedTemplateCard', card);
            });
        },

        /**
         * Clear all filter
         * */

        checkClearFilterVisibility(filterValues) {
            let activeFilters = Object.values(filterValues).filter(item => {
                if (typeof item === 'object') return !_.isEmpty(item)
                if (typeof item === 'string') return String(item) !== ''
                if (typeof item === 'number') return Boolean(item)
                if (typeof item === 'boolean') return item
                return false
            })
            this.clearFilterVisible = activeFilters.length !== 0;
        },

        clearAllCardViewFilter() {
            this.$hub.$emit(`clearAllFilter-${this.id}`);
            this.filterValues = {};
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id);
                this.clearFilterVisible = false;
            });
        }
    }
}
</script>
