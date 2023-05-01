import {PAYSLIP} from "../../Config/ApiUrl";
import {getDateDifferenceString} from "../../../common/Helper/Support/DateTimeHelper";
import {DepartmentFilterMixin} from "./FilterMixin";
import NavFilterMixin from "./NavFilterMixin";
import StatusMixin from "../../../common/Mixin/Global/StatusMixin";
import {formatCurrency, numberFormatter} from "../../../common/Helper/Support/SettingsHelper";
import {ucFirst} from "../../../common/Helper/Support/TextHelper";

export default {
    mixins:[DepartmentFilterMixin, NavFilterMixin, StatusMixin],
    data(){
        return {
            numberFormatter,
            tableOptions: {
                tableData: 0,
                afterRequestSuccess: ({data}) => {
                    this.tableData = data.data.length;
                },
                name: this.$t('payslip'),
                url: PAYSLIP,
                showHeader: true,
                enableRowSelect: false,
                showCount: false,
                showClearFilter: false,
                showFilter: false,
                showSearch: false,
                tablePaddingClass: "px-0",
                tableShadow: false,
                managePagination: true,
                datatableWrapper: false,
                filters: [],
                columns: [
                    {
                        title: this.$t('profile'),
                        type: 'component',
                        key: 'user',
                        componentName: 'app-attendance-employee-media-object',
                    },
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
                    },
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
            options: {
                showFilter: true,
                showSearch: true,
                showClearFilter: true,
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('status'),
                        type: "multi-select-filter",
                        key: "status",
                        option:[],
                        listValueField: 'translated_name',
                    },
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        option: [],
                        listValueField: 'name',
                        permission: this.$can('view_departments')
                    },
                    {
                        title: this.$t('type'),
                        type: "radio",
                        key: "type",
                        option:[
                            { id: 'manual', name: this.$t('manual') },
                            { id: 'default', name: this.$t('default') },
                        ],
                        listValueField: 'name',
                        header: {
                            "title": "Filter data by Payrun Type.",
                            "description": "You can filter the data table based on which Payrun Type applied to generated the Payslips."
                        },
                    },
                    {
                        title: this.$t('payrun_period'),
                        type: "radio",
                        key: "payrun_period",
                        option:[
                            { id: 'monthly', name: this.$t('monthly') },
                            { id: 'weekly', name: this.$t('weekly') },
                        ],
                        listValueField: 'name',
                        header: {
                            "title": "Filter data by Payrun Period.",
                            "description": "You can filter the data table based on which Payrun Period applied to generated the Payslips."
                        },
                    },
                    {
                        title : this.$t('show_conflicted'),
                        type: 'toggle-filter',
                        key: 'conflicted',
                        buttonLabel: {
                            active: 'Yes',
                            inactive: 'No'
                        },
                        header: {
                            title: this.$t('show_conflicted_payslip'),
                            description: this.$t('filter_data_which_are_conflicted')
                        }
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
    methods:{
        putIntoSpan(data){
            return `<span class="min-width-100 d-block">${data}</span>`
        }
    }
}