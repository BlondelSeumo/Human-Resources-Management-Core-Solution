import {axiosPatch} from "../../../common/Helper/AxiosHelper";
import {EMPLOYEES} from "../../Config/ApiUrl";

export default {
    data() {
        return {
            attendanceModalActive: false,
            leaveModalActive: false,
            confirmationModalActive: false,
            promptAction: '',
            promptTitle: '',
            promptMessage: '',
            modalClass: '',
            icon: '',
            loading: false,
            employmentStatusModalActive: false,
            isTerminationReasonModalActive: false,
            isJobHistoryEditModalActive: false,
            modalAction: '',
        }
    },
    methods: {
        openAttendanceModal() {
            this.attendanceModalActive = true;
        },
        openLeaveModal() {
            this.leaveModalActive = true;
        },
        employeeAction(action) {
            this.isJobHistoryEditModalActive = true;
            this.modalAction = action;
        },
        triggerTerminate() {
            this.confirmationModalActive = true;
            this.promptAction = 'terminate';
            this.icon = 'log-out';
            this.modalClass = 'danger';
            this.promptTitle = this.$t('are_you_sure');
            this.promptMessage = this.$t('you_are_going_to_terminate_an_employee');
        },
        triggerRejoin() {
            this.confirmationModalActive = true;
            this.promptAction = 'rejoining';
            this.icon = 'log-in';
            this.modalClass = 'primary';
            this.promptTitle = this.$t('are_you_sure');
            this.promptMessage = this.$t('you_are_permitting_an_employee_for_re_joining');
        },
        triggerConfirm(){
            if (this.promptAction === 'rejoining'){
                this.rejoining();
            }else if (this.promptAction === 'terminate'){
                this.terminate();
            }
        },
        terminate() {
            this.loading = true;
            axiosPatch(`${EMPLOYEES}/${this.employeeId}/terminate`).then(({data}) => {
                this.confirmationModalActive = false;
                this.$toastr.s(data.message);
                this.refreshStatus();
                setTimeout(() => {
                    this.isTerminationReasonModalActive = true;
                })
            }).catch(({data}) => {
                this.confirmationModalActive = false;
                this.$toastr.e(data.message);
            }).finally(() => this.closeConfirmation());
        },
        rejoining() {
            this.confirmationModalActive = false;
            this.employmentStatusModalActive = true;
        },
        refreshStatus(){
            this.$store.dispatch("getEmployeeDetails", this.employeeId);
            this.$store.dispatch('getEmployeeSocialLinks', this.employeeId);
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
        cancelled() {
            this.confirmationModalActive = false;
        }
    },
}