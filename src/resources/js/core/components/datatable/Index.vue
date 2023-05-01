<template>
    <div>
        <div v-if="options.datatableWrapper || isUndefined(options.datatableWrapper)"
             class="d-flex justify-content-between">
            <div class="d-flex justify-content-start">
                <div v-if="options.showFilter || isUndefined(options.showFilter)"
                     class="filters-wrapper d-flex justify-content-start flex-wrap">

                    <manage-columns
                        v-if="((options.showManageColumn || isUndefined(options.showManageColumn)) && !isCardView)"
                        :manage-option="columns"
                        :init-option="initColumns"
                        @get-columns-options="getColumnsOptions"
                    />

                    <div v-if="cardView" class="single-filter">
                        <button
                            v-if="!isCardView"
                            :key="'card-view'"
                            class="btn btn-filter btn-list-grid"
                            type="button"
                            @click.prevent="toggleDatatableView">
                            <app-icon :name="options.cardViewIcon ? options.cardViewIcon : 'grid'"/>
                        </button>
                        <button
                            v-else
                            :key="'list-view'"
                            class="btn btn-filter btn-list-grid"
                            type="button"
                            @click.prevent="toggleDatatableView">
                            <app-icon name="align-justify"/>
                        </button>
                    </div>

                    <!--Open Filters Button For Mobile-->
                    <button class="btn d-block d-sm-none btn-toggle-filters"
                            type="button"
                            @click.prevent="toggleFilters">
                        <app-icon :name="'filter'"/>
                        {{ $t('filters') }}
                    </button>

                    <span v-if="!isUndefined(options.filters) && options.filters.length > 0"
                          v-show="isFiltersOpen"
                          class="mobile-filters-wrapper">
                        <app-filter
                            :table-id="id"
                            :filters="options.filters"
                            :options="options"
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

            <div v-if="options.showSearch || isUndefined(options.showSearch)">
                <div class="mr-0 single-filter single-search-wrapper">
                    <app-search @input="getSearchValue"/>
                </div>
            </div>
        </div>

        <table-with-wrapper
            v-if="options.datatableWrapper || isUndefined(options.datatableWrapper)"
            :id="id"
            :options="options"
            :columns="columns"
            :card-view="isCardView"
            :filtered-data="filterForTable"
            :search-value="searchForTable"
            :clear-filter-visible="clearFilterVisible"
            @action="getAction"
            @afterClearFilter="reloadAfterClearFilter"
            :key="'table-with-wrapper'+id"
        />

        <table-without-wrapper
            v-else
            :id="id"
            :options="options"
            :columns="columns"
            :card-view="isCardView"
            :filtered-data="filterForTable"
            :search-value="searchForTable"
            :clear-filter-visible="clearFilterVisible"
            @action="getAction"
            @afterClearFilter="reloadAfterClearFilter"
            :key="'table-without-wrapper'+id"
        />
    </div>
</template>

<script>
import TableWithWrapper from "./TableWithWrapper";
import TableWithoutWrapper from "./TableWithoutWrapper";
import ManageColumns from "./ManageColumns";
import coreLibrary from '../../helpers/CoreLibrary';
import moment from "moment/moment";

