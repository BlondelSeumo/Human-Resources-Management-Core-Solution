import {BENEFICIARY_BADGE} from "../../Config/ApiUrl";
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    data(){
        return {
            options: {
                name: this.$t('beneficiary_badge'),
                url: BENEFICIARY_BADGE,
                showHeader: true,
                enableRowSelect: false,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                    },
                    {
                        title: this.$t('description'),
                        type: 'custom-html',
                        key: 'description',
                        isVisible: true,
                        modifier: (description) => {
                            return  description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('type'),
                        type: 'custom-html',
                        key: 'type',
                        isVisible: true,
                        modifier: type => {
                            return type === 'allowance' ?
                                `<span class="badge badge-pill badge-success">
                                ${this.$t('allowance')}
                            </span>` :
                                `<span class="badge badge-pill badge-warning">
                                ${this.$t('deduction')}
                            </span>`
                        }
                    },
                    {
                        title: this.$t('status'),
                        type: 'component',
                        componentName: 'app-beneficiary-status-toggle-button',
                        key: 'is_active',
                    },
                    {
                        title: this.$t('created'),
                        type: 'custom-html',
                        key: 'created_at',
                        isVisible: true,
                        modifier: (value) => {
                            return formatDateToLocal(value);
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],

                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('type'),
                        type: "radio",
                        key: "type",
                        option:[
                            { id: 'allowance', name: this.$t('allowance') },
                            { id: 'deduction', name: this.$t('deduction') },
                        ],
                        listValueField: 'name',
                        header: {
                            "title": "Filter data by Beneficiary Type.",
                            "description": "You can filter the data table based on Beneficiary Type."
                        },
                    },
                    {
                        title: this.$t('status'),
                        type: "radio",
                        key: "status",
                        option:[
                            { id: 'active', name: this.$t('active') },
                            { id: 'inactive', name: this.$t('inactive') },
                        ],
                        listValueField: 'name',
                        header: {
                            "title": "Filter data by Beneficiary Status.",
                            "description": "You can filter the data table based on Beneficiary Status."
                        },
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        name: 'edit',
                        modifier: row => this.$can('edit_beneficiaries')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        name: 'delete',
                        modifier: (row) => this.$can('delete_beneficiaries')
                    }
                ],
            },
        }
    }
}