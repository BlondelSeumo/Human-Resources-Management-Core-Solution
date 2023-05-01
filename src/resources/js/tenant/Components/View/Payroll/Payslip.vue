<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$t('payslip')">
            
            <app-default-button
                v-if="$can('export_payslips')"
                btn-class="btn btn-success mr-1"
                :title="$t('export_all')"
                @click="viewConfirmationModal()"
            />

            <app-default-button
                :title="this.$t('send_filtered_payslip')"
                btn-class="btn btn-success mr-1"
                v-if="$can('send_bulk_payslip')"
                @click="filteredPayslipConfirmation"
            />
            <app-default-button
                :title="this.$t('send_monthly_payslip')"
                v-if="$can('send_bulk_payslip')"
                @click="monthlyPayslipConfirmation"
            />
        </app-page-top-section>

        <div v-if="payrun" class="mb-3">
            <h6>
                Showing payslips of payrun id: <span class="text-primary px-1 font-weight-bold">{{ payrun }}</span>
                |<a :href="urlGenerator(apiUrl.PAYSLIP_FRONTEND)" class="btn btn-link text-primary pl-2">Clear</a>
            </h6>
        </div>
        <app-filter-with-search
            :options="options"
            @filteredValue="setFilterValues"
            @searchValue="setSearchValue"
        />

        <!--        <app-overlay-loader v-if="preloader"/>-->

        <div class="card card-with-shadow border-0" style="min-height: 400px;">
            <div>
                <div
                    class="card-header d-flex align-items-center justify-content-between primary-card-color p-primary mb-primary">
                    <app-month-calendar/>

                    <app-period-calendar :filters="periodFilters"
                                         :initial-filter-value="payrun ? 'total' : 'lastMonth'"/>
                </div>
            </div>

            <app-table
                id="payslip-table"
                :options="tableOptions"
                @action="triggerActions"
            />
        </div>

        <app-confirmation-modal
            v-if="exportConfirmationModal"
            :title="modalSubtitle"
            :message="modalMessage"
            modal-id="app-confirmation-modal"
            modal-class="primary"
            icon="download"
            :first-button-name="$t('export')"
            :second-button-name="$t('cancel')"
            @confirmed="exportFilteredAsset()"
            @cancelled="exportConfirmationModal = false"
            :self-close="false"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :loading="loading"
            :message="promptMessage"
            :title="promptTitle"
            :second-button-name="actionType === 'none' ? $t('okay') : $t('no')"
            :hide-first-button="actionType === 'none'"
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
            :settings="payslipSettings"
        />

        <app-payslip-edit-modal
            v-if="payslipEditModal"
            v-model="payslipEditModal"
            :payslip="selectedPayslip"
            :settings="payslipSettings"
            @updated="afterUpdated"
        />

        <app-payslip-conflict-modal
            v-if="payslipConflictModal"
            v-model="payslipConflictModal"
            :payslip="selectedPayslip"
            @input="setUrlAndReloadTable"
        />
    </div>
</template>

<script>
import PayslipMixin from "../../Mixins/PayslipMixin";
import {PAYSLIP} from "../../../Config/ApiUrl";
import {axiosGet, urlGenerator} from "../../../../common/Helper/AxiosHelper";
import {localTimeZone} from "../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "Payslip",
    mixins: [PayslipMixin],
    props: {
        payrun: {}
    },
    data() {
        return {
            urlGenerator,
            confirmationModalActive: false,
            promptClass: '',
            promptIcon: '',
            promptMessage: '',
            payslipViewModal: false,
            payslipEditModal: false,
            payslipConflictModal: false,
            selectedPayslip: {},
            actionType: '',
            loading: false,
            promptTitle: this.$t('are_you_sure'),
            payslipSettings: {},
            exportConfirmationModal: false,
            modalMessage: '',
            modalSubtitle: '',
        }
    },
    created() {
        this.getPayslipSettings();
        this.$store.dispatch('getStatuses', 'payslip')
    },
    methods: {
        monthlyPayslipConfirmation() {
            this.confirmationModalActive = true;
            this.promptIcon = 'send';
            this.promptClass = 'primary';
            this.promptMessage = this.$t('are_you_sure');
            this.promptTitle = this.$t('sending_for_last_month');
            this.actionType = 'sendMonthly';
        },
        filteredPayslipConfirmation() {
            if (this.tableData < 1) {
                this.promptTitle = this.$t('no_payslip_found');
                this.promptMessage = this.$t('please_filter_your_payslip_first');
                this.promptClass = 'warning';
                this.actionType = 'none';
            } else {
                this.promptClass = 'primary';
                this.promptMessage = this.$t('are_you_sure');
                this.promptTitle = this.$t('sending_filtered_payslip');
                this.actionType = 'sendFiltered';
            }
            this.promptIcon = 'filter';
            this.confirmationModalActive = true;
        },
        setUrlAndReloadTable() {
            this.tableOptions.url = this.payrun ? `${PAYSLIP}?${this.queryString}&payrun=${this.payrun}` : `${PAYSLIP}?${this.queryString}`;
            this.$hub.$emit('reload-payslip-table');
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
        viewConfirmationModal() {
            this.modalSubtitle = this.$t('all_payslip_export_title');
            this.modalMessage = this.$t('all_payslip_export_message');
            this.exportConfirmationModal = true;
        },
        exportFilteredAsset() {
            window.location = urlGenerator(`${this.apiUrl.EXPORT}/payslip/all?timeZone=${localTimeZone}`);
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.exportConfirmationModal = false;
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
                this.promptTitle = this.$t('are_you_sure');
                this.promptMessage = this.$t('this_content_will_be_deleted_permanently');
            } else if (action.actionName === 'view_pdf') {
                window.open(urlGenerator(`${PAYSLIP}/${row.id}/pdf`), "_blank") || window.location.replace(urlGenerator(`${PAYSLIP}/${row.id}/pdf`));
            } else if (action.actionName === 'download_pdf') {
                window.open(urlGenerator(`${PAYSLIP}/${row.id}/pdf?download=true`), "_blank") || window.location.replace(urlGenerator(`${PAYSLIP}/${row.id}/pdf?download=true`));
            } else if (action.actionName === 'manage_conflict') {
                this.selectedPayslip = row
                this.payslipConflictModal = true;
            }
        },
        sendPayslipMail() {
            this.loading = true;
            axiosGet(`${PAYSLIP}/${this.selectedPayslip.id}/send`).then((response) => {
                this.$toastr.s(response.data.message);
            }).finally(() => {
                this.$hub.$emit('reload-payslip-table');
                this.closeConfirmation();
            })
        },
        sendMonthlyPayslip(filtered = false) {
            this.loading = true;
            axiosGet(`${PAYSLIP}/send-monthly?${filtered ? this.queryString : 'within=lastMonth'}`).then((response) => {
                this.$toastr.s(response.data.message);
            }).finally(() => {
                this.$hub.$emit('reload-payslip-table');
                this.closeConfirmation();
            })
        },
        deletePayslip() {
            this.loading = true;
            axiosGet(`${PAYSLIP}/${this.selectedPayslip.id}/delete`).then((response) => {
                this.$toastr.s(response.data.message);
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
        getPayslipSettings() {
            axiosGet(`${PAYSLIP}/settings`).then(({data}) => {
                this.payslipSettings = data;
            })
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
                this.setUrlAndReloadTable()
            },
            immediate: true
        }
    },
}
</script>

<style scoped>

</style>