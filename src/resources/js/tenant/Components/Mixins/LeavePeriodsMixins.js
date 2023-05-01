import {LEAVE_PERIODS} from '../../Config/ApiUrl'
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    data() {
        return {
            options: {
                name: this.$t('leave_period'),
                url: LEAVE_PERIODS,
                showHeader: true,
                columns: [
                    {
                        title: this.$t('start_date'),
                        type: 'custom-html',
                        key: 'start_date',
                        modifier: start_date => formatDateToLocal(start_date)
                    },
                    {
                        title: this.$t('end_date'),
                        type: 'custom-html',
                        key: 'end_date',
                        modifier: end_date => formatDateToLocal(end_date)
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    }
                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "default",
                showSearch: false,
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-department-modal',
                        modalId: 'department-modal',
                        url: LEAVE_PERIODS,
                        name: 'edit',
                        modifier: row => this.$can('delete_leave_periods')
                    },
                    {
                        title: this.$t('delete'),
                        name: 'delete',
                        icon: 'trash-2',
                        modalClass: 'warning',
                        url: LEAVE_PERIODS,
                        modifier: row => this.$can('delete_leave_periods')
                    },
                ],
            }
        }
    }
}
