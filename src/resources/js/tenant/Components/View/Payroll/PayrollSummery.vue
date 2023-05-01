<template>
    <div :class="!fromEmployeeDetails ? 'content-wrapper':''">
        <template v-if="!fromEmployeeDetails">
            <app-page-top-section
                :title="$t('summery')">
            </app-page-top-section>

            <app-filter-with-search
                :options="tableOptions"
                @filteredValue="setUserId"
            />
        </template>

        <div class="card border-0" style="min-height: 400px;">
            <div :class="fromEmployeeDetails ? 'pt-0 px-0 pb-primary' : 'p-primary'"
                 class="card-header d-flex align-items-center justify-content-between primary-card-color">
                <app-month-calendar/>

                <app-period-calendar :filters="periodFilters" initial-filter-value="lastMonth"/>
            </div>
            <app-overlay-loader v-if="!employee"/>
            <div v-if="employee" :class="!fromEmployeeDetails? 'card-body' : ''">
                <div v-if="!preloader" class="row pb-primary">
                    <div v-if="!fromEmployeeDetails" class="col-md-5">
                        <div class="d-flex mb-primary">
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
                            <div class="d-flex align-items-center ml-primary">
                                <a href="#" @click="changeUser('previous')"
                                   :class="{'disabled text-muted' : isPreviousNull}">
                                    <i class="fa fa-angle-left fa-2x"></i>
                                </a>
                                <span class="text-muted px-2">  </span>
                                <a href="#" @click="changeUser('next')"
                                   :class="{'disabled text-muted' : isNextNull}">
                                    <i class="fa fa-angle-right fa-2x fa-circle-thin"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div :class="fromEmployeeDetails ? 'col-md-12' : 'col-md-7'">
                        <payroll-summery-cards
                            :summaries="summaries.card_summaries"
                        />
                    </div>
                </div>

                <app-table
                    id="payslip-table"
                    :options="options"
                    @action="triggerActions"
                />
            </div>
        </div>

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :loading="loading"
            :message="promptMessage"
            :modal-class="promptClass"
            :icon="promptIcon"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed"
            @cancelled="confirmationModalActive = false"
            :self-close="false"
        />

        <app-payslip-view-modal
            v-if="payslipViewModal"
            v-model="payslipViewModal"
            :payslip="selectedPayslip"
        />

        <app-payslip-edit-modal
            v-if="payslipEditModal"
            v-model="payslipEditModal"
            :payslip="selectedPayslip"
            @updated="afterUpdated"
        />

        <app-payslip-conflict-modal
            v-if="payslipConflictModal"
            v-model="payslipConflictModal"
            :payslip="selectedPayslip"
            @input="setUrlAndReloadTable(employee.id)"
        />
    </div>
</template>

<script>
import SelectAbleUserFilterMixin from "../../Mixins/SelectAbleUserFilterMixin";
import {axiosGet, urlGenerator} from "../../../../common/Helper/AxiosHelper";
import {collection} from "../../../../common/Helper/helpers";
import PayrollSummeryMixin from "../../Mixins/PayrollSummeryMixin";
import PayrollSummeryCards from "./Components/PayrollSummeryCards";
import {PAYROLL, PAYSLIP} from "../../../Config/ApiUrl";
import {TENANT_SELECTABLE_PAYRUN_USER} from "../../../Config/ApiUrl";

