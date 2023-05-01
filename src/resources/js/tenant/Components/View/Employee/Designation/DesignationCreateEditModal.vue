<template>
    <modal id="designation-modal"
           v-model="showModal"
           :title="generateModalTitle('designation')"
           @submit="submitData" :loading="loading"
           :preloader="preloader">

        <form
            ref="form"
            :data-url='selectedUrl ? selectedUrl : DESIGNATION'
            @submit.prevent="submitData"
        >
            <app-form-group
                :label="$t('name')"
                :placeholder="$placeholder('name')"
                v-model="formData.name"
                :required="true"
                :error-message="$errorMessage(errors, 'name')">
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('description')"
                :placeholder="$textAreaPlaceHolder('description')"
                v-model="formData.description"
                :required="true"
                :error-message="$errorMessage(errors, 'description')">
            </app-form-group>

        </form>
    </modal>
</template>

<script>
    import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
    import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
    import {DESIGNATION, SELECTABLE_DEPARTMENT} from "../../../../Config/ApiUrl";

    export default {
        name: "DesignationCreateEditModal",
        mixins: [FormHelperMixins, ModalMixin],
        data() {
            return {
                formData: {},
                DESIGNATION,
                SELECTABLE_DEPARTMENT
            }
        },
        methods: {
            afterSuccess({data}) {
                this.formData = {};
                $('#designation-modal').modal('hide');
                this.$emit('input', false);
                this.toastAndReload(data.message, 'designation-table');
            },
            afterSuccessFromGetEditData(response) {
                this.preloader = false;
                this.formData = response.data;
            },
        },
    }
</script>

