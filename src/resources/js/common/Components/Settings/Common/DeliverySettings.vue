<template>
    <div class="container-fluid p-0">
        <form method="post" ref="form" :data-url="submit_url" @submit.prevent="save">
            <app-form-group
                :label="$fieldTitle('provider')"
                page="page"
                type="select"
                v-model="formData.provider"
                :list="providers"
                :error-message="$errorMessage(errors, 'provider')"
            />

            <component :is="componentNames[formData.provider]" v-model="formData" :errors="errors"/>

            <app-form-group
                :label="$fieldLabel('from', 'name')"
                page="page"
                v-model="formData.from_name"
                :placeholder="$placeholder('from', 'name')"
                :error-message="$errorMessage(errors, 'from_name')"
            />

            <app-form-group
                :label="$fieldLabel('from', 'email')"
                page="page"
                v-model="formData.from_email"
                :placeholder="$placeholder('from', 'email')"
                :error-message="$errorMessage(errors, 'from_email')"
            />

            <div class="form-group mt-5 mb-0">
                <app-submit-button @click="submitData" :loading="loading"/>
                <app-button
                    v-if="testMailButton"
                    :isDisabled="false"
                    :label="$t('test_email')"
                    class="btn-secondary ml-2"
                    @submit="testMailModal = true"
                />
            </div>
        </form>

        <app-test-mail-modal
        v-if="testMailModal"
        v-model="testMailModal"
        />
        <app-confirmation-modal
            :title="$t('wait_a_minute')"
            :message="$t('popup_subtitle_for_redirect_notification_template')"
            modal-class="primary"
            :first-button-name="$t('okay')"
            :second-button-name="$t('no')"
            icon="trello"
            v-if="promptIsActive"
            modal-id="app-confirmation-modal"
            @confirmed="triggerConfirm"
            @cancelled="promptIsActive = false"
        />

    </div>
</template>

<script>
import {FormMixin} from "../../../../core/mixins/form/FormMixin";
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";
import {axiosGet, urlGenerator} from '../../../Helper/AxiosHelper'
import {MAIL_SETTINGS_LIST, TENANT_NOTIFICATION_SETTINGS_FRONT_END} from "../../../Config/apiUrl";

export default {
    name: "DeliverySettings",
    components: {},
    mixins: [FormMixin, FormHelperMixins],
    data() {
        return {
            alias: false,
            formData: {
                provider: '',
            },
            submit_url: 'admin/app/settings/delivery-settings',
            componentNames: {
                amazon_ses: 'app-ses',
                mailgun: 'app-mailgun',
                mandrill: 'app-mandrill',
                postmark: 'app-postmark',
                smtp: 'app-smtp',
                sparkpost: 'app-sparkpost',
                sendmail: 'app-sendmail'
            },
            promptIsActive: false,
            promptActivable: false,
            testMailButton: false,
            testMailModal: false,
        }
    },
    props: {
        mailContext: {
            default: function () {
                return null;
            }
        },
        props: {
            default: function () {
                return {
                    alias: 'app'
                }
            }
        }
    },
    computed: {
        providers() {
            return [{
                id: '',
                disabled: true,
                value: this.$t('Choose a provider')
            }, ...this.$store.getters.getFormattedConfig('mail_context')]
        },
    },
    methods: {
        submitData() {
            this.loading = true;
            this.message = '';
            this.errors = {};
            this.save(this.formData);
        },

        afterSuccess({data}) {
            if (this.promptActivable) {
                this.promptIsActive = true;
            }
            this.testMailButton = true;
            this.scrollToTop();
            this.toastAndReload(data.message);
        },

        getMailSettings(provider = null) {
            provider = provider ? `/${provider}` : '';
            const url = `${MAIL_SETTINGS_LIST}${provider}`;
            axiosGet(url).then(({data}) => {

                if (!Object.keys(data).length) {
                    this.promptActivable = true
                }else {
                    this.testMailButton = true
                }

                if (data.provider) {
                    this.formData = Object.keys(data).length ? data : {provider: 'smtp'};
                }else {
                    this.formData = {provider: this.formData.provider}
                }
            });
        },

        triggerConfirm() {
            window.location = urlGenerator(TENANT_NOTIFICATION_SETTINGS_FRONT_END);
        }
    },
    created() {
        this.$store.dispatch('getConfig');
    },
    watch: {
        'formData.provider': {
            handler: function (provider) {
                this.getMailSettings(provider);
            },
            immediate: true
        },
    }
}
</script>

<style scoped>

</style>
