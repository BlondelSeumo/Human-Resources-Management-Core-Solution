<template>
    <modal id="employment-status-modal"
           v-model="showModal"
           :title="selectedUrl ?  $editLabel('employment_status') :  $addLabel('employment_status')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader"
    >
        <form
            :data-url='this.selectedUrl ? `${apiUrl.EMPLOYMENT_STATUSES}/${this.formData.id}` : apiUrl.EMPLOYMENT_STATUSES'
            ref="form"
            @submit="submitData"
        >
            <app-form-group
                :label="$t('name')"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :error-message="$errorMessage(errors, 'name')"
            />

            <app-form-group
                type="select"
                :label="$t('color_value')"
                v-model="formData.class"
                :placeholder="$placeholder('color_value', '')"
                :error-message="$errorMessage(errors, 'class')"
                :list="classes"
            >
                <template #suggestion v-if="formData.class && formData.name">
                    <div class="note note-warning p-2 mt-3">
                        <span :class="`badge badge-pill badge-${formData.class}`">{{ formData.name }}</span>
                        {{ $t('this_will_be_the_badge_of_the_employee') }}
                    </div>
                </template>
            </app-form-group>

            <app-form-group
                type="textarea"
                :label="$t('description')"
                v-model="formData.description"
                :placeholder="$textAreaPlaceHolder('description')"
                :error-message="$errorMessage(errors, 'description')"
                :list="classes"
            />

        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {addSelectInSelectArray} from "../../../../../common/Helper/Support/FormHelper";

export default {
    name: "EmploymentStatusCreateEdit",
    mixins: [FormHelperMixins, ModalMixin],
    data() {
        return {
            something: {}
        }
    },
    methods: {
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'tenant-employment-status');
            this.formData = {};
            $('#employment-status-modal').modal('hide');
        },
        afterSuccessFromGetEditData(response) {
            this.preloader = false;
            this.formData = response.data;
        },
    },
    computed: {
        classes() {
            return addSelectInSelectArray([
                {id: 'purple', value: this.$t('purple')},
                {id: 'success', value: this.$t('success')},
                {id: 'info', value: this.$t('info')},
                {id: 'warning', value: this.$t('warning')},
                {id: 'primary', value: this.$t('primary')},
                {id: 'danger', value: this.$t('danger')},
            ], 'value', this.$t('color_value'))
        }
    }
}
</script>

<style scoped>

</style>
