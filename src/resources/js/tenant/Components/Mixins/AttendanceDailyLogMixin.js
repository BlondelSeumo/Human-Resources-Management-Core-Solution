import {ATTENDANCE_DAILY_LOG} from "../../Config/ApiUrl";
import { convertSecondToHourMinutes} from "../../../common/Helper/Support/DateTimeHelper";
import AttendanceHelperMixin from "./AttendanceHelperMixin";
import {attendanceBehavior} from "../View/Attendance/Helper/Helper";
import {DepartmentFilterMixin, UserFilterMixin, WorkingShiftFilterMixin} from "./FilterMixin";
import {axiosGet} from "../../../common/Helper/AxiosHelper";

export default {
    mixins: [AttendanceHelperMixin, DepartmentFilterMixin, WorkingShiftFilterMixin, UserFilterMixin],
    data() {
        return {
            options: {
                name: this.$t('daily_log'),
                url: ATTENDANCE_DAILY_LOG,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                tableData: 0,
                afterRequestSuccess: ({data}) => {
                    this.tableData = data.data.length;
                },
                columns: [
                    {
                        title: this.$t('profile'),
                        type: 'component',
                        componentName: 'app-attendance-employee-media-object',
                        key: 'user',
                    },
                    {
                        title: this.$t('punched_in'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-in-date-time',
                    },
                    {
                        title: this.$t('in_geolocation'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-in-geolocation',
                    },
                    {
                        title: this.$t('punched_out'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-out-date-time',
                    },
                    {
                        title: this.$t('out_geolocation'),
                        type: 'component',
                        key: 'details',
                        componentName: 'app-punch-out-geolocation',
                    },
                    {
                        title: this.$t('behavior'),
                        type: 'custom-html',
                        key: 'behavior',
                        modifier: attendanceBehavior
                    },
                    {
                        title: this.$t('type'),
                        type: 'component',
                        componentName: 'app-attendance-type',
                        key: 'details',
                    },
                    {
                        title: this.$t('total_hours'),
                        type: 'custom-html',
                        key: 'details',
                        modifier: (details, attendance) => convertSecondToHourMinutes(this.getTotalWorked(attendance).asSeconds())
                    },
                    {
                        title: this.$t('entry'),
                        type: 'expandable-column',
                        key: 'details',
                        isVisible: true,
                        componentName: 'app-attendance-expandable-column',
                        modifier: (details) => {
                            return {
                                title: details.length > 1 ? this.$t('multi') : this.$t('single'),
                                expandable: details.length > 1,
                                className: details.length > 1 ? 'warning' : 'success'
                            };
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    }
                ],
                filters: [
                    {
                        title: this.$t('today'),
                        type: "date",
                        key: "date",
                        initValue: new Date(), // not required
                    },
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_departments')
                    },
                    {
                        title: this.$t('work_shift'),
                        type: "drop-down-filter",
                        key: "working_shifts",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_working_shifts')
                    },
                    {
                        title: this.$t('behavior'),
                        type: "drop-down-filter",
                        key: "behavior",
                        option: [
                            {id: 'early', name: this.$t('early')},
                            {id: 'regular', name: this.$t('regular')},
                            {id: 'late', name: this.$t('late')},
                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('type'),
                        type: "drop-down-filter",
                        key: "type",
                        option: [
                            {id: 'auto', name: this.$t('auto')},
                            {id: 'manual', name: this.$t('manual')},
                        ],
                        listValueField: 'name'
                    },
                    {
                        title: this.$t('users'),
                        type: "multi-select-filter",
                        key: "users",
                        option: [],
                        listValueField: 'full_name',
                        permission: !!this.$can('view_all_attendance') && !!this.$can('view_users')
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
                        type: 'dailyLogTableRow',
                        url: ATTENDANCE_DAILY_LOG,
                        name: 'edit',
                        modifier: row =>  (this.$can('update_attendances') ||
                            this.$can('send_attendance_request')) && row?.details?.length < 2
                    },
                    {
                        title: this.$t('change_log'),
                        icon: 'trash-2',
                        name: 'change-log',
                        type: 'dailyLogTableRow',
                        modifier: row => row?.details?.length < 2
                    },
                    {
                        title: this.$t('cancel'),
                        name: 'cancel',
                        icon: 'trash-2',
                        type: 'dailyLogTableRow',
                        modalClass: 'warning',
                        modalSubtitle: this.$t('you_are_going_to_cancel_a_attendance'),
                        modalIcon: 'slash',
                        modifier: row => this.$can('update_attendances') &&
                            !!this.collection(row.details).first()?.added_by && row?.details?.length < 2 &&
                            (row.user_id !== window.user.id || this.$isAdmin())
                    },
                ],
            }
        }
    }
}