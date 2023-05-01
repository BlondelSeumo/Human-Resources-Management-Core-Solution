<template>
    <div class="content-wrapper">
        <app-note
            class="mb-primary"
            :title="$t('note')"
            content-type="html"
            :notes="`<ol>
                        <li>${$t('any_type_of_change_will_be_effected_from_next_day')}</li>
                        <li>${$t('how_leave_settings_work_message',{
                            link: `<a href='https://payday.gainhq.com/documentation/instruction-guide.html#appLeave' target='_blank'>${$t('documentation')}</a>`
                        })}</li>
                        <li>${$t('leave_carry_forward_message')}</li>
                    </ol>`"
        />
        <app-overlay-loader v-if="preloader"/>
        <form v-else @submit.prevent="submitData" ref="form" :data-url="apiUrl.LEAVE_SETTINGS">
            <app-form-group
                page="page"
                :label="$t('leave_year_start_from')"
                type="select"
                v-model="formData.start_month"
                :list="months"
                :error-message="$errorMessage(errors, 'start_month')"
            />
            <div>
                <div class="row mt-6">
                    <div class="col-3">
                        <label>{{ $t('employees_for_allowance') }}</label><br/>
                        <small class="text-muted font-italic">{{ $t('add_employee_status_message') }}</small>
                    </div>
                    <div class="col-9">
                        <app-form-group-selectable
                            type="multi-select"
                            :label="$t('for_paid_leave')"
                            list-value-field="name"
                            v-model="formData.statuses_for_paid_leave"
                            :error-message="$errorMessage(errors, 'statuses_for_paid_leave')"
                            :fetch-url="apiUrl.SELECTABLE_EMPLOYMENT_STATUS"
                            :chooseAll="false"
                        />

                        <app-form-group-selectable
                            type="multi-select"
                            :label="$t('for_unpaid_leave')"
                            list-value-field="name"
                            v-model="formData.statuses_for_unpaid_leave"
                            :error-message="$errorMessage(errors, 'statuses_for_unpaid_leave')"
                            :fetch-url="apiUrl.SELECTABLE_EMPLOYMENT_STATUS"
                            :chooseAll="false"
                        />
                    </div>
                </div>
                <div class="row">
                    <div class="col-3"></div>
                    <div class="col-9">
                        <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {LEAVE_SETTINGS} from "../../../../Config/ApiUrl";
import moment from "moment";

export default {
    name: "LeaveAllowancePolicy",
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
            },
        }
    },
    computed: {
        months() {
            return Array.apply(0, Array(12)).map((_, i) => {
                return {
                    id: moment().locale('en').month(i).format('MMM'),
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
                    roles: data.roles ? JSON.parse(data.roles) : [],
                    users: data.users ? JSON.parse(data.users) : [],
                    statuses_for_paid_leave: data.statuses_for_paid_leave ? JSON.parse(data.statuses_for_paid_leave) : [],
                    statuses_for_unpaid_leave: data.statuses_for_unpaid_leave ? JSON.parse(data.statuses_for_unpaid_leave) : [],
                }
            }).finally(() => {
                this.preloader = false;
            })
        }
    },
    mounted() {
        this.getSettings();
    }
}
</script>
