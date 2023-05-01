<template>
    <modal id="user-invite"
           v-model="showModal"
           :title="$fieldTitle('invite', 'user', true)"
           @submit="submitData"
           :btn-label="$t('invite')"
           :scrollable="false"
           :loading="loading"
           :preloader="preloader"
    >
        <form ref="form" :data-url='submitURL' v-if="isMailSettingExist">
            <app-form-group
                type="email"
                :label="$t('email')"
                :placeholder="$placeholder('email')"
                v-model="formData.email"
                :required="true"
                :error-message="$errorMessage(errors, 'email')"
            />

            <app-form-group
                :label="$t('roles')"
                type="multi-select"
                :list="roles"
                v-model="formData.roles"
                :error-message="$errorMessage(errors, 'roles')"
                list-value-field="name"
                :isAnimatedDropdown="true"
            />
        </form>
        <app-note v-else
                  :title="$t('no_delivery_settings_found')"
                  :notes="`<ol>
                     <li>${$t('cron_job_settings_warning',{
                      documentation: `<a href='https://payday.gainhq.com/documentation/important-settings.html#scheduler-queue' target='_blank'>${$t('documentation')}</a>`
                  })}</li>
                     <li>${$t('no_delivery_settings_warning', {
                      location: `<a href='${urlGenerator(TENANT_EMAIL_SETUP_SETTING)}'>${$t('here')}</a>`
                  })}</li>
                  </ol>`"
                  content-type="html"
        />
        <template v-if="!isMailSettingExist" slot="footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                {{ $t('close') }}
            </button>
        </template>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../Mixin/Global/ModalMixin";
import {MAIL_CHECK_URL, TENANT_USER_INVITE, DELIVERY_SETTINGS_FRONT_END, TENANT_MAIL_CHECK_URL,TENANT_DELIVERY_SETTINGS_FRONT_END} from
        "../../../Config/apiUrl";
import {axiosGet, urlGenerator} from "../../../Helper/AxiosHelper";

export default {
    name: "UserInviteModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: ['roles', 'alias'],
    data() {
        return {
            formData: {
                roles: []
            },
            isMailSettingExist: false,
            settingsLocation: urlGenerator(this.alias === 'tenant' ? TENANT_DELIVERY_SETTINGS_FRONT_END : DELIVERY_SETTINGS_FRONT_END)
        }
    },
    methods: {
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'user-table');
            $("#user-invite").modal('hide')
        },

        afterSuccessFromGetEditData({data}) {
            this.formData = data;
        },

        checkMailSettings () {
            this.preloader = true;
            const url = this.alias === 'tenant' ? TENANT_MAIL_CHECK_URL : MAIL_CHECK_URL
            axiosGet(url).then(response => {
                this.isMailSettingExist = !!response.data;
            }).finally(() => {
                this.preloader = false;
            });
        }
    },
    computed: {
        submitURL() {
            return this.alias === 'tenant' ? TENANT_USER_INVITE : `admin/auth/users/invite-user`;
        }
    },

    created() {
        this.checkMailSettings();
    }


}
</script>

