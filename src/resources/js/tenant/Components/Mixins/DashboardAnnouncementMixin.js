import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {EMPLOYEE_ANNOUNCEMENTS} from "../../Config/ApiUrl";
import {
    calenderTime,
    calenderTimeWithMonthSortName,
    formatDateToLocal
} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('dashboard_announcement'),
                url: EMPLOYEE_ANNOUNCEMENTS,
                datatableWrapper: false,
                tableShadow: false,
                showHeader: true,
                responsive: true,
                showSearch: false,
                showFilter: false,
                showCount: false,
                showClearFilter: false,
                columns: [
                    {
                        title: this.$t('title'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('start_date'),
                        type: 'custom-html',
                        key: 'start_date',
                        isVisible: true,
                        modifier: start_date => start_date ? calenderTimeWithMonthSortName(start_date) : '-'
                    },
                    {
                        title: this.$t('end_date'),
                        type: 'custom-html',
                        key: 'end_date',
                        isVisible: true,
                        modifier: end_date => end_date ? calenderTimeWithMonthSortName(end_date) : '-'
                    },
                    {
                        title: this.$t('description'),
                        type: 'component',
                        componentName: 'app-employee-announcement-detail',
                        key: 'description',
                    },
                ],
                paginationType: "pagination",
                rowLimit: 5,
                showAction: false,
                orderBy: 'desc',
                actionType: "default",
            }
        }
    }
}
