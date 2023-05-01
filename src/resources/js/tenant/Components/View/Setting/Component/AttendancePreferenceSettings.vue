<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else @submit.prevent="submitData" ref="form" :data-url="apiUrl.ATTENDANCE_SETTINGS">
        <app-form-group
            :label="$t('auto_approval')"
            v-model="formData.auto_approval"
            page="page"
            type="switch"
            :error-message="$errorMessage(errors, 'auto_approval')"
            label-alignment="top">
            <template slot="suggestion">
                {{ auto_approval_suggestion }}
            </template>
        </app-form-group>
        <div v-show="formData.auto_approval">
            <div class="row mt-6">
                <div class="col-3">
                    <label>{{ $t('manage_audience') }}</label><br/>
                    <small class="text-muted font-italic">{{ $t('manage_audience_message') }}</small>
                </div>
                <div class="col-9">
                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('by_role')"
                        list-value-field="name"
                        v-model="formData.roles"
                        :error-message="$errorMessage(errors, 'roles')"
                        :fetch-url="apiUrl.TENANT_SELECTABLE_ROLES"
                        :chooseAll="true"
                    />

                    <app-form-group-selectable
                        type="multi-select"
                        :label="$t('by_user')"
                        list-value-field="full_name"
                        v-model="formData.users"
                        :error-message="$errorMessage(errors, 'users')"
                        :fetch-url="apiUrl.TENANT_SELECTABLE_ATTENDANCE_USERS"
                        :chooseAll="true"
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
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "AttendancePreferenceSettings",
    mixins: [FormHelperMixins],
    data() {
        return {
            formData: {
                roles: [],
                users: [],
                auto_approval: false
            },
            auto_approval_suggestion: this.$t('enabled_auto_approval_suggestion')
        }
    },
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getSetting() {
            this.preloader = true;
            axiosGet(this.apiUrl.ATTENDANCE_SETTINGS).then(({data}) => {
                this.formData = {
                    roles: data.roles ? JSON.parse(data.roles) : [],
                    users: data.users ? JSON.parse(data.users) : [],
                    auto_approval: Boolean(parseInt(data.auto_approval))
                }
            }).finally(() => {
                this.preloader = false;
            })
        }
    },
    mounted() {
        this.getSetting();
    },
    watch: {
        'formData.auto_approval': {
            handler: function (value) {
                if (!value) {
                    this.formData = {
                        roles: [],
                        users: [],
                        auto_approval: false
                    }
                    this.submitData();
                    return this.auto_approval_suggestion = this.$t('enabled_auto_approval_suggestion')
                }
                this.auto_approval_suggestion = this.$t('disabled_auto_approval_suggestion')
            }
        }
    }
}
</script>