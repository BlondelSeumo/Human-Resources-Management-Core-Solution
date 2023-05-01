<template>
    <div class="login-form d-flex align-items-center">
        <form class="sign-in-sign-up-form w-100" ref="form" data-url="users/password-reset">
            <div class="text-center mb-4">
                <img :src="logoUrl" alt="" class="img-fluid logo">
            </div>
            <div class="form-row">
                <div class="form-group col-12">
                    <h6 class="text-center mb-0">{{ $t('reset_password') }}</h6>
                </div>
            </div>
            <template v-if="!message">
                <div class="form-row">
                    <div class="form-group col-12">
                        <label>{{ $t('email') }}</label>
                        <app-input
                            type="email"
                            :placeholder="$placeholder('your', 'email')"
                            :required="true"
                            v-model="formData.email"
                            :error-message="$errorMessage(errors, 'email')"
                        />
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group col-12">
                        <app-submit-button
                            btn-class="btn-primary d-inline-flex btn-block text-center"
                            :label="$t('request')"
                            :loading="loading"
                            @click="submitData"
                        />
                    </div>
                </div>
            </template>
            <template v-else>
                <div class="form-group text-justify">
                    {{ message }}
                </div>
            </template>
            <div class="form-row">
                <div class="col-12">
                    <a :href="urlGenerator('/')" :class="`${message ? 'btn btn-primary btn-block' :'bluish-text'}`">
                        <app-icon
                            name="arrow-left"
                            v-if="!message"
                        />
                        &nbsp;{{ message ? $t('thank_you') : $t('back_to', {destination: $t('login')}) }}
                    </a>
                </div>
            </div>
            <div class="form-group">
                <div class="col-12">
                    <p class="text-center mt-5 footer-copy">
                        {{ $t('copyright') }} @ {{ new Date().getFullYear() }} {{ $t('by') }}
                        {{ appName }}
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
    mixins: [ThemeMixin, FormHelperMixins],
    name: "PasswordReset",
    props: {
        logoUrl: {
            required: false
        },
        appName: {
            required: false
        }
    },
    methods: {}
}
</script>
