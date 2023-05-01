<template>
    <div class="datatable">
        <app-overlay-loader v-if="preloader"/>

        <div class="my-2 d-flex justify-content-between"
             v-if="options.showCount || options.showClearFilter || filterByOptions">
            <!--show item count and clear filter-->
            <div class="d-flex align-items-center">
                <p v-if="options.showCount"
                   class="text-muted mb-0">
                    {{ showItemText }}
                </p>
                <p class="text-muted mb-0 mx-2"
                   v-if="options.showCount && options.showClearFilter && clearFilterVisible">-</p>
                <a v-if="options.showClearFilter && clearFilterVisible"
                   href="#"
                   @click.prevent="clearAllFilter">
                    {{ $t('clear_all_filters') }}
                </a>
            </div>

            <!--filter for some keys-->
            <div v-if="filterByOptions"
                 class="dropdown-menu-filter d-flex justify-content-end align-items-center">
                <label class="text-muted mr-2 mb-0">
                    {{ filterByOptions.label ? filterByOptions.label : $t('sort_by') }}
                </label>
                <div class="dropdown keep-inside-clicks-open">
                    <button
                        class="btn btn-filter"
                        type="button"
                        :id="'filter-by-'+id"
                        data-toggle="dropdown">
                        {{ filterByValue }}
                        <img :src="getAppUrl('images/chevron-down.svg')"
                             style="height: 16px; width: 16px" alt="">
                    </button>
                    <div :class="'my-2 dropdown-menu dropdown-menu-sort-by-'+id"
                         :aria-labelledby="'filter-by-'+id">
                        <a v-for="(item,index) in filterByOptions.options"
                           :key="index" href="#"
                           :class="{'active disabled': item === filterByValue}"
                           @click.prevent="setFilterBy(item)"
                           class="dropdown-item">
                            {{ $t(item) }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!--bulk select options selector-->
        <div v-if="visibleBulkSelect"
             class="note note-warning shadow animate__animated animate__fadeIn mb-1">
            <div class="d-flex align-items-center justify-content-center p-3">
                <template v-if="!bulkSelected">
                    <p class="text-muted mb-0">{{ `${selectedRows.length} ${$t('items_selected')}` }}</p>
                    <p class="mx-2 mb-0 text-muted">-</p>
                    <a href="#" @click.prevent="selectBulkOptions">
                        {{ `${$t('select_all')} ${totalRow} ${$t('items')}` }}
                    </a>
                </template>
                <template v-else>
                    <p class="text-muted mb-0">
                        <span class="text-capitalize">{{ $t('all') }}</span>
                        {{ ` ${totalRow} ${$t('items_selected')}` }}
                    </p>
                    <p class="mx-2 mb-0 text-muted">-</p>
                    <a href="#" @click.prevent="clearBulkSelection">
                        {{ $t('clear_selection') }}
                    </a>
                </template>
            </div>
        </div>

        <!-- App Table -->
        <div v-if="!cardView" class="table-responsive custom-scrollbar"
             :class="[
                 {
                 'table-view-responsive': (options.responsive || isUndefined(options.responsive)),
                 'loading-opacity': preloader,
                 'shadow' : (options.tableShadow || isUndefined(options.tableShadow))
                 },
                 options.tablePaddingClass ? options.tablePaddingClass : 'py-primary'
             ]">
            <table class="table mb-0">
                <thead>
                <tr v-if="isUndefined(options.showHeader) || options.showHeader"
                    :key="id+'-table-header'">

                    <!--all select checkbox-->
                    <th v-if="!allColumnDisable && options.enableRowSelect"
                        :key="id+'-customized-checkbox'"
                        class="datatable-th pt-0 pr-0">
                        <div class="customized-checkbox checkbox-default">
                            <input
                                type="checkbox"
                                id="row-select-all"
                                :checked="selectedRows.length === dataSet.length && dataSet.length !== 0"
                                @input="doSelectAllRow($event)"
                            />
                            <label for="row-select-all" class="mb-0"/>
                        </div>
                    </th>

                    <!--Column title-->
                    <th class="datatable-th pt-0"
                        v-for="(column, index) in dataTableColumns"
                        v-if="!isEmpty(column) && (column.isVisible || isUndefined(column.isVisible))"
                        :key="id+'-header-column-'+index"
                        :track-by="index"
                        :class="{'text-right': (column.type === 'action' || column.titleAlignment === 'right')}">
                        <span
                            v-if="column.type === 'action' && (isUndefined(options.showAction) || options.showAction)"
                            :key="id+'column-action-'+index"
                            class="font-size-default">
                            {{ column.title }}
                        </span>
                        <span
                            v-else
                            class="font-size-default"
                            :key="id+'column-normal-'+index">
                            <span
                                v-if="!isUndefined(column.sortable) && column.sortable"
                                :key="id+'column-normal-sortable-'+index"
                                class="btn d-flex justify-content-start cursor-pointer font-weight-bold"
                                @click.prevent="sortByColumn(column, index)">
                                <span>{{ column.title }}</span>
                                <span class="ml-2">
                                    <sort
                                        v-if="isSort"
                                        :icon="column.sortIcon"
                                        :key="`column-sort-${column.key}-${index}`"/>
                                </span>
                            </span>
                            <span
                                v-else
                                :key="id+'column-normal-without-sortable-'+index">
                                {{ column.title }}
                            </span>
                        </span>
                    </th>
                </tr>
                </thead>
                <tbody>
                <template v-if="dataSet.length > 0" v-for="(row, rowIndex) in dataSet">
                    <tr>

                        <!--select row checkbox-->
                        <td v-if="!allColumnDisable && options.enableRowSelect" class="datatable-td pr-0">
                            <div class="customized-checkbox checkbox-default">
                                <input type="checkbox"
                                       :id="'row-select-'+rowIndex"
                                       :checked="includesInSelectedRow(row)"
                                       :value="row.id" @input="doSelectRow($event,row)"/>
                                <label :for="'row-select-'+rowIndex" class="mb-0"/>
                            </div>
                        </td>

                        <!--Datatable column render-->
                        <td v-for="(column, columnIndex) in columns"
                            v-if="!isEmpty(column) && (column.isVisible || isUndefined(column.isVisible))"
                            :key="'column'+rowIndex+columnIndex"
                            :data-title="[(options.responsive || isUndefined(options.responsive)) ? column.title : '']"
                            class="datatable-td"
                            :class="{'text-md-right': column.type === 'action', 'pt-0 border-top-0':
                            (options.showHeader === false && rowIndex === 0), [column.tdClassName]: !isUndefined(column.tdClassName) }">

                            <!-- Link type column -->
                            <span v-if="column.type === 'link'"
                                  :class="{[column.className]: !isUndefined(column.className)}"
                                  :key="'column-link-'+rowIndex+columnIndex">
                                <a :href="getAppUrl(column.modifier(row[column.key], row))">
                                    {{ row[column.key] }}
                                </a>
                            </span>

                            <!-- Image type column -->
                            <span v-else-if="column.type === 'image'"
                                  :key="'column-image-'+rowIndex+columnIndex">
                                <app-avatar :avatar-class="column.className"
                                            :url="row[column.key] ? row[column.key] : column.default"
                                            :alt-text="column.altText"/>
                            </span>

                            <!-- Media object type column -->
                            <span v-else-if="column.type === 'media-object'"
                                  :key="'column-media-object-'+rowIndex+columnIndex">
                                <app-media-object
                                    :img-url="column.modifier(row[column.key], row) ? column.modifier(row[column.key], row) : column.default"
                                    :alt-text="row[column.mediaTitleKey]"
                                    :media-title="row[column.mediaTitleKey]"
                                    :media-subtitle="row[column.mediaSubtitleKey]"
                                />
                            </span>

                            <!-- Component type column -->
                            <span v-else-if="column.type === 'component'"
                                  :key="'column-component-'+rowIndex+columnIndex"
                                  :class="{[column.className]: !isUndefined(column.className)}">
                                <component
                                    :is="column.componentName"
                                    :data="column.data"
                                    :value="row[column.key]"
                                    :row-data="row"
                                    :table-id="id"
                                    :index="columnIndex"
                                />
                            </span>

                            <!-- Custom html type column -->
                            <span v-else-if="column.type === 'custom-html'"
                                  :key="'column-custom-html-'+rowIndex+columnIndex"
                                  v-html="column.modifier(row[column.key], row)">
                            </span>

                            <!-- custom-class type column -->
                            <span v-else-if="column.type === 'custom-class'"
                                  :key="'column-custom-class-'+rowIndex+columnIndex"
                                  :class="column.modifier(row[column.key], row)">
                                {{ row[column.key] }}
                            </span>

                            <!-- object type column -->
                            <span v-else-if="column.type === 'object'" :key="'column-object-'+rowIndex+columnIndex">
                                {{ column.modifier(row[column.key], row) }}
                            </span>

                            <!-- action column -->
                            <span v-else-if="column.type === 'action' && !isUndefined(options.actions)
                                &&(isUndefined(options.showAction) || options.showAction)"
                                  :key="'column-action-'+rowIndex+columnIndex">

                                <dropdown-action
                                    v-if="isUndefined(options.actionType) || options.actionType === 'dropdown'"
                                    :key="options.actionType+columnIndex"
                                    :actions="options.actions"
                                    :unique-key="row[column.key]"
                                    :row-data="row"
                                    @action="getAction"/>

                                <default-action
                                    v-else
                                    :key="options.actionType+columnIndex"
                                    :actions="options.actions"
                                    :unique-key="row[column.key]"
                                    :row-data="row"
                                    @action="getAction"/>
                            </span>

                            <!-- Dynamic content column -->
                            <span v-else-if="column.type === 'dynamic-content'"
                                  :key="'column-dynamic-content-'+rowIndex+columnIndex">
                                <button
                                    v-if="!column.modifier(row[column.key], row).isValue && !getDynamicContentValueByKey('dynamicContent'+activePaginationPage+rowIndex+columnIndex)"
                                    :class="[column.className ? column.className : 'btn btn-primary']"
                                    @click="getDynamicContent('dynamicContent'+activePaginationPage+rowIndex+columnIndex, column.modifier(row[column.key], row))">
                                    <app-icon v-if="column.icon" :name="column.icon"/>
                                    {{ column.label ? column.label : '' }}
                                </button>
                                <span v-else>
                                    {{
                                        column.modifier(row[column.key], row).isValue ?
                                            column.modifier(row[column.key], row).value :
                                            getDynamicContentValueByKey('dynamicContent' + activePaginationPage + rowIndex + columnIndex)
                                    }}
                                </span>
                            </span>

                            <!-- Button type column -->
                            <span v-else-if="column.type === 'button'" :key="'column-button-'+rowIndex+columnIndex">
                                <button v-if="column.modifier(row[column.key], row)!=false"
                                        :class="[column.className ? column.className : 'btn btn-primary']"
                                        @click="getButtonTypeValue(row, column)">
                                        <app-icon v-if="column.icon" :name="column.icon"/>
                                    {{ column.modifier(row[column.key], row) }}
                                </button>
                            </span>

                            <!-- Text type column -->
                            <span v-else-if="column.type === 'text'" :key="'column-normal-'+rowIndex+columnIndex"
                                  :class="{[column.className]: !isUndefined(column.className)}">
                                {{ row[column.key] }}
                            </span>

                            <!-- Expandable column -->
                            <span v-else-if="column.type === 'expandable-column'"
                                  :key="'column-expandable-'+rowIndex+columnIndex"
                                  class="table-expanded-column">
                                <span class="d-inline-flex">
                                    <span v-if="expandableColumn.modifier(row[expandableColumn.key],row).prefixData"
                                          class="m-0 mr-3 d-flex align-items-center">
                                        {{ expandableColumn.modifier(row[expandableColumn.key], row).prefixData }}
                                    </span>
                                    <template v-if="expandableColumn.modifier(row[expandableColumn.key],row).visible
                                    || isUndefined(expandableColumn.modifier(row[expandableColumn.key],row).visible)">
                                        <template v-if="column.modifier(row[column.key],row).button">
                                            <button
                                                v-if="expandableColumn.modifier(row[expandableColumn.key],row).expandable"
                                                type="button"
                                                data-toggle="collapse"
                                                :data-target="'#collapse-'+rowIndex"
                                                @click.prevent="expandableButtonClicked(rowIndex)"
                                                aria-expanded="false"
                                                :key="expandableColumnIcon(rowIndex)"
                                                :aria-controls="'collapse-'+rowIndex"
                                                :class="['btn ', 'btn-'+expandableColumnClassName(row,column)]">
                                                {{ expandableColumnTitle(rowIndex, row) }}
                                                <app-icon
                                                    class="animate__animated animate__fadeIn size-16 ml-2"
                                                    :name="expandableColumnIcon(rowIndex)"
                                                />
                                            </button>
                                        </template>
                                        <template v-else>
                                            <span
                                                :class="['badge rounded-pill', 'badge-'+expandableColumnClassName(row,column)]">
                                                {{ expandableColumnTitle(rowIndex, row) }}
                                            </span>
                                            <template
                                                v-if="expandableColumn.modifier(row[expandableColumn.key],row).expandable
                                                || isUndefined(expandableColumn.modifier(row[expandableColumn.key],row).expandable)">
                                                <button
                                                    class="btn p-0 ml-2"
                                                    data-toggle="collapse"
                                                    :data-target="'#collapse-'+rowIndex"
                                                    @click.prevent="expandableButtonClicked(rowIndex)"
                                                    aria-expanded="false"
                                                    :key="expandableColumnIcon(rowIndex)"
                                                    :aria-controls="'collapse-'+rowIndex">
                                                    <app-icon
                                                        class="animate__animated animate__fadeIn size-16"
                                                        :class="'text-'+expandableColumnClassName(row,column)"
                                                        :name="expandableColumnIcon(rowIndex)"
                                                    />
                                                </button>
                                            </template>
                                        </template>
                                    </template>
                                </span>
                            </span>
                        </td>
                    </tr>

                    <!-- Expandable area -->
                    <tr class="table-expandable-area"
                        v-if="!isUndefined(expandableColumn)
                        && (expandableColumn.modifier(row[expandableColumn.key],row).expandable
                        || isUndefined(expandableColumn.modifier(row[expandableColumn.key],row).expandable))">
                        <td class="datatable-td" :colspan="visibleColumnCount">
                            <div class="collapse" :id="'collapse-'+rowIndex">
                                <component
                                    :is="expandableColumn.componentName"
                                    :data="row"
                                    :table-id="id"
                                />
                            </div>
                        </td>
                    </tr>
                </template>
                </tbody>
            </table>

            <app-empty-data-block
                v-if="(dataSet.length === 0 && !preloader) ||(allColumnDisable)"
                :message="$t('empty_data_block_dummy_message')"
            />
        </div>

        <!-- For card view -->
        <div v-else
             style="min-height: 400px;"
             class="position-relative"
             :class="{'loading-opacity': preloader}">
            <component
                :is="options.cardViewComponent"
                :id="id"
                :data="dataSet"
                :actions="options.actions"
                :data-loader="preloader"
                :filtered-data="filteredData"
                @getCardAction="getAction"/>
            <app-empty-data-block
                v-if="dataSet.length === 0 && !preloader &&
                (options.cardViewEmptyBlock || isUndefined(options.cardViewEmptyBlock))"
                :message="$t('empty_data_block_dummy_message')"/>
        </div>

        <!-- Pagination and Load more component -->
        <div :class="paginationClass">
            <div v-if="options.paginationType === 'pagination' || isUndefined(options.paginationType)">
                <div class="d-flex flex-column flex-md-row"
                     :class="(options.managePagination || isUndefined(options.managePagination))
                     ? 'justify-content-between' : 'justify-content-end'">

                    <!--Show per page functionalities-->
                    <div v-if="dataSet.length > 0 && (totalRow > options.rowLimit) && !allColumnDisable &&
                    (options.managePagination || isUndefined(options.managePagination))"
                         class="dropdown-menu-filter d-flex align-items-center justify-content-center justify-content-md-start">
                        <div class="dropdown keep-inside-clicks-open">
                            <button
                                class="btn btn-filter"
                                type="button"
                                :id="'show-pagination-'+id"
                                data-toggle="dropdown">
                                {{ paginationRowLimit }}
                                <img :src="getAppUrl('images/chevron-down.svg')"
                                     style="height: 16px; width: 16px" alt="">
                            </button>
                            <div :class="'my-2 dropdown-menu dropdown-menu-'+id"
                                 :aria-labelledby="'show-pagination-'+id">
                                <a v-for="(item,index) in showPaginationOptions"
                                   :key="index" href="#"
                                   :class="{'active disabled': item === paginationRowLimit}"
                                   @click.prevent="setPaginationRow(item)"
                                   class="dropdown-item">
                                    {{ item }}
                                </a>
                            </div>
                        </div>
                        <label class="text-muted ml-2 mb-0">{{ $t('items_showing_per_page') }}</label>
                    </div>

                    <app-pagination
                        v-show="showPagination"
                        :total-row="totalRow"
                        :row-limit="paginationRowLimit"
                        @submit="paginationSubmit"
                    />
                </div>

            </div>
            <div v-else class="text-center">
                <app-load-more
                    v-show="showPagination"
                    :preloader="loadMorePreloader"
                    @submit="loadMoreSubmit"/>
            </div>
        </div>
    </div>
