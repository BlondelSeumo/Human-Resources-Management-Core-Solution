<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else @submit.prevent="submitData" ref="form" :data-url="`${apiUrl.PAYROLL_SETTINGS}/audience`">

                <app-note
                    class="mb-primary"
                    :title="$t('restriction_note')"
                    content-type="html"
                    :notes="`<p class='m-0'>${$t('by_default_all_users_are_eligible_for')} <span class='font-italic'>${$t('payrun_and_beneficiary_badges')}</span></p>
                             <p>${$t('user_restriction_note')} <span class='font-italic'>${$t('payrun_and_beneficiary_badges')}</span></p>`"
                />

        <div class="">
            <div class="row mt-6">
                <div class="col-3">
                    <label>{{ $t('restricted_user') }}</label><br/>
                    <label>({{ $t('for_payrun') }})</label>
                </div>
                <div class="col-9">
                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('department_preference')"
                        list-value-field="name"
                        v-model="formData.payrun_departments"
                        :fetch-url="apiUrl.SELECTABLE_PAYRUN_DEPARTMENTS"
                        :chooseAll="false"
                    />

                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('user_preference')"
                        list-value-field="full_name"
                        v-model="formData.payrun_users"
                        :fetch-url="`${apiUrl.TENANT_SELECTABLE_PAYRUN_USER}${formData.payrun_departments ? `?excluded_departments=${formData.payrun_departments}` : ''}`"
                        :chooseAll="false"
                    />
                </div>
            </div>
        </div>

        <div class="mt-primary">
            <div class="row mt-6">
                <div class="col-3">
                    <label>{{ $t('restricted_user') }}</label><br/>
                    <label>({{ $t('for_beneficiary_badge') }})</label>
                </div>
                <div class="col-9">
                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('employment_status_preference')"
                        list-value-field="name"
                        v-model="formData.employment_statuses"
                        :fetch-url="`${apiUrl.SELECTABLE_EMPLOYMENT_STATUS}?excluded=terminated`"
                        :chooseAll="false"
                    />

                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('department_preference')"
                        list-value-field="name"
                        v-model="formData.beneficiary_departments"
                        :fetch-url="apiUrl.SELECTABLE_PAYRUN_DEPARTMENTS"
                        :chooseAll="false"
                    />

                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('user_preference')"
                        list-value-field="full_name"
                        v-model="formData.beneficiary_users"
                        :fetch-url="`${apiUrl.TENANT_SELECTABLE_PAYRUN_USER}${formData.beneficiary_departments ? `?excluded_departments=${formData.beneficiary_departments}` : ''}`"
                        :chooseAll="false"
                    />
                </div>
            </div>
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
    name: "PayrunAudienceSetting",
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
            axiosGet(`${this.apiUrl.PAYROLL_SETTINGS}/audience`).then(({data}) => {
                this.formData = {
                    payrun_departments: data.payrun.departments ? JSON.parse(data.payrun.departments) : [],
                    payrun_users: data.payrun.users ? JSON.parse(data.payrun.users) : [],
                    beneficiary_departments: data.beneficiary.departments ? JSON.parse(data.beneficiary.departments) : [],
                    beneficiary_users: data.beneficiary.users ? JSON.parse(data.beneficiary.users) : [],
                    employment_statuses: data.beneficiary.employment_statuses ? JSON.parse(data.beneficiary.employment_statuses) : [],
                }
            }).finally(() => {
                this.preloader = false;
            })
        },
    },
    mounted() {
        this.getSetting();
    }
}
</script>

<style scoped>

</style>