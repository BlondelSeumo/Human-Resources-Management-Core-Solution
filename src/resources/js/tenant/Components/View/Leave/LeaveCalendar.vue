<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('calendar')">
            <leave-top-buttons @open-model="openLeaveModal"/>
        </app-page-top-section>

        <app-filter-with-search
            :options="options"
            @filteredValue="setFilterValues"
            @searchValue="setSearchValue"
        />

        <div class="card card-with-shadow border-0">
            <div class="card-header p-primary primary-card-color">
                <div class="row align-items-center">
                    <div class="col-md-10">
                        <div class="d-flex align-items-center justify-content-between">
                            <app-month-calendar :periods-url="apiUrl.SELECTABLE_LEAVE_PERIODS"/>

                            <app-period-calendar/>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <app-input
                            data-toggle="collapse"
                            data-target="#requestedLeaves"
                            type="single-checkbox"
                            v-model="isLeaveRequest"
                            label-class="mb-0"
                            :list-value-field="$t('see_leave_requests')"
                        />
                    </div>
                </div>
            </div>

            <div class="card-body" v-if="preloader">
                <app-overlay-loader/>
            </div>

            <div class="card-body" v-else>
                <leave-calendar-summary :summaries="summaries"/>

                <div class="summery-calendar leave-calendar mb-5 position-relative" style="min-height: 200px">
                    <app-overlay-loader v-if="calendarLoader"/>
                    <div class="d-flex" v-else-if="$optional(users, 'data', 'length')">
                        <div class="profile-column">
                            <div class="column-header">{{ $t('profile') }}</div>
                            <template v-for="user in users.data">
                                <employee-media-object :employee="user"/>
                            </template>
                        </div>
                        <div class="age-column">
                            <div class="column-header">{{ $t('leave_duration') }}</div>
                            <employee-leave-age
                                v-for="user in users.data"
                                :key="user.id"
                                :leaves="user.leaves"
                            />
                        </div>
                        <div class="date-column custom-scrollbar custom-scrollbar-with-mouse-move" @scroll="hidePopper">
                            <div class="d-flex">
                                <div class="date-value-wrapper" v-for="range in ranges">
                                    <div class="date">
                                        {{ range.format('MMM D') }}
                                    </div>
                                    <div class="date-hour-wrapper" v-for="user in users.data">
                                        <leave-age-popover
                                            @openResponseLogModal="openResponseLogModal"
                                            :leave="getLeave(user.leaves, range)"
                                            @activePopper="setActivePopper"
                                            :active="activePopper"
                                            :range="range"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <app-empty-data-block v-else :message="$t('nothing_to_show_here')"/>

                    <app-pagination
                        v-show="users.total && users.last_page > 1"
                        :rowLimit="users.per_page"
                        :totalRow="users.total"
                        @submit="setPagination"
                    />
                </div>
            </div>
        </div>

        <app-leave-create-edit-modal
            v-if="isLeaveModalActive"
            v-model="isLeaveModalActive"
            @reload="reload"
            :specificId="adminRequestId"
        />

        <app-leave-response-log-modal
            v-if="isResponseLogModalActive"
            v-model="isResponseLogModalActive"
            :url="logUrl"
            :manager-dept="managerDept"
        />
    </div>
</template>

<script>
import LeaveCalendarMixin from '../../Mixins/LeaveCalendarMixin'
import LeaveCalendarSummary from "./Components/LeaveCalendarSummary";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";
import EmployeeMediaObject from "./Components/EmployeeMediaObject";
import EmployeeLeaveAge from "./Components/EmployeeLeaveAge";
import LeaveAgePopover from "./Components/LeaveAgePopover";
import LeaveDateAndTime from "./Components/LeaveDateAndTime";
import moment from 'moment'
import {serverDateFormat, serverDateTimeFormat} from "../../../../common/Helper/Support/DateTimeHelper";
import NavFilterMixin from "../../Mixins/NavFilterMixin";
import LeaveTopButtons from "./Components/LeaveTopButtons";

export default {
    name: "LeaveCalendar",
    mixins: [LeaveCalendarMixin, NavFilterMixin],
    components: {
        LeaveDateAndTime,
        EmployeeMediaObject,
        EmployeeLeaveAge,
        LeaveAgePopover,
        LeaveCalendarSummary,
        LeaveTopButtons
    },
    props: {
        managerDept: {}
    },
    data() {
        return {
            summaries: {
                approved_stats: {},
                pending_stats: {},
            },
            activePopper: '',
            users: [],
            preloader: true,
            calendarLoader: true,
            approvedStatus: 0,
            pendingStatus: 0,
            isLeaveRequest: '',
            ranges: [],
        }
    },
    methods: {
        getLeaveCalendarSummary() {
            this.preloader = true;
            axiosGet(`${this.apiUrl.LEAVES}/calendar?${this.queryString}`).then(response => {
                this.summaries = response.data;
                this.ranges = response.data.ranges.map(range => moment(range, serverDateTimeFormat));
                this.getLeaveSummaryCalendar()
            }).finally(() => {
                this.preloader = false;
            });
        },
        getLeaveSummaryCalendar() {
            this.calendarLoader = true;
            axiosGet(`${this.apiUrl.LEAVES}/calendar-table?${this.queryString}&pending=${this.isLeaveRequest}`)
                .then(response => {
                    this.users = response.data;
                    this.$hub.$emit('resetPaginationState', response.data.current_page);
                })
                .finally(() => {
                    this.calendarLoader = false;
                });
        },
        setActivePopper(active) {
            this.activePopper = active;
        },
        getLeave(leaves, date) {
            return leaves.find(leave => {
                return date.isBetween(leave.start_at, leave.end_at) ||
                    date.format(serverDateFormat) === moment(leave.start_at).format(serverDateFormat);
            }) || {}
        },
        hidePopper() {
            this.activePopper = null;
        },
        reload(){
            this.getLeaveCalendarSummary();
        }
    },
    computed: {
        queryString() {
            return this.$store.getters.getFilterUrls;
        },

    },
    watch: {
        queryString: {
            handler: function (queryString) {
                if (!queryString) {
                    return;
                }
                this.getLeaveCalendarSummary();
            },
            immediate: true
        },
        isLeaveRequest: function () {
            this.getLeaveSummaryCalendar();
        }
    },
}
</script>