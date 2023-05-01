<template>
    <div class="login-form d-flex align-items-center custom-scrollbar h-100">
        <form class="sign-in-sign-up-form w-100" ref="form" data-url="users/confirm">
            <div class="text-center mb-4">
                <img :src="logoUrl" alt="" class="img-fluid logo">
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <h6 class="text-center mb-0">{{ $t('hi', {object: $t('there')}) }}!</h6>
                    <label class="text-center d-block">
                        {{ $t('confirm_your_account') }}
                    </label>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{{ $t('first_name') }}</label>
                    <app-input
                        type="text"
                        v-model="formData.first_name"
                        :placeholder="$placeholder('your', 'first_name')"
                        :error-message="$errorMessage(errors, 'first_name')"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{{ $t('last_name') }}</label>
                    <app-input
                        type="text"
                        v-model="formData.last_name"
                        :placeholder="$placeholder('your', 'last_name')"
                        :error-message="$errorMessage(errors, 'last_name')"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{{ $t('email') }}</label>
                    <app-input
                        type="email"
                        :read-only="true"
                        v-model="formData.email"
                        :error-message="$errorMessage(errors, 'email')"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{{ $t('password') }}</label>
                    <app-input
                        type="password"
                        :placeholder="$placeholder('your', 'password')"
                        v-model="formData.password"
                        :error-message="$errorMessage(errors, 'password')"
                        :show-password="true"
                    />
                    <div class="note note-warning p-4 mt-2">
                        <p class="m-1" v-html="$t('password_requirements_message')"></p>
                    </div>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <label>{{ $fieldTitle('confirm', 'password') }}</label>
                    <app-input
                        type="password"
                        :placeholder="$placeholder('your', 'password')"
                        v-model="formData.password_confirmation"
                        :error-message="$errorMessage(errors, 'password_confirmation')"
                        :show-password="true"
                    />
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <app-submit-button
                        btn-class="btn-primary d-inline-flex btn-block text-center"
                        :label="$t('confirm')"
                        :loading="loading"
                        @click="submitData"
                    />
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import ThemeMixin from "../../Mixin/Global/ThemeMixin";
import FormHelperMixins from "../../Mixin/Global/FormHelperMixins";
import Note from "../../../core/components/badge/Note";
import {urlGenerator} from "../../Helper/AxiosHelper";

export default {
    name: "UserInvitationConfirm",
    components: {Note},
    mixins: [ThemeMixin, FormHelperMixins],
    props: {
        user: {},
        logoUrl: {
            required: false
        },
        companyName: {
            required: false
        }
    },
    data() {
        return {
            confirm: {},
            logo_url: window.logo_url
        }
    },
    methods: {
        afterSuccess(response) {
            this.$toastr.s(
                '',
                `${response.data.message}${this.$t('dot')} ${this.$t('login')} ${this.$t('to')} ${this.$t('continue').toLocaleLowerCase()}`
            );

            window.location = urlGenerator('/admin/users/login');
        },
        afterFinalResponse() {
        },
    },
    watch: {
        user: {
            handler: function (user) {
                this.formData = {...user};
            },
            immediate: true,
            deep: true
        }
    }
}
</script>

<style scoped>

</style>
