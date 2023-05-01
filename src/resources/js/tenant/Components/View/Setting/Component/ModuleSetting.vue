<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else method="post" ref="form" :data-url="apiUrl.MODULE_SETTINGS">


        <div class="form-group row">
            <label for="appSettingsCompanyName" class="col-lg-4 col-xl-4 col-md-4 col-sm-4">
                {{ $t('module_list') }}<br>
                <small class="text-muted font-italic">
                    {{ $t('module_setting_suggestion') }}
                </small>
            </label>

            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-8">
                <app-input
                    type="checkbox"
                    :list="moduleList"
                    v-model="formData.list"
                    custom-checkbox-type="checkbox-default"
                    :error-message="$errorMessage(errors, 'list')"
                />
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
            </div>
        </div>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "ModuleSetting",
    mixins: [FormHelperMixins],
    data() {
        return {
            moduleList: [
                {id: 'job_desk', value: this.$t('job_desk')},
                {id: 'employee', value: this.$t('employee')},
                {id: 'attendance', value: this.$t('attendance')},
                {id: 'leave', value: this.$t('leave')},
                {id: 'payroll', value: this.$t('payroll')},
                {id: 'administration', value: this.$t('administration')},
                {id: 'asset', value: this.$t('assets')},
            ],
            formData: {
                list: [],
            }
        }
    },
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
            location.reload();
        },
        getModuleSettings() {
            this.preloader = true;
            axiosGet(this.apiUrl.MODULE_SETTINGS).then(({data}) => {
                this.formData.list = data.list ? JSON.parse(data.list) : [];
            }).finally(() => this.preloader = false)
        }
    },
    mounted() {
        this.getModuleSettings();
    }
}
</script>
