import {axiosPost} from "../../../common/Helper/AxiosHelper";
import {LEAVE_REQUESTS} from "../../Config/ApiUrl";

export default {
    data() {
        return {
            formData: {},
            statusChangeUrl: '',
            modalIcon: '',
            modalClass: '',
            modalSubtitle: '',
            isEditModalActive: false,
            confirmationModalActive: false,
            isResponseLogModalActive: false,
            logUrl: '',
        }
    },
    mounted(){
        this.$hub.$on('getLeaveActivityAction', (row, action, active) => {
            this.triggerActions(row, action, active)
        })
    },
    methods: {
        triggerActions(row, action, active) {
            this.formData = row;
            if (action.name === 'approved' || action.name === 'rejected' || action.name === 'canceled') {
                this.modalIcon = action.modalIcon;
                this.modalClass = action.modalClass;
                this.modalSubtitle = action.modalSubtitle;
                this.formData.status_name = action.name;
                this.statusChangeUrl = `${LEAVE_REQUESTS}/${this.formData.id}/${this.formData.status_name}`
                this.confirmationModalActive = true;
            }else if(action.name === 'response-log'){
                this.logUrl = `${this.apiUrl.LEAVES}/${row.id}/log`
                this.isResponseLogModalActive = true
            }
        },
        cancelled() {
            this.formData = {};
            this.statusChangeUrl = ''
            this.confirmationModalActive = false;
        },
        updateStatus() {
            axiosPost(
                this.statusChangeUrl,
                this.formData
            ).then(({data}) => {
                this.statusChangeUrl = ''
                this.formData = {};
                this.confirmationModalActive = false;
                this.$toastr.s('', data.message);
                this.$hub.$emit(`reload-${this.tableId ? this.tableId : this.id}`);
                this.$hub.$emit('leaveStatusUpdated');
            }).catch(({response}) => {
                this.$toastr.e('', response.data.message);
            })
        }
    }
}