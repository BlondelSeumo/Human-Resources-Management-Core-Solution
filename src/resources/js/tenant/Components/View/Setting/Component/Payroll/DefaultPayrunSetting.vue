<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else @submit.prevent="submitData" ref="form" :data-url="apiUrl.PAYROLL_SETTINGS">

        <app-note
            class="mb-primary"
            :title="$t('how_payrun_work')"
            content-type="html"
            :notes="`<ol>
                        <li>${$t('default_payrun_is_applicable_to_generate_payslip_for_all_employees_whenever_it_execute_from_payrun_module',{
                            payrun : `<a href='${urlGenerator(apiUrl.PAYRUN_FRONTEND)}'>${$t('payrun')}</a>`
                        })}</li>
                        <li>${$t('you_can_set_payrun_individually_over_the_default_from_the_employees_details',{
                            employees : `<a href='${urlGenerator(apiUrl.EMPLOYEES_FRONTEND)}'>${$t('employees')}</a>`
                        })}</li>
                     </ol>`"
        />

        <app-form-group
            type="select"
            page="page"
            :label="$t('payrun_period')"
            v-model="formData.payrun_period"
            :error-message="$errorMessage(errors, 'payrun_period')"
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
            :label="$t('payrun_generating_type')"
            radio-checkbox-name="consider_type"
            v-model="formData.consider_type"
            :error-message="$errorMessage(errors, 'consider_type')"
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

        <div class="row mt-4">
            <div class="col-3"></div>
            <div class="col-9">
                <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
            </div>
        </div>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../../common/Helper/AxiosHelper";

export default {
    name: "DefaultPayrunSetting",
    mixins: [FormHelperMixins],
    data() {
        return {}
    },
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getSetting() {
            this.preloader = true;
            axiosGet(`${this.apiUrl.PAYROLL_SETTINGS}?context=payrun`).then(({data}) => {
                this.formData = {
                    payrun_period: data.setting?.payrun_period,
                    consider_type: data.setting?.consider_type,
                    consider_overtime: data.setting && data.setting.consider_type !== 'none' ? parseInt(data.setting.consider_overtime) : true,
                }
            }).finally(() => {
                this.preloader = false;
            })
        },
        submitData() {
            if (this.formData.consider_type === 'none'){
                this.formData.consider_overtime = false;
            }
            this.setBasicFormActionData()
                .save(this.formData);
        },
    },
    mounted() {
        this.getSetting();
    }
}
</script>
