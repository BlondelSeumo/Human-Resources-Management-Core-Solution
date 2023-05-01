<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else @submit.prevent="submitData" ref="form" :data-url="apiUrl.LEAVE_SETTINGS">

        <app-form-group
            :label="$t('request_approval_type')"
            v-model="formData.approval_level"
            page="page"
            type="radio"
            radio-checkbox-wrapper="row"
            custom-radio-type="checkbox-default col-md-4"
            label-alignment="align-item-start"
            :error-message="$errorMessage(errors, 'approval_level')"
            :list="[{id:'single', value:this.$t('single_level')}, {id:'multi', value:this.$t('multi_level')}]"
        >
            <template slot="suggestion">
                <app-note
                    :show-title="false"
                    class="mb-primary mt-4"
                    content-type="html"
                    :notes="formData.approval_level === 'multi' ? `<strong>${$t('multi_level')} ${$t('note')}: </strong> ${$t('multi_level_leave_approval_note')}` :
                     `<strong>${$t('single_level')} ${$t('note')}:</strong> ${$t('single_level_leave_approval_note')}`"
                />
            </template>
        </app-form-group>

        <div v-if="formData.approval_level === 'single'" class="mt-5">
            <div class="row mt-6">
                <div class="col-3">
                    <label>{{ $t('special_audience') }}</label><br/>
                    <small class="text-muted font-italic">{{ $t('manage_leave_audience_message') }}</small>
                </div>
                <div class="col-9">
                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('by_role')"
                        list-value-field="name"
                        v-model="formData.roles"
                        :error-message="$errorMessage(errors, 'roles')"
                        :fetch-url="apiUrl.TENANT_SELECTABLE_ROLES"
                        :chooseAll="false"
                    />

                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('by_user')"
                        list-value-field="full_name"
                        v-model="formData.users"
                        :error-message="$errorMessage(errors, 'users')"
                        :fetch-url="apiUrl.TENANT_SELECTABLE_LEAVE_USERS"
                        :chooseAll="false"
                    />
                </div>
            </div>
        </div>
        <div v-else class="mt-5">
            <app-form-group
                :label="$t('allow_bypass')"
                v-model="formData.allow_bypass"
                page="page"
                type="switch"
                >
                <template slot="suggestion">
                    {{ allow_bypass_suggestion }}
                </template>
            </app-form-group>
        </div>
        <div class="row mt-3">
            <div class="col-3"></div>
            <div class="col-9">
                <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
            </div>
        </div>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {LEAVE_SETTINGS} from "../../../../Config/ApiUrl";
import moment from "moment";

export default {
    name: "LeaveApprovalSetting",
    mixins: [FormHelperMixins],
    data() {
        return {
            formData: {
                start_month: '',
                statuses_for_paid_leave: [],
                statuses_for_unpaid_leave: [],
                approval_level: 'single',
                roles: [],
                users: [],
                allow_bypass: false,
            },
            single_level_suggestion: this.$t('single_level_suggestion'),
            allow_bypass_suggestion: this.$t('allow_bypass_suggestion')
        }
    },
    computed: {
        months() {
            return Array.apply(0, Array(12)).map((_, i) => {
                return {
                    id: moment().month(i).format('MMM'),
                    value: moment().locale(window.appLanguage).month(i).format('MMMM')
                };
            });
        },
    },
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getSettings() {
            this.preloader = true;
            axiosGet(LEAVE_SETTINGS).then(({data}) => {
                this.formData = {
                    start_month: data.start_month ? data.start_month : 'Jan',
                    approval_level: data.approval_level ? data.approval_level : 'single',
                    allow_bypass: Boolean(parseInt(data.allow_bypass)),
                    roles: data.roles ? JSON.parse(data.roles) : [],
                    users: data.users ? JSON.parse(data.users) : [],
                    statuses_for_paid_leave: data.statuses_for_paid_leave ? JSON.parse(data.statuses_for_paid_leave) : [],
                    statuses_for_unpaid_leave: data.statuses_for_unpaid_leave ? JSON.parse(data.statuses_for_unpaid_leave) : [],
                }
            }).finally(() => {
                this.preloader = false;
            })
        },
        submitData() {
            if (this.formData.approval_level === 'multi'){
                this.formData = {
                    ...this.formData,
                    roles: [],
                    users: [],
                }
            }else{
                this.formData = {
                    ...this.formData,
                    allow_bypass: false,
                }
            }
            this.setBasicFormActionData()
                .save(this.formData);
        },
    },
    mounted() {
        this.getSettings();
    }
}
</script>
