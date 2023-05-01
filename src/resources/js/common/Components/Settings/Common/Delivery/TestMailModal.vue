<template>
    <modal id="test-mail-modal"
           v-model="showModal"
           :title="$t('send_test_email')"
           @submit="submitData"
           :loading="loading"
           :btnLabel="$t('send')"
           :preloader="preloader">

        <form
            ref="form"
            :data-url="apiUrl.TEST_MAIL"
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('email_address')"
                :placeholder="$placeholder('email_address')"
                v-model="formData.email"
                :required="true"
                :error-message="$errorMessage(errors, 'email')">
            </app-form-group>

            <app-form-group
                :label="$t('subject')"
                :placeholder="$placeholder('subject')"
                v-model="formData.subject"
                :required="true"
                :error-message="$errorMessage(errors, 'subject')">
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('message')"
                :placeholder="$textAreaPlaceHolder('message')"
                v-model="formData.message"
                :required="true"
                :error-message="$errorMessage(errors, 'message')">
            </app-form-group>

        </form>
    </modal>
</template>

<script>


import FormHelperMixins from "../../../../Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../Mixin/Global/ModalMixin";

export default {
    name: "TestMailModal",
    mixins: [FormHelperMixins, ModalMixin],
    data() {
        return {
            formData: {},
        }
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {};
            $('#test-mail-modal').modal('hide');
            this.$emit('input', false);
            this.$toastr.s(data.message);
        },
    },
}
</script>