export default {
    name: "AppTable",
    components: {
        TableWithWrapper,
        ManageColumns,
        TableWithoutWrapper
    },
    extends: coreLibrary,
    props: {
        id: {
            required: true,
            type: String,
        },
        options: {
            required: true,
            type: Object
        },
        filteredData: Object,
        search: String,

        // for card view
        cardView: {
            type: Boolean,
            default: false
        }
    },
    data() {
        return {
            filterValues: {},
            columns: [],
            isFiltersOpen: true,
            searchValue: '',
            isCardView: false,
            clearFilterVisible: false
        }
    },
    watch: {
        initColumns: function (data) {
            this.columns = data;
        }
    },
    created() {
        this.columns = this.preparedColumn();
        this.isCardView = this.cardView;
        this.setInitFilterValues();
    },
    beforeMount() {
        if(this.options.enableCookie || this.isUndefined(this.options.enableCookie))
            this.getCookies();
    },
    mounted() {
        this.isFiltersActive();
        window.onresize = () => {
            this.isFiltersActive();
        };
        this.setRows();
    },
    computed: {
        filterForTable() {
            let condition = this.options.datatableWrapper || this.isUndefined(this.options.datatableWrapper);
            return condition ? this.filterValues : this.filteredData;
        },
        searchForTable() {
            let condition = this.options.datatableWrapper || this.isUndefined(this.options.datatableWrapper);
            return condition ? this.searchValue : this.search;
        },
        initColumns() {
            return this.preparedColumn();
        }
    },
    methods: {
        /**
         * Set filterValues if set any active or initial values
         * */
        setInitFilterValues() {
            let condition = (this.options.datatableWrapper || this.isUndefined(this.options.datatableWrapper))
                && this.options.filters;
            if (condition) {
                // filtersWP means FilterWithPermission
                let filtersWP = this.options.filters.filter(f => (this.isUndefined(f.permission) || f.permission)),
                    initFilters = filtersWP.filter(item => item.type === 'date' || item.initValue || item.active),
                    filter = {};
                initFilters.forEach((item) => {
                    let key = _.snakeCase(_.lowerCase(item.key))
                    if (item.type === 'date') filter[key] = moment(new Date()).format('YYYY-MM-DD').toString()
                    else filter[key] = item.initValue || item.active;
                })
                this.filterValues = filter;
            }
        },
        checkClearFilterVisibility(filterValues) {
            let activeFilters = Object.values(filterValues).filter(item => {
                if (typeof item === 'object') return !this.isEmpty(item)
                if (typeof item === 'string') return String(item) !== ''
                if (typeof item === 'number') return Boolean(item)
                if (typeof item === 'boolean') return item
                return false
            })
            this.clearFilterVisible = activeFilters.length !== 0;
        },


        /**
         * Reload after clearing all filter values
         * */
        reloadAfterClearFilter() {
            this.setInitFilterValues();
            this.clearFilterVisible = false;
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id);
            });
        },

        /**
         * Emit from manage-columns
         * */
        getColumnsOptions(value) {
            let data = _.cloneDeep(value);
            this.columns = data;
            if(this.options.enableCookie || this.isUndefined(this.options.enableCookie))
                this.setCookies(data);
        },

        /**
         * Emit from filter
         * */
        getFilterValues(values) {
            this.checkClearFilterVisibility(values);
            this.filterValues = {...this.filterValues, ...values};
            this.$emit('getFilteredValues', this.filterValues);
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id);
            });
        },

        /**
         * Emit from search
         * */
        getSearchValue(value) {
            this.searchValue = value;
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id);
            });
        },

        /**
         * For filter options
         * */
        isFiltersActive() {
            this.isFiltersOpen = window.innerWidth > 575;
        },
        toggleFilters() {
            this.isFiltersOpen = !this.isFiltersOpen;
        },

        /**
         * $emit for action
         * @param rowData
         * @param actionObj
         * @param active
         * */
        getAction(rowData, actionObj, active) {
            this.$emit("action", rowData, actionObj, active);
        },

        /**
         * Send selected rows to parents by event
         * */
        setRows() {
            this.$hub.$on('selectedRowsData-' + this.id, (rows, bulkSelected) => {
                this.$emit('getRows', rows, bulkSelected);
            });
        },

        /**
         * Set columns value in cookies
         * */
        setCookies(values) {
            let name = this.id + '-columns',
                data = JSON.stringify(values);
            AppCookie.set(name, data);
        },

        /**
         * Get columns value from cookies
         * */
        getCookies() {
            let name = this.id + '-columns',
                data = JSON.parse(AppCookie.get(name));
            if (data) {
                data.map((item) => {
                    let actualColumn = this.initColumns.find(column => column.title === item.title);
                    if(actualColumn?.modifier)
                        item.modifier = actualColumn.modifier;
                });
                this.columns = data;
            }
        },

        preparedColumn() {
            return this.options.columns.filter(item => !this.isUndefined(item.title)).map(column => {
                return {
                    ...column,
                    isVisible: column.isVisible !== undefined ? column.isVisible : true
                }
            })
        },

        /**
         * Toggle Data List & Card View
         * */
        toggleDatatableView() {
            this.isCardView = !this.isCardView;
            setTimeout(() => {
                this.$hub.$emit('reload-' + this.id, false);
            });
        }
    }
}
</script>
