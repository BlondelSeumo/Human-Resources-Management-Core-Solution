import {LEAVE_TYPES} from '../../Config/ApiUrl'

export default {
    data() {
        return {
            options: {
                name: this.$t('leave_type'),
                url: LEAVE_TYPES,
                showHeader: true,
                tableShadow:false,
                tablePaddingClass:'pt-primary',
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('type'),
                        type: 'custom-html',
                        key: 'type',
                        modifier: (type) => {
                            return this.$t(type);
                        }
                    },
                    {
                        title: this.$t('amount'),
                        type: 'text',
                        key: 'amount',
                    },
                    {
                        title: this.$t('enabled'),
                        type: 'custom-html',
                        key: 'is_enabled',
                        modifier: (value) => {
                            return parseInt(value) === 1 ? this.$t("yes") : this.$t("no");
                        }
                    },
                    {
                        title: this.$t('is_earning_enabled'),
                        type: 'component',
                        componentName: 'app-leave-allow-earning-button',
                        key: 'is_earning_enabled',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
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
                        type: "checkbox",
                        key: "type",
                        option: [
                            {id: 'paid', value: this.$t('paid')},
                            {id: 'unpaid', value: this.$t('unpaid')},
                            {id: 'special', value: this.$t('special')},
                        ],
                        listValueField: 'value'
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-department-modal',
                        modalId: 'department-modal',
                        url: LEAVE_TYPES,
                        name: 'edit',
                        modifier: row => this.$can('update_leave_types')
                    },
                    {
                        title: this.$t('delete'),
                        name: 'delete',
                        icon: 'trash-2',
                        modalClass: 'warning',
                        url: LEAVE_TYPES,
                        modifier: row => this.$can('delete_leave_types')
                    },
                ],
            }
        }
    }
}