</template>

<script>
import DefaultAction from "./DefaultAction";
import DropdownAction from "./DropdownAction";
import Sort from "./helpers/Sort";
import {TableMixin} from "./mixin/TableMixin.js";

export default {
    name: "TableWithoutWrapper",
    components: {
        DefaultAction,
        DropdownAction,
        Sort
    },
    mixins: [TableMixin],
    data() {
        return {
            expandedColumnIndex: null,
            dataSet: [],
            preloader: false,
            loadMorePreloader: false,
            loadMoreDisabled: false,
            totalRow: 0,
            paginationRowLimit: this.options.rowLimit ? this.options.rowLimit : 10,
            activePaginationPage: 1,
            dataOffset: 0,
            sortByKey: null,
            filterValues: {},
            search: null,
            orderBy: this.options.orderBy ? this.options.orderBy : 'desc',
            actionTriggered: false,

            // Dynamic content
            dynamicContents: [],

            // Multiple row select
            selectedRows: [],
            visibleBulkSelect: false,
            bulkSelected: false,

            // Sort by
            dataTableColumns: [],
            isSort: false,

            // Filter By
            filteredBy: '',
        }
    },
    created() {
        this.setPreloader(true);
    },
    mounted() {
        setTimeout(() => {
            // init
            this.dataTableInit();

            // hub
            this.reloadDataTable();
        });
        this.isSort = true;
    },
    watch: {
        'columns': {
            handler: 'setColumns',
            immediate: true
        }
    },
    methods: {
        /**
         * BulkSelect
         * */
        selectBulkOptions() {
            this.bulkSelected = true;
            this.sendSelectedRowData();
        },

        clearBulkSelection() {
            this.selectedRows = [];
            this.visibleBulkSelect = false;
            this.bulkSelected = false;
            this.sendSelectedRowData();
        },

        /**
         * Multiple row select
         * */
        doSelectAllRow(e) {
            let ele = e.target;
            if (ele.checked) {
                this.selectedRows = _.clone(this.dataSet);
                this.visibleBulkSelect = true;
            } else {
                this.selectedRows = [];
                this.visibleBulkSelect = false
            }
            this.bulkSelected = false;
            this.sendSelectedRowData();
        },
        doSelectRow(e, row) {
            let ele = e.target;
            if (ele.checked) {
                this.selectedRows.push(row);
            } else {
                let index = this.selectedRows.indexOf(row);
                this.selectedRows.splice(index, 1);
            }
            this.visibleBulkSelect = false;
            this.bulkSelected = false;
            this.sendSelectedRowData();
        },
        includesInSelectedRow(value) {
            return this.selectedRows.includes(value);
        },
        sendSelectedRowData() {
            this.$hub.$emit('selectedRowsData-' + this.id, this.selectedRows, this.bulkSelected);
        },

        /**
         * Init datable for render
         * */
        dataTableInit() {
            // this.paginationRowLimit = this.options.rowLimit ? this.options.rowLimit : 10;
            this.filterValues = this.filteredData;
            this.search = this.searchValue;
            this.dataSet = [];
            this.dataOffset = 0;
            this.getDataSet();
        },

        /**
         * Get data from database
         * */
        getDataSet() {
            let filter = this.requestParams ? _.cloneDeep(this.filterValues) : {},
                reqData = {},
                url = this.options.url;

            filter.per_page = this.paginationRowLimit;
            filter.sortBy = this.sortByKey;
            filter.page = parseInt(this.activePaginationPage);
            if(this.requestParams) filter.search = this.search;
            filter.orderBy = this.orderBy;

            if (!this.isUndefined(this.options.cardViewQueryParams) && this.cardView)
                filter = {...filter, ...this.options.cardViewQueryParams}
            if (this.filterByOptions)
                filter[this.filterByOptions.key ? this.filterByOptions.key : 'filterBy'] = this.filterByValue

            reqData.params = filter;

            this.setPreloader(true);

            this.axiosGet(
                url,
                reqData
            ).then(response => {

                if (this.options.paginationType === 'pagination' || this.isUndefined(this.options.paginationType))
                    this.configureTableForPagination(response.data);
                else this.configureTableForLoadMore(response.data);

                this.totalRow = response.data.total;
                this.dataOffset = this.dataSet.length;

                // For Bulk options and row selection
                if (this.options.enableRowSelect) {
                    this.selectedRows = [];
                    this.visibleBulkSelect = false;
                    this.bulkSelected = false;
                    this.sendSelectedRowData();
                }

                // send response in root
                if (this.isFunction(this.options.afterRequestSuccess))
                    this.options.afterRequestSuccess(response);

            }).catch(({response}) => {
                // Send error in root
                if (this.isFunction(this.options.afterRequestError))
                    this.options.afterRequestError(response);
            }).finally(() => {
                this.actionTriggered = false;
                this.setPreloader(false);

                // Close other collapse in expandable column
                this.$nextTick(() => {
                    if (!this.isUndefined(this.expandableColumn)) this.expandableColumnDesignModified()
                });
            });
        },

        /**
         * When paginationType is load more
         * */
        configureTableForLoadMore(response) {
            this.loadMorePreloader = false;
            this.activePaginationPage = response.current_page;
            this.dataSet = [...this.dataSet, ...response[this.dataSetKey]];
        },

        /**
         * When paginationType is pagination
         * */
        configureTableForPagination(response) {
            this.dataSet = response[this.dataSetKey];
        },

        /**
         * @pram = boolean
         * Set preloader for data table
         * */
        setPreloader(val) {
            this.preloader = val;
        },

        /**
         * For reload datatable
         * */
        reloadDataTable() {
            let name = 'reload-' + this.id;
            this.$hub.$on(name, (value = true) => {
                if (value === true && !this.actionTriggered) this.activePaginationPage = 1;
                this.$hub.$emit('resetPaginationState', this.activePaginationPage);
                this.dataTableInit();
            });
        },

        /**
         * After clear all filter reload datatable
         * */
        clearAllFilter() {
            this.$emit('afterClearFilter')
            this.$hub.$emit('clearAllFilter-' + this.id);
        },

        /**
         * For load more emit
         * */
        loadMoreSubmit() {
            this.loadMorePreloader = true;
            this.activePaginationPage = Number(this.activePaginationPage) + Number(1);
            this.dataOffset += Number(this.paginationRowLimit);
            this.getDataSet();
        },

        /**
         * For pagination emit
         * */
        paginationSubmit(page) {
            this.activePaginationPage = page;
            this.getDataSet();
        },

        /**
         * Set pagination limit and reload datatable
         * */
        setPaginationRow(limit) {
            this.paginationRowLimit = limit;
            this.$hub.$emit('resetPaginationState', 1);
            this.activePaginationPage = 1;
            $('.dropdown-menu-' + this.id).removeClass('show');
            this.getDataSet();
        },

        /**
         * Sort by Filter
         * */
        setFilterBy(item) {
            this.filteredBy = item;
            this.$hub.$emit('resetPaginationState', 1);
            this.activePaginationPage = 1;
            $('.dropdown-menu-sort-by-' + this.id).removeClass('show');
            this.getDataSet();
        },

        /**
         * Get dynamic content form db
         * */
        getDynamicContent(key, dynamicContentObj) {
            this.setPreloader(true);

            this.axiosGet(dynamicContentObj.value)
                .then(response => {

                    let obj = {
                        key: key,
                        value: response.data
                    }
                    this.dynamicContents.push(obj);

                }).catch(({response}) => {

                // trigger after error

            }).finally(() => {
                this.setPreloader(false);
            });
        },

        /**
         * @parm key = String
         * Get the dynamic content value by key
         * */
        getDynamicContentValueByKey(key) {
            let obj = this.dynamicContents.find(item => item.key === key);
            return !this.isUndefined(obj) ? obj.value : false;
        },

        /**
         * @parm row = Object
         * @parm column = Object
         * $emit from column type = butoon
         * */
        getButtonTypeValue(row, column) {
            this.getAction(row, column, true);
        },

        /**
         * Sort by key and order
         * */
        sortByColumn(column, index) {
            this.isSort = false;
            if (this.sortByKey === column.key) {
                if (this.orderBy === 'desc') {
                    this.setSortIcon('asc', 'up', index);
                } else {
                    this.setSortIcon('desc', 'down', index);
                }
            } else {
                this.setSortIcon('asc', 'up', index);
            }

            this.dataTableColumns.filter((item, itemIndex) => {
                if (index !== itemIndex && item?.sortable) this.dataTableColumns[itemIndex].sortIcon = 'no-sort';
            });
            this.sortByKey = column.key;
            this.isSort = true;
            this.dataTableInit();
        },
        setColumns() {
            this.dataTableColumns = this.columns.filter((item, index) => {
                item.sortIcon = item?.sortable ? 'no-sort' : '';
                return item;
            })
        },
        setSortIcon(order, icon, index) {
            this.orderBy = order;
            this.dataTableColumns[index].sortIcon = icon;
        },

        /**
         * Expandable column
         * */
        expandableButtonClicked(index) {
            this.expandedColumnIndex === index ? this.expandedColumnIndex = null : this.expandedColumnIndex = index;
        },
        expandableColumnTitle(index, row) {
            return this.expandedColumnIndex === index ?
                (this.expandableColumn.hideTitle ? this.expandableColumn.hideTitle : this.expandableColumn.modifier(row[this.expandableColumn.key], row).title) :
                (this.expandableColumn.showTitle ? this.expandableColumn.showTitle : this.expandableColumn.modifier(row[this.expandableColumn.key], row).title)
        },
        expandableColumnIcon(index) {
            return this.expandedColumnIndex === index ?
                (this.expandableColumn.hideIcon ? this.expandableColumn.hideIcon : 'corner-up-left') :
                (this.expandableColumn.showIcon ? this.expandableColumn.showIcon : 'corner-right-down')
        },
        expandableColumnClassName(row, column) {
            return this.expandableColumn.modifier(row[this.expandableColumn.key], row).className
                ? this.expandableColumn.modifier(row[this.expandableColumn.key], row).className
                : (column.className ? column.className : 'warning');
        },
        expandableColumnDesignModified() {
            this.expandedColumnIndex = null;
            setTimeout(() => {
                let dataTable = $('.datatable');
                dataTable.find('.collapse.show').collapse('hide');
                dataTable.on('show.bs.collapse', '.collapse', function () {
                    dataTable.find('.collapse.show').collapse('hide');
                });
                $('.table-expandable-area').prev().css({
                    borderBottom: 0
                })
                window.onresize = () => {
                    $('.table-expandable-area').prev().css({
                        borderBottom: 0
                    })
                }
            })
        }
    }
}
</script>

<!--would be removed as soon as possible-->
<style lang="scss">
.small-sort-icon {
    svg {
        height: 10px !important;
    }
}
</style>
