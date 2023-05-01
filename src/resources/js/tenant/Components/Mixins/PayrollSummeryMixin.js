import {getDateDifferenceString} from "../../../common/Helper/Support/DateTimeHelper";
import {formatCurrency, numberFormatter} from "../../../common/Helper/Support/SettingsHelper";
import {ucFirst} from "../../../common/Helper/Support/TextHelper";

export default {
    data() {
        return {
            options: {
                name: this.$t('payslip'),
                url: '',
                showHeader: true,
                enableRowSelect: false,
                showCount: false,
                showClearFilter: false,
                showFilter: false,
                showSearch: false,
                tablePaddingClass: "px-0",
                tableShadow: false,
                managePagination: false,
                datatableWrapper: true,
                filters: [],
                columns: [
                    {
                        title: this.$t('payrun'),
                        type: 'custom-html',
                        key: 'payrun',
                        isVisible: true,
                        modifier: (val, row) => {
                            return `<span class="min-width-130 d-block">${getDateDifferenceString(row.start_date, row.end_date)}</span><p class="pt-1 font-size-90 text-muted">ID: ${val.uid}</p>`;
                        }
                    },
                    {
                        title: this.$t('payrun_period'),
                        type: 'custom-html',
                        key: 'payrun',
                        isVisible: true,
                        modifier: payrun => {
                            if(payrun.data){
                                return this.putIntoSpan(JSON.parse(payrun.data).period ? ucFirst(JSON.parse(payrun.data).period) : '-');
                            }
                        }
                    },
                    {
                        title: this.$t('payrun_type'),
                        type: 'custom-html',
                        key: 'payrun',
                        isVisible: true,
                        modifier: payrun => {
                            if(payrun.data){
                                return this.putIntoSpan(JSON.parse(payrun.data).type ? ucFirst(JSON.parse(payrun.data).type) : '-');
                            }
                        }
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: (status, row) => {
                            return `<div class="d-flex"><span class="mr-2 badge badge-sm badge-pill badge-${status.class}">
                                ${status.translated_name}
                            </span>${parseInt(row.conflicted) ?
                                `<span class="badge badge-sm badge-pill badge-danger">
                                ${this.$t('conflicted')}
                            </span>` : ''}</div>`
                        }
                    },
                    {
                        title: this.$t('salary'),
                        type: 'custom-html',
                        key: 'basic_salary',
                        isVisible: true,
                        modifier: (basicSalary) => {
                            return this.putIntoSpan(`${formatCurrency(numberFormatter(Number(basicSalary)))}`);
                        }
                    },
                    {
                        title: this.$t('net_salary'),
                        type: 'custom-html',
                        key: 'net_salary',
                        isVisible: true,
                        modifier: (netSalary) => {
                            return this.putIntoSpan(`${formatCurrency(numberFormatter(Number(netSalary)))}`);
                        }
                    },
                    {
                        title: this.$t('details'),
                        type: 'button',
                        className: 'btn btn-secondary d-inline-flex align-items-center',
                        icon: 'eye',
                        actionName: 'view',
                        modifier: (row) => {
                            return 'View';
                        }
                    },
                    this.$can('send_individual_payslip') ?
                    {
                        title: this.$t('email'),
                        type: 'button',
                        className: 'btn btn-secondary d-inline-flex align-items-center',
                        key: 'status',
                        icon: 'send',
                        actionName: 'send',
                        modifier: (status) => {
                            return status.name !== 'status_generated' ? this.$t('resend') : this.$t('send')
                        }
                    } : {},
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                queryParams: false,
                actions: [
                    {
                        title: this.$t('edit'),
                        actionName: 'edit',
                        modifier: row => this.$can('edit_payslip')
                    },
                    {
                        title: this.$t('manage_conflict'),
                        actionName: 'manage_conflict',
                        modifier: row => parseInt(row.conflicted) && this.$can('manage_payslip_confliction')
                    },
                    {
                        title: this.$t('view_pdf'),
                        actionName: 'view_pdf',
                        modifier: row => this.$can('view_payslip_pdf')
                    },
                    {
                        title: this.$t('download_pdf'),
                        actionName: 'download_pdf',
                        modifier: row => this.$can('view_payslip_pdf')
                    },
                    {
                        title: this.$t('delete'),
                        actionName: 'delete',
                        modifier: row => this.$can('delete_payslip')
                    },
                ],
            },
            periodFilters: [
                {
                    id: 'thisMonth',
                    value: this.$t('this_month')
                },
                {
                    id: 'lastMonth',
                    value: this.$t('last_month')
                },
                {
                    id: 'thisYear',
                    value: this.$t('this_year')
                },
                {
                    id: 'lastYear',
                    value: this.$t('last_year')
                },
                {
                    id: 'total',
                    value: this.$t('total')
                },
            ]
        }
    },
    computed:{
        currencySymbol(){
            return window.settings.currency_symbol;
        }
    },
    methods: {
        setFilterValues(filterValue) {
            if (filterValue.created && typeof filterValue.created != 'string') {
                filterValue.created = JSON.stringify(filterValue.created)
            }
            if (filterValue.payrun_date && typeof filterValue.payrun_date != 'string') {
                filterValue.payrun_date = JSON.stringify(filterValue.payrun_date)
            }
            this.$store.dispatch('updateFilterObject', filterValue)
        },
        setSearchValue(search) {
            this.$store.dispatch('updateFilterObject', {search: search})
        },
        putIntoSpan(data){
            return `<span class="min-width-100 d-block">${data}</span>`
        }
    }
}