export default {
    name: "PayrollSummery",
    components: {PayrollSummeryCards},
    mixins: [SelectAbleUserFilterMixin, PayrollSummeryMixin],
    props: {
        firstUser: {
            default: function () {
                return {}
            }
        },
        fromEmployeeDetails: {},
    },
    data() {
        return {
            userType: 'payrun',
            selectableUserByManager: true,
            preloader: true,
            selectableUserUrl: `${TENANT_SELECTABLE_PAYRUN_USER}?with=status-profile-picture`,
            summaries: {},
            isChanging: false,
            nextUser: {},
            filterLoaded: true,
            confirmationModalActive: false,
            payslipConflictModal: false,
            promptClass: '',
            promptIcon: '',
            promptMessage: '',
            payslipViewModal: false,
            payslipEditModal: false,
            selectedPayslip: {},
            actionType: '',
            loading: false,
        }
    },
    methods: {
        setUserId(filter) {
            this.getPayrollSummary(filter.user || this.user.id);
            this.setActiveUserToAvatarFilter(filter.user)
            this.setActiveUserToDropdownFilter(filter.user);
            this.setUrlAndReloadTable(filter.user || this.user.id);
        },
        changeUser(type) {
            this.isChanging = true;
            axiosGet(`${this.apiUrl.APP_SELECTABLE}/${this.employee.id || this.user.id}/next-user/${type}`)
                .then(response => {
                    this.nextUser = response.data;
                    this.setActiveUserToAvatarFilter(this.nextUser.id);
                    this.setActiveUserToDropdownFilter(this.nextUser.id);
                    this.getPayrollSummary(this.nextUser.id);
                    this.setUrlAndReloadTable(this.nextUser.id);
                })
        },
        setUrlAndReloadTable(userId) {
            this.options.url = `${PAYROLL}/${userId}/summery-table?${this.queryString}`;
            this.$hub.$emit('reload-payslip-table');
        },
        getPayrollSummary(userId){
            this.preloader = true;
            if(userId){
                axiosGet(`${PAYROLL}/${userId}/summaries?${this.queryString}`).then(response => {
                    this.summaries = response.data;
                }).finally(() => {
                    this.preloader = false;
                });
            }
        },
        triggerActions(row, action, active) {
            if (action.actionName === 'view') {
                this.selectedPayslip = row
                this.payslipViewModal = true;
            } else if (action.actionName === 'edit') {
                this.selectedPayslip = row
                this.payslipEditModal = true;
            } else if (action.actionName === 'send') {
                this.actionType = 'send';
                this.selectedPayslip = row
                this.confirmationModalActive = true;
                this.promptIcon = 'send';
                this.promptClass = 'primary';
                this.promptMessage = this.$t('send_this_payslip_to_employee', {
                    send: row.status.name === 'status_sent' ? this.$t('resend') : this.$t('send'),
                    employee: row.user.full_name
                });
            } else if (action.actionName === 'delete') {
                this.actionType = 'delete';
                this.selectedPayslip = row
                this.confirmationModalActive = true;
                this.promptIcon = 'trash-2';
                this.promptClass = 'danger';
                this.promptMessage = this.$t('this_content_will_be_deleted_permanently');
            } else if (action.actionName === 'view_pdf') {
                window.open(urlGenerator(`${PAYSLIP}/${row.id}/pdf`), "_blank") || window.location.replace(urlGenerator(`${PAYSLIP}/${row.id}/pdf`));
            } else if (action.actionName === 'download_pdf') {
                window.open(urlGenerator(`${PAYSLIP}/${row.id}/pdf?download=true`), "_blank") || window.location.replace(urlGenerator(`${PAYSLIP}/${row.id}/pdf?download=true`));
            }else if (action.actionName === 'manage_conflict') {
                this.selectedPayslip = row
                this.payslipConflictModal = true;
            }
        },
        confirmed() {
            if (this.actionType === 'send') {
                this.sendPayslipMail()
            } else if (this.actionType === 'delete') {
                this.deletePayslip()
            } else if (this.actionType === 'sendMonthly') {
                this.sendMonthlyPayslip()
            } else if (this.actionType === 'sendFiltered') {
                this.sendMonthlyPayslip(true)
            }
        },
        sendPayslipMail() {
            this.loading = true;
            axiosGet(`${PAYSLIP}/${this.selectedPayslip.id}/send`).then((response) => {
                this.$toastr.s(response.data.message);
                this.getPayrollSummary(this.employee?.id || this.user.id);
            }).finally(() => {
                this.$hub.$emit('reload-payslip-table');
                this.closeConfirmation();
            })
        },
        deletePayslip() {
            this.loading = true;
            axiosGet(`${PAYSLIP}/${this.selectedPayslip.id}/delete`).then((response) => {
                this.$toastr.s(response.data.message);
                this.getPayrollSummary(this.employee?.id || this.user.id);
            }).catch(({response}) => {
                this.$toastr.e('', response.data.message);
            }).finally(() => {
                this.$hub.$emit('reload-payslip-table');
                this.closeConfirmation();
            })
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
        afterUpdated() {
            $("#payslip-edit-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.payslipEditModal = false;
            this.$hub.$emit('reload-payslip-table');
        },
    },
    computed: {
        queryString() {
            return this.$store.getters.getFilterUrls;
        },
        user() {
            return this.isChanging ? this.nextUser : JSON.parse(this.firstUser);
        },
        employee() {
            return this.summaries.employee;
        },
        profileUrl() {
            return urlGenerator(`${this.apiUrl.EMPLOYEES_PROFILE}/${this.employee.id}/profile`)
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
    },
    watch: {
        queryString: {
            handler: function (queryString) {
                if (!queryString) {
                    return;
                }
                this.getPayrollSummary(this.employee?.id || this.user.id);
                this.setUrlAndReloadTable(this.employee?.id || this.user.id);
            },
            immediate: true
        }
    },
    // mounted() {
    //     this.getPayrollSummary(this.employee?.id || this.user.id);
    // },

}
</script>

<style scoped>

</style>