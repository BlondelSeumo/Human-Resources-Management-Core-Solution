<template>
    <div class="login-form d-flex align-items-center">
        <form class="sign-in-sign-up-form w-100" ref="form" data-url="users/reset-password">
            <div class="text-center mb-4">
                <img :src="logoUrl" alt="" class="img-fluid logo">
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <h6 class="text-center mb-0">{{ $t('reset_password') }}</h6>
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
                        :placeholder="$placeholder('confirm', 'password')"
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
                        :label="$t('change')"
                        :loading="loading"
                        @click="submitData"
                    />
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <p class="text-center mt-5 footer-copy">
                        {{ $t('copyright') }} @ {{ new Date().getFullYear() }} {{ $t('by') }} {{ appName }}
                    </p>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
import ThemeMixin from "../../../../core/mixins/global/ThemeMixin";
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";

export default {
    name: "ResetPassword",
    props: {
        logoUrl: {
            required: false
        },
        appName: {
            required: false
        },
        user: {
            required: true,
            type: String
        },
        token: {
            required: true,
            type: String
        }
    },
    mixins: [ThemeMixin, FormHelperMixins],
    methods: {
        afterSuccess(response) {
            this.message = response.data.message;
            window.location = response.data.redirect
        },
        afterFinalResponse() {
        },
    },
    watch: {
        user: {
            handler: function (user) {
                user = JSON.parse(user);
                this.formData.email = user.email;
                this.formData.token = this.token;
            },
            immediate: true
        }
    }
}
</script>
