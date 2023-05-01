<template>
    <modal id="announcement-create-edit-modal"
           size="large"
           v-model="showModal"
           :title="generateModalTitle('announcement')"
           @submit="submit"
           :cancel-btn-label="checkDisable ? $t('close') : $t('cancel')"
           :loading="loading"
           :preloader="preloader">

        <form :data-url='selectedUrl ? selectedUrl : apiUrl.ANNOUNCEMENTS'
              :class="{'disabled-section' : checkDisable}"
              method="POST"
              ref="form"
        >
            <app-form-group
                :label="$t('title')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name', '')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />
            <div class="row">
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('start_date')"
                        type="date"
                        v-model="formData.start_date"
                        :placeholder="$placeholder('start_date', '')"
                        :required="true"
                        :error-message="$errorMessage(errors, 'start_date')"
                    />
                </div>
                <div class="col-md-6">
                    <app-form-group
                        :label="$t('end_date')"
                        type="date"
                        v-model="formData.end_date"
                        :placeholder="$placeholder('end_date', '')"
                        :error-message="$errorMessage(errors, 'end_date')"
                    />
                </div>
            </div>

            <app-form-group-selectable
                :label="$fieldLabel('department', '')"
                :recommendation="$fieldTitle('you_can_set_the_announcement_only_for_specific_department_by_adding_them', '')"
                type="multi-select"
                v-model="formData.departments"
                list-value-field="name"
                :error-message="$errorMessage(errors, 'departments')"
                :fetch-url="apiUrl.SELECTABLE_DEPARTMENT"
            />

            <div class="form-group">
                <label>{{ $t('description') }}</label>
                <app-input
                    type="text-editor"
                    v-model="formData.description"
                    id="text-editor-for-description"
                    :placeholder="$textAreaPlaceHolder('description')"
                    row="4"
                    :error-message="$errorMessage(errors, 'description')"
                />
            </div>
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {errorMessageForArray} from '../../../../../common/Helper/Support/FormHelper'
import {formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "AnnouncementCreateEditModal",
    mixins: [FormHelperMixins, ModalMixin],
    data() {
        return {
            errorMessageForArray,
            formData: {
                name: '',
                start_date: new Date(),
                end_date: '',
                description: '',
                departments: [],
            },
        }
    },
    computed: {
        checkDisable() {
            return this.selectedUrl ? (!this.$can('update_announcements')) :
                !this.$can('create_announcements')
        },
    },
    methods: {
        submit() {
            let formData = {...this.formData};

            formData.start_date = formatDateForServer(this.formData.start_date);
            formData.end_date = formatDateForServer(this.formData.end_date);

            this.loading = true;
            this.message = '';
            this.errors = {};
            this.save(formData);
        },
        afterSuccess({data}) {
            this.formData = {};
            $('#announcement-create-edit-modal').modal('hide');
            this.$emit('input', false);
            this.toastAndReload(data.message, 'announcement-table');
        },
        afterSuccessFromGetEditData({data}) {
            this.preloader = false;
            this.formData = data;
            this.formData.departments = this.collection(data.departments).pluck();
            this.formData.start_date = new Date(data.start_date);
            this.formData.end_date = new Date(data.end_date);
            this.formData.description = data.description;
        },
    },
}
</script>