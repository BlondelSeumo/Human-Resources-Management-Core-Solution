<template xmlns="http://www.w3.org/1999/html">
    <div>
        <div class="card card-with-shadow p-5 border-0">
            <div class="row">
                <div class="col-md-6">
                    <div>
                        <h6 class="text-primary d-inline">
                            <a :href="`${urlGenerator(apiUrl.PAYSLIP_FRONTEND)}?payrun=${payrun.uid}`">
                                {{ payrunTitle(payrunData.time_range) }}
                            </a>
                        </h6>
                        <span class="text-muted font-size-80 ml-2">
                            {{ $t('includes_employee', {'count': payrunData.employees.length}) }}
                        </span>
                    </div>
                    <div>
                        <span class="font-size-90">
                            {{ $t(payrunData.period) }} -
                            {{ $t(payrunData.consider_type) }} {{ $t('based') }}

                            {{  payrunData.consider_type !== 'none' && payrunData.consider_type !== 'None' ?
                                `(${considerOvertimeTitle(payrunData.consider_overtime)} ${$t('overtime')})` : '' }}
                        </span>
                        <span class="text-muted">|</span>
                        <span class="font-size-90">
                            <span class="text-info">
                                {{ $t(payrunData.type) }}
                            </span>
                            - {{ $t('followed_by', {'setting': $t(payrun.followed)}) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted font-size-90">
<!--                            <app-icon name="clipboard" width="14" height="14" class="mr-1"/>-->
                            ID:
                        </span>
                        <span class="font-size-90">{{ payrun.uid }}</span>
                    </div>
                    <div class="mt-1">
                        <a :href="`${urlGenerator(apiUrl.PAYSLIP_FRONTEND)}?payrun=${payrun.uid}`"
                           class="btn btn-outline-primary btn-sm  padding-y-2">
                            {{ this.$t('view_all_payslips') }}
                        </a>
                        <a :href="`${urlGenerator(apiUrl.EXPORT)}/payslip/all?payrun=${payrun.uid}`"
                           v-if="$can('export_payslips')"
                           class="btn btn-outline-success btn-sm ml-2 padding-y-2" :title="$t('export_as_excel')">
                            <app-icon class="size-14" name="download"/>
                            {{ this.$t('export') }}
                        </a>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-1">
                        <span
                            :class="`badge badge-sm badge-pill badge-${inProgress ? 'primary' : payrun.status.class} text-capitalize`">
                            {{ inProgress ? $t('generating') : payrun.status.translated_name }}
                        </span>
                        <!--                        <app-pre-loader class="d-inline"/>-->
                        <div v-if="inProgress" role="status" class="spinner-border width-15 height-15 ml-2"><span
                            class="sr-only"></span></div>
                    </div>
                    <div v-if="inProgress">
                        <span class="text-muted font-size-90">
                            <app-icon name="clipboard" width="14" height="14" class="mr-1"/>
                        </span>
                        <span class="text-muted font-size-90">
                            {{
                                `${batchDetails.processedJobs} ${$t('generated_of')} ${batchDetails.totalJobs} ${$t('payslip')}`
                            }}
                        </span>
                    </div>
                    <div>
                        <span class="text-muted font-size-90">
                            <app-icon name="calendar" width="14" height="14" class="mr-1"/>
                            {{ $t('created_at') }}
                        </span>
                        <span class="font-size-90">{{ timeFormat(payrun.created_at) }}</span>
                    </div>
                    <div>
                        <span class="text-muted font-size-90">
                            <app-icon name="user-check" width="14" height="14" class="mr-1"/>
                            {{
                                payrun.status.name === 'status_generated' ?
                                    $t('not_yet_sent') : $t('sent_to_employee', {count: payrun.sent_payslip})
                            }}
                        </span>
                    </div>
                    <div class="text-warning" v-if="parseInt(payrun.conflicted) > 0">
                        <span class="font-size-90">
                            <app-icon name="flag" width="14" height="14" class="mr-1"/>
                            {{ $t('payslips_conflicted_number_of_employees', {number: payrun.conflicted}) }}
                        </span>
                        <span class="cursor-pointer text-primary font-size-90"
                              v-if="$can('manage_payslip_confliction')"
                              @click="openConflictedPayslip">{{ $t('manage') }}</span>
                    </div>
                </div>
                <div class="col-md-3 text-right">
                    <div v-if="inProgress" class="progress" style="height: 20px">
                        <div class="progress-bar bg-success progress-bar-striped progress-bar-animated text-dark"
                             :style="`width:${batchDetails.progress}%;height:20px`">
                            <span class="ml-2">{{ batchDetails.progress }}% completed</span>
                        </div>
                    </div>

                    <template v-else>
                        <div v-if="payrunData.type === 'manual' && $can('update_payruns') && payrun.status.name === 'status_generated'"
                             class="btn-group btn-group-action"
                             role="group"
                             aria-label="Default action">
                            <button type="button"
                                    @click="updatePayrun(payrun.id)"
                                    class="btn btn-link">
                                <app-icon name="edit"/>
                            </button>
                        </div>
                        <div class="btn-group btn-group-action"
                             role="group"
                             v-if="payrun.status.name === 'status_generated' && $can('delete_payruns')"
                             aria-label="Default action">
                            <button type="button"
                                    @click="deletePayrunClicked(payrun.id)"
                                    class="btn btn-link">
                                <app-icon name="trash-2"/>
                            </button>
                        </div>
                        <button
                            v-if="$can('send_payrun_payslips')"
                            class="btn btn-primary btn-sm"
                            @click.prevent="sendPayslipsClicked(payrun.id)">
                            {{ payrun.status.name === 'status_sent' ? $t('resend_payslip') : $t('send_payslip') }}
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :loading="loading"
            :message="promptMessage"
            :title="promptTitle"
            :firstButtonName="$t('yes')"
            :modal-class="promptClass"
            :icon="promptIcon"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed"
            @cancelled="cancelled"
            :self-close="false"
        />
    </div>
</template>

<script>
import {payrunTitleByDateRange} from "../Helper/helper";
import {calenderTime} from "../../../../../common/Helper/Support/DateTimeHelper";
import {axiosDelete, axiosGet, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {getConsiderOvertimeTitle} from "../Helper/helper";

export default {
    name: "PayrunItemsRow",
    props: {
        payrun: {},
    },
    data() {
        return {
            urlGenerator,
            confirmationModalActive: false,
            loading: false,
            promptMessage: '',
            promptClass: '',
            promptTitle: '',
            promptIcon: '',
            actionType: '',
            deleteUrl: '',
            payslipUrl: '',
            payrunProgress: 0,
            inProgress: false,
            progressPayrunUid: null,
            batchDetails: {},
        }
    },
    mounted() {
        if (this.payrun.batch_id) {
            this.inProgress = true;
            this.getBatchProgress(this.payrun.batch_id)
        }
    },
    computed: {
        payrunData() {
            return JSON.parse(this.payrun?.data)
        },
    },
    methods: {
        considerOvertimeTitle(consider_overtime){
            return getConsiderOvertimeTitle(consider_overtime)
        },
        getBatchProgress(batchId) {
            axiosGet(`batch/${batchId}`).then(({data}) => {
                this.batchDetails = data;
                if (!data.finishedAt && data.progress < 100) {
                    setTimeout(() => {
                        this.getBatchProgress(data.id);
                    }, 2000)
                } else {
                    this.removeBatchId();
                }
            })
        },
        removeBatchId() {
            axiosGet(`${this.apiUrl.PAYRUNS}/${this.payrun.id}/batch/update`).then(({data}) => {
                this.inProgress = false;
                this.batchDetails = {};
                this.$toastr.s(this.$t('payrun_execution_completed'));
                this.$emit('reload');
            })
        },
        openConflictedPayslip() {
            this.$emit('openConflictedPayslip', this.payrun.id)
        },
        payrunTitle(dateRange) {
            if (!dateRange || Object.keys(dateRange).length === 0) return this.$t('multiple_time_period')
            return payrunTitleByDateRange(dateRange);
        },
        timeFormat(date) {
            if (!date) return '';
            return calenderTime(date, false);
        },
        deletePayrunClicked(id) {
            this.confirmationModalActive = true;
            this.promptTitle = this.$t('are_you_sure');
            this.promptMessage = this.$t('this_content_will_be_deleted_permanently');
            this.promptClass = 'danger';
            this.promptIcon = 'trash';
            this.actionType = 'delete';
            this.deleteUrl = `${this.apiUrl.PAYRUNS}/${id}`
        },
        sendPayslipsClicked(id) {
            this.confirmationModalActive = true;
            this.promptIcon = 'send';
            this.promptClass = 'primary';
            this.promptMessage = this.$t('are_you_sure');
            this.promptTitle = this.$t('sending_all_payslip_of_this_payrun');
            this.actionType = 'sendPayslip';
            this.payslipUrl = `${this.apiUrl.PAYRUNS}/${id}/send-payslip`
        },
        confirmed() {
            if (this.actionType === 'delete') {
                this.deletePayrun();
            } else if (this.actionType === 'sendPayslip') {
                this.sendPayslips();
            }
        },
        cancelled() {
            this.confirmationModalActive = false;
            this.deleteUrl = ''
            this.payslipUrl = ''
            this.actionType = ''
        },
        updatePayrun(id) {
            this.$emit('openManualPayrun', id)
        },
        deletePayrun() {
            this.loading = true;
            axiosDelete(this.deleteUrl).then(({data}) => {
                this.$toastr.s('', data.message);
                this.deleteUrl = ''
            }).catch(({response}) => {
                this.$toastr.e('', response.data.message);
            }).finally(() => {
                this.$emit('reload');
                this.closeConfirmation();
            })
        },
        sendPayslips() {
            this.loading = true;
            axiosGet(this.payslipUrl).then((response) => {
                this.$toastr.s(response.data.message);
                this.payslipUrl = '';
            }).finally(() => {
                this.$emit('reload');
                this.closeConfirmation();
            })
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        }
    }
}
</script>

<style scoped>

</style>