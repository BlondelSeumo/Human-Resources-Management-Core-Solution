<template>
    <div :class="!fromEmployeeDetails ? 'content-wrapper':''">
        <app-page-top-section v-if="!fromEmployeeDetails" :title="$t('summery')">
            <div class="d-flex d-inline">
                <app-default-button
                    btn-class="btn btn-success mr-2"
                    :title="$t('export_all')"
                    v-if="$can('export_leave_summery')"
                    @click="viewConfirmationModal(true)"
                />
                <app-default-button
                    btn-class="btn btn-success mr-2"
                    :title="$t('export')"
                    v-if="$can('export_leave_summery')"
                    @click="viewConfirmationModal(false)"
                />
                <leave-top-buttons @open-model="openLeaveModal"/>
            </div>
        </app-page-top-section>

        <app-filter-with-search
            :options="tableOptions"
            @filteredValue="setUserId"
            v-if="!specificEmployee"
        />

        <app-overlay-loader v-if="preloader"/>

        <div v-if="employee"
             :class="!fromEmployeeDetails?'card card-with-shadow border-0':''" style="min-height: 400px;">
            <div>
                <div :class="!fromEmployeeDetails? 'card-body' : ''">
                    <div class="pb-primary">
                        <div class="" v-if="!specificEmployee">
                            <div class="d-flex justify-content-between mb-primary">
                                <div class="media align-items-center">
                                    <app-avatar
                                        :title="employee.full_name"
                                        avatar-class="avatars-w-50"
                                        :status="$optional(employee, 'status', 'class')"
                                        :img="$optional(employee, 'profile_picture', 'full_url')"
                                    />

                                    <div class="media-body ml-3">
                                        <a :href="profileUrl">{{ employee.full_name }}</a>
                                        <p class="text-muted font-size-90 mb-0">
                                            {{ employee.email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="#" @click="changeUser('previous')"
                                       :class="{'disabled text-muted' : isPreviousNull}">
                                        {{ $t('previous') }}
                                    </a>
                                    <span class="text-muted px-2">|</span>
                                    <a href="#" @click="changeUser('next')"
                                       :class="{'disabled text-muted' : isNextNull}">
                                        {{ $t('next') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <employee-leave-summary
                                :summaries="cardSummaries"
                            />
                        </div>
                    </div>
                    <div
                        class="d-flex align-items-center justify-content-between primary-card-color"
                        :class="fromEmployeeDetails? 'pt-0 px-0 mb-3' : ''">
                        <app-month-calendar :periods-url="apiUrl.SELECTABLE_LEAVE_PERIODS"/>

                        <app-period-calendar/>
                    </div>
                    <hr class="mb-primary">
                    <app-table
                        class="remove-datatable-x-padding"
                        id="leave-summery-table"
                        :options="options"
                        @action="triggerActions"
                    />
                </div>
            </div>
        </div>

        <app-leave-create-edit-modal
            v-if="isLeaveModalActive"
            v-model="isLeaveModalActive"
            @reload="reload"
            :specific-id="specificId"
        />

        <app-leave-response-log-modal
            v-if="isResponseLogModalActive"
            v-model="isResponseLogModalActive"
            :url="logUrl"
            :manager-dept="managerDept"
            :table-id="tableId"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :message="modalSubtitle"
            :modal-class="modalClass"
            :icon="modalIcon"
            modal-id="app-confirmation-modal"
            @confirmed="updateStatus"
            @cancelled="cancelled"
        />

        <app-confirmation-modal
            v-if="exportConfirmationModal"
            :title="modalSubtitle"
            :message="modalMessage"
            modal-id="app-confirmation-modal"
            modal-class="primary"
            icon="download"
            :first-button-name="$t('export')"
            :second-button-name="$t('cancel')"
            @confirmed="exportFilteredLeave()"
            @cancelled="exportConfirmationModal = false"
            :self-close="false"
        />

        <app-confirmation-modal
            v-if="noDataFoundModal"
            :message="this.$t('please_filter_the_leave_data_first')"
            :title="this.$t('no_leave_data_found')"
            :second-button-name="$t('okay')"
            :hide-first-button="true"
            modal-class="warning"
            icon="filter"
            modal-id="app-confirmation-modal"
            @cancelled="noDataFoundModal = false"
            :self-close="false"
        />
    </div>
</template>

<script>
import LeaveSummaryMixin from '../../Mixins/LeaveSummaryMixin'
import EmployeeLeaveSummary from "./Components/EmployeeLeaveSummary";
import {axiosGet, urlGenerator} from "../../../../common/Helper/AxiosHelper";
import SelectAbleUserFilterMixin from "../../Mixins/SelectAbleUserFilterMixin";
import NavFilterMixin from "../../Mixins/NavFilterMixin";
import {LEAVES} from "../../../Config/ApiUrl";
import LeaveRequestActionMixin from "../../Mixins/LeaveRequestActionMixin";
import LeaveTopButtons from "./Components/LeaveTopButtons";
import {collection} from "../../../../common/Helper/helpers";
import {localTimeZone} from "../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "LeaveSummary",
    props: {
        leaveId: {},
        firstUser: {
            default: function () {
                return {}
            }
        },
        specific: {},
        managerDept: {},
        fromEmployeeDetails: {}
    },
    components: {EmployeeLeaveSummary, LeaveTopButtons},
    mixins: [LeaveSummaryMixin, SelectAbleUserFilterMixin, NavFilterMixin, LeaveRequestActionMixin],
    data() {
        return {
            userType: 'leave',
            selectableUserByManager: true,
            preloader: true,
            summaries: {},
            dataSet: [],
            isChanging: false,
            nextUser: {},
            filterLoaded: true,
            noDataFoundModal: false,
            exportConfirmationModal: false,
            exportAllEmployee : '',
            modalSubtitle : '',
            modalMessage : '',
        }
    },
    created() {
        if (this.leaveId) {
            this.logUrl = `${this.apiUrl.LEAVES}/${this.leaveId}/log`
            this.isResponseLogModalActive = true;
        }
    },
    methods: {
        getLeaveSummary(userId = false) {
            this.preloader = true;
            let employeeId = userId || this.user.id
            if (employeeId) {
                axiosGet(`${this.apiUrl.LEAVES}/${employeeId}/summaries`).then(response => {
                    this.summaries = response.data;
                }).finally(() => {
                    this.preloader = false;
                });
            }
        },
        changeUser(type) {
            this.isChanging = true;
            axiosGet(`${this.apiUrl.APP_SELECTABLE}/${this.employee.id || this.user.id}/next-user/${type}`)
                .then(response => {
                    this.nextUser = response.data;
                    this.setActiveUserToAvatarFilter(this.nextUser.id);
                    this.setActiveUserToDropdownFilter(this.nextUser.id);
                    this.getLeaveSummary();
                    this.setUrlAndReloadTable(this.nextUser.id);
                })
        },
        setUserId(filter) {
            this.getLeaveSummary(filter.user);
            this.setActiveUserToAvatarFilter(filter.user)
            this.setActiveUserToDropdownFilter(filter.user);
            this.setUrlAndReloadTable(filter.user || this.user.id);
        },
        setUrlAndReloadTable(userId) {
            this.options.url = `${LEAVES}/${userId}/summaries-data-table?${this.queryString}`;
            this.$hub.$emit('reload-leave-summery-table');
        },
        reload() {
            this.setUrlAndReloadTable(this.employee?.id || this.user.id)
            this.getLeaveSummary(this.employee?.id);
        },
        viewConfirmationModal(allEmployee = false) {
            if (allEmployee) {
                this.exportAllEmployee = true;
                this.modalSubtitle = this.$t('all_employees_leave_export_title');
                this.modalMessage = this.$t('all_employees_leave_export_message');
            } else {
                this.exportAllEmployee = false;
                this.modalSubtitle = this.$t('leave_export_title');
                this.modalMessage = this.$t('leave_export_message');
            }
            if (!allEmployee && this.tableData.length < 1) {
                this.noDataFoundModal = true;
            } else {
                this.exportConfirmationModal = true;
            }
        },
        exportFilteredLeave() {
            let url = urlGenerator(`${this.apiUrl.EXPORT}/${this.employee.id || this.user.id}/leave?${this.queryString}&timeZone=${localTimeZone}`);
            if (this.exportAllEmployee) {
                url = urlGenerator(`${this.apiUrl.EXPORT}/leave/all?${this.queryString}&timeZone=${localTimeZone}`);
            }
            window.location = url;
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.exportConfirmationModal = false;
        }
    },
    computed: {
        specificId() {
            if (this.specific) {
                return JSON.parse(this.firstUser)?.id
            }

            return this.adminRequestId ? this.adminRequestId : '';
        },
        isPreviousNull() {
            return collection(this.getAllUsers).first().id === (this.employee.id || this.user.id)
        },
        getAllUsers() {
            return this.tableOptions.filters.find(data => data.key === 'user').option
        },
        isNextNull() {
            return collection(this.getAllUsers).last().id === (this.employee.id || this.user.id)
        },
        specificEmployee() {
            if (this.specific) {
                return true
            }

            if (this.getAllUsers.length <= 1) {
                return collection(this.getAllUsers).last().id === window.user.id
            }

            return false;
        },
        cardSummaries() {
            return this.summaries.card_summaries;
        },
        user() {
            return this.isChanging ? this.nextUser : JSON.parse(this.firstUser);
        },
        queryString() {
            return this.$store.getters.getFilterUrls;
        },
        employee() {
            return this.summaries.employee;
        },
        profileUrl() {
            return urlGenerator(`${this.apiUrl.EMPLOYEES_PROFILE}/${this.employee.id}/profile`)
        }
    },
    mounted() {
        this.getLeaveSummary();
        this.$hub.$on('employeeDetailsActionHappened', (value) => {
            this.getLeaveSummary();
            this.$hub.$emit(`reload-${this.tableId}`);
        });
        this.$hub.$on('leaveStatusUpdated', () => {
            this.getLeaveSummary(this.employee?.id);
        })
    },
    watch: {
        queryString: {
            handler: function (queryString) {
                if (!queryString) {
                    return;
                }
                this.setUrlAndReloadTable(this.employee?.id || this.user.id)
            },
            immediate: true
        }
    },

}
</script>