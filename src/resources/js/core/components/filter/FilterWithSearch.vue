<template>
    <div class="d-flex justify-content-between">
        <div>
            <div v-if="options.showFilter || isUndefined(options.showFilter)"
                 class="filters-wrapper d-flex justify-content-start flex-wrap">

                <!--Open Filters Button For Mobile-->
                <button
                    class="btn d-block d-sm-none btn-toggle-filters"
                    type="button"
                    @click.prevent="toggleFilters">
                    <app-icon :name="'filter'"/>
                    {{ $t('filters') }}
                </button>

                <span
                    v-show="isFiltersOpen"
                    class="mobile-filters-wrapper">
                    <app-filter
                        :table-id="id"
                        :filters="options.filters"
                        @get-values="getFilterValues"
                    />

                    <button type="button"
                            class="btn btn-primary btn-with-shadow d-sm-none btn-close-filter-wrapper d-flex justify-content-center align-items-center"
                            @click="toggleFilters">
                        {{ $t('close') }}
                    </button>
                </span>
            </div>
        </div>
        <div>
            <div v-if="options.showSearch || isUndefined(options.showSearch)"
                 class="mr-0 single-filter single-search-wrapper">
                <div class="form-group form-group-with-search d-flex align-items-center">
                    <app-search @input="getSearchValue"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "FilterWithSearch",
    extends: CoreLibrary,
    data() {
        return {
            isFiltersOpen: true,
        }
    },
    props: {
        id: String,
        options: {
            type: Object,
            require: true
        }
    },
    mounted() {
        this.isFiltersActive();
        window.onresize = () => {
            this.isFiltersActive();
        };
    },
    methods: {
        toggleFilters() {
            this.isFiltersOpen = !this.isFiltersOpen;
        },
        isFiltersActive() {
            this.isFiltersOpen = window.innerWidth > 575;
        },
        getFilterValues(value) {
            this.$emit('filteredValue', value);
        },
        getSearchValue(value) {
            this.$emit('searchValue', value);
        }
    }
}
</script>

