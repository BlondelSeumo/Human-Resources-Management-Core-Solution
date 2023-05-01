<template>
    <modal id="employee-payrun-period-modal"
           size="large"
           v-model="showModal"
           :title="userPayrun ? this.$t('edit_payrun') : this.$t('add_payrun')"
           @submit="submit"
           :preloader="preloader">
        <app-form-group
            type="select"
            page="page"
            :label="$t('payrun_period')"
            v-model="formData.payrun_period"
            :list="[
                {id:'', value:this.$fieldLabel('choose', 'payrun_period'), disabled: true},
                {id:'monthly', value:this.$t('monthly')},
                {id:'weekly', value:this.$t('weekly')}
             ]">
            <template slot="suggestion">
                <small class="text-muted font-italic"
                       v-if="formData.payrun_period === 'monthly'">
                    {{ $t('always_run_for_the_last_month') }}
                </small>
            </template>
        </app-form-group>

        <app-form-group
            type="radio"
            page="page"
            class="mt-primary"
            :label="$t('payrun_generating_type')"
            radio-checkbox-name="consider_type"
            v-model="formData.consider_type"
            :list="[
                {id:'hour', value:this.$t('hour')},
                {id:'daily_log', value:this.$t('daily_log')},
                {id:'none', value:this.$t('none')}
             ]"
        />

        <div v-if="formData.consider_type === 'hour' || formData.consider_type === 'daily_log'" class="mt-primary">
            <app-form-group
                label-alignment=""
                type="switch"
                page="page"
                :label="this.$t('consider_overtime')"
                v-model="formData.consider_overtime"
            >
                <template slot="suggestion" v-if="formData.consider_overtime">
                    <small class="mt-2 font-italic">
                        ({{ $t('overtime_will_be_calculated_after_the_end_of_the_total_schedule_time') }})
                    </small>
                </template>
            </app-form-group>
        </div>

        <template slot="footer-space" v-if="employeePayrun">
            <button type="button"
                    class="btn btn-success mr-3"
                    @click="restorePayrunPeriod">
                {{ this.$t('restore_to_default') }}
            </button>
        </template>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet, axiosPost} from "../../../../../../../common/Helper/AxiosHelper";

export default {
    name: "EmployeePayrunPeriodModal",
    mixins:[ModalMixin],
    props: {
        userPayrun: {},
        employeeId: {},
        employeePayrun: {},
    },
    data() {
        return {
            formData: {
                payrun_period: this.userPayrun?.payrun_period,
                consider_type: this.userPayrun?.consider_type,
                consider_overtime: this.userPayrun && this.userPayrun.consider_type !== 'none' ? parseInt(this.userPayrun.consider_overtime) : true,
            },
        }
    },
    methods:{
        submit(){
            this.preloader = true;
            if (this.formData.consider_type === 'none'){
                this.formData.consider_overtime = false;
            }
            axiosPost(`${this.apiUrl.EMPLOYEES}/${this.employeeId}/payrun-setting/update-payrun`, this.formData)
                .then( response => {
                    $('#employee-payrun-period-modal').modal('hide');
                    this.$toastr.s('', response.data.message);
                    this.$emit('reload')
                }).finally(() => {
                this.preloader = false;
            })
        },
        restorePayrunPeriod(){
            this.preloader = true;
            axiosGet(`${this.apiUrl.EMPLOYEES}/${this.employeeId}/payrun-setting/restore?type=payrun-period`)
                .then( response => {
                    $('#employee-payrun-period-modal').modal('hide');
                    this.$toastr.s('', response.data.message);
                    this.$emit('reload')
                }).finally(() => {
                this.preloader = false;
            })
        }
    }
}
</script>
