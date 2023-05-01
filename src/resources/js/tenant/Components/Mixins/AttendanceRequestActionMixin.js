import {filterLastPendingData} from "../View/Attendance/Helper/Helper";
import {axiosPost} from "../../../common/Helper/AxiosHelper";

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
            isAttendanceLogModalActive: false,
            changeLogUrl: '',
        }
    },
    methods: {
        triggerActions(row, action, active){
            this.formData = (action.type && action.type === 'requestTableRow')
                ? filterLastPendingData(row?.details): (action.type && action.type === 'dailyLogTableRow')
                    ? this.collection(row.details).first(): row;

            if(action.name === 'approve' || action.name === 'reject' || action.name === 'cancel') {
                this.modalIcon = action.modalIcon;
                this.modalClass = action.modalClass;
                this.modalSubtitle = action.modalSubtitle;
                this.formData.status_name = action.name;
                this.statusChangeUrl = `${this.apiUrl.ATTENDANCES}/${this.formData.id}/status/${this.formData.status_name}`
                this.confirmationModalActive = true;
            }else if (action.name === 'edit'){
                this.selectedUrl = `${this.apiUrl.ATTENDANCES}/details/${this.formData.id}`;
                this.isEditModalActive = true;
            }else if(action.name === 'change-log'){
                this.changeLogUrl = `${this.apiUrl.ATTENDANCES}/${this.formData.id}/log`
                this.isAttendanceLogModalActive = true

            }
        },
        cancelled(){
            this.formData = {};
            this.statusChangeUrl = ''
            this.confirmationModalActive = false;
        },
        updateStatus(){
            axiosPost(
                this.statusChangeUrl,
                this.formData
            ).then(({data}) => {
                this.formData = {};
                this.statusChangeUrl = ''
                this.confirmationModalActive = false;
                this.$toastr.s('', data.message);
                this.$hub.$emit(`reload-${this.tableId ? this.tableId : this.id}`);
            }).catch(({response}) => {
                this.$toastr.e('', response.data.message);
            })
        }
    }
}