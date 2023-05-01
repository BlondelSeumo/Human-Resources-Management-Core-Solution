<template>
    <modal id="user-modal"
           v-model="showModal"
           :title="generateModalTitle('user')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
    >
        <form ref="form" :data-url='selectedUrl || getBaseURL'>
            <app-form-group
                :label="$fieldTitle('first', 'name')"
                :placeholder="$placeholder('first', 'name')"
                v-model="formData.first_name"
                :required="true"
                :error-message="$errorMessage(errors, 'first_name')"
            />

            <app-form-group
                :label="$fieldTitle('last', 'name')"
                :placeholder="$placeholder('last', 'name')"
                v-model="formData.last_name"
                :required="true"
                :error-message="$errorMessage(errors, 'last_name')"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../Mixin/Global/ModalMixin";
import {TENANT_USERS} from "../../../Config/apiUrl";

export default {
    name: "UserModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: ['alias'],
    methods: {
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'user-table')
            $('#user-modal').modal('hide');
        },

        afterSuccessFromGetEditData(response) {
            this.formData = response.data;
            this.preloader = false;
        }
    },
    computed: {
        getBaseURL() {
            return {
                tenant: TENANT_USERS,
                app: `admin/auth/users`
            }[this.alias];
        }
    }
}
</script>

