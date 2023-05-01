import coreLibrary from '../../../helpers/CoreLibrary';

export const TableMixin = {
    extends: coreLibrary,
    props: {
        id: {
            type: String,
            required: true
        },
        options: {
            required: true,
            type: Object
        },
        columns: {
            required: true,
            type: Array
        },
        cardView: {
            type: Boolean,
            default: false
        },
        clearFilterVisible: {
            type: Boolean,
            default: false
        },
        filteredData: {
            type: Object,
            default: function () {
                return {};
            }
        },
        searchValue: String
    },
    computed: {
        allColumnDisable() {
            let disabledColumns = this.columns.filter(column => column.isVisible !== false);
            return disabledColumns.length <= 0;
        },
        expandableColumn() {
            return this.columns.find(element => element.type === 'expandable-column');
        },
        visibleColumnCount() {
            return this.columns.filter(element => element.isVisible).length + (this.options.enableRowSelect ? 1 : 0)
        },
        showPaginationOptions() {
            return this.options.paginationOptions ? this.options.paginationOptions : [10, 20, 30, 40, 50, 100]
        },
        paginationClass() {
            return this.options.paginationBlockClass ? this.options.paginationBlockClass :
                ((this.options.datatableWrapper || this.isUndefined(this.options.datatableWrapper)) ? 'mt-primary' : 'p-primary')
        },
        showItemText() {
            return `
            ${this.$t('showing')}
            ${this.totalRow > 0 ? ((this.paginationRowLimit * (this.activePaginationPage - 1)) + 1) : 0}
            ${this.$t('to')}
            ${((this.activePaginationPage * this.paginationRowLimit) > this.totalRow) ? this.totalRow :
                (this.activePaginationPage * this.paginationRowLimit)}
            ${this.$t('items')}
            ${this.$t('of')}
            ${this.totalRow}
            `
        },
        showPagination() {
            return this.totalRow > 0 && this.paginationRowLimit > 0 && this.totalRow > this.dataOffset && !this.allColumnDisable && !this.preloader
                && (this.cardView ? (this.options.cardViewPagination || this.isUndefined(this.options.cardViewPagination)) : true)
        },
        dataSetKey() {
            return this.options.dataKey ? this.options.dataKey : 'data'
        },
        filterByOptions() {
            return this.options.sortByFilter?.isVisible === true ? this.options.sortByFilter : null
        },
        filterByValue() {
            return this.filteredBy ? this.filteredBy : (this.filterByOptions?.options.length ? this.filterByOptions.options[0] : '')
        },
        requestParams() {
            return this.isUndefined(this.options.queryParams) || this.options.queryParams
        }
    },
    methods: {
        /**
         * Send event after trigger an action
         * */
        getAction(rowData, actionObj, active) {
            this.actionTriggered = true;
            this.$emit("action", rowData, actionObj, active);
        }
    }
};
