<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else method="post" ref="form" :data-url="apiUrl.ATTENDANCE_SETTINGS">
        <app-form-group
            page="page"
            :label="`${$t('punch_in_time_tolerance')}(${$t('minutes')})`"
            :recommendation="$t('punch_in_time_tolerance_recommendation')"
            v-model="formData.punch_in_time_tolerance"
            type="number"
            :error-message="$errorMessage(errors, 'punch_in_time_tolerance')"
            label-alignment="top">

            <template slot="suggestion">
                <div class="d-flex mt-4">
                    <div class="text-center mr-4">
                        <span class="badge badge-warning rounded-pill mb-2">
                            {{ $t('early') }}
                        </span>
                        <p class="text-muted mb-0">
                            {{ $t('before_on_time') }}
                        </p>
                    </div>
                    <div class="text-center mr-4">
                        <span class="badge badge-success rounded-pill mb-2">
                            {{ $t('regular') }}
                        </span>
                        <p class="text-muted mb-0">
                            {{ $t('on_time_tolerance') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <span class="badge badge-danger rounded-pill mb-2">
                            {{ $t('late') }}
                        </span>
                        <p class="text-muted mb-0">
                            {{ $t('after_tolerance') }}
                        </p>
                    </div>
                </div>
            </template>
        </app-form-group>

        <app-form-group
            page="page"
            :label="`${$t('work_availability_definition')}(${$t('percentage')})`"
            :recommendation="$t('work_availability_definition_recommendation')"
            v-model="formData.work_availability_definition"
            type="number"
            :error-message="$errorMessage(errors, 'work_availability_definition')"
            label-alignment="top">
            <template slot="suggestion">
                <div class="d-flex mt-4">
                    <div class="text-center mr-4">
                        <span class="badge badge-success rounded-pill mb-2">
                            {{ $t('good') }}
                        </span>
                        <p class="text-muted mb-0">
                            {{ $t('equal_to_or_above_of_percentage') }}
                        </p>
                    </div>
                    <div class="text-center">
                        <span class="badge badge-danger rounded-pill mb-2">
                            {{ $t('bad') }}
                        </span>
                        <p class="text-muted mb-0">
                            {{ $t('bellow_percentage') }}
                        </p>
                    </div>
                </div>
            </template>
        </app-form-group>

        <app-form-group
            :recommendation="$t('punch_in_out_alert_suggestion')"
            :label="$t('punch_in_out_alert')"
            v-model="formData.punch_in_out_alert"
            page="page"
            type="switch"
            :error-message="$errorMessage(errors, 'auto_approval')"
            label-alignment="top">
        </app-form-group>

        <div class="form-group row">
            <label for="appSettingsCompanyName" class="col-lg-3 col-xl-3 col-md-3 col-sm-12">
                {{ $t('alert_area') }}<br>
                <small class="text-muted font-italic">
                    {{ $t('alert_area_suggestion') }}
                </small>
            </label>

            <div class="col-lg-8 col-xl-8 col-md-8 col-sm-12">
                <app-input
                    type="checkbox"
                    :list="[{id: 'web', value: $t('web')},{id: 'system', value: $t('system')}]"
                    v-model="formData.alert_area"
                    radio-checkbox-wrapper="row"
                    custom-checkbox-type="checkbox-default mr-5"
                    :error-message="$errorMessage(errors, 'alert_area')"
                />
            </div>
        </div>

        <app-form-group
            :recommendation="$t('in_seconds')"
            page="page"
            :label="$t('punch_in_out_interval')"
            type="number"
            v-model="formData.punch_in_out_interval"
            :error-message="$errorMessage(errors, 'punch_in_out_interval')"
        />

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
    name: "AttendanceDefinitionSettings",
    mixins: [FormHelperMixins],
    data() {
        return {
            work_availability_definition: 80,
            punch_in_time_tolerance: 15,
            formData: {
                punch_in_out_alert: false,
                punch_in_out_interval: 0,
                alert_area: [],
            }
        }
    },
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getAttendanceSettings() {
            this.preloader = true;
            axiosGet(this.apiUrl.ATTENDANCE_SETTINGS).then(({data}) => {
                this.formData = data;
                this.formData.punch_in_out_alert = Boolean(parseInt(data.punch_in_out_alert))
                this.formData.alert_area = data.alert_area ? JSON.parse(data.alert_area) : [];

            }).finally(() => this.preloader = false)
        }
    },
    mounted() {
        this.getAttendanceSettings();
    }
}
</script>
