<template>
    <modal id="department-modal"
           v-model="showModal"
           :title="generateModalTitle('department')"
           @submit="submitData"
           :loading="loading"
           :preloader="preloader">
        <form :data-url='selectedUrl ? selectedUrl : apiUrl.DEPARTMENTS'
              method="POST"
              ref="form">

            <app-form-group
                :label="$t('name')"
                type="text"
                v-model="formData.name"
                :placeholder="$placeholder('name')"
                :required="true"
                :error-message="$errorMessage(errors, 'name')"
            />

            <app-form-group-selectable
                type="search-select"
                :label="$t('manager')"
                list-value-field="full_name"
                :chooseLabel="$t('manager')"
                v-model="formData.manager_id"
                :required="true"
                :error-message="$errorMessage(errors, 'manager_id')"
                :fetch-url="`${apiUrl.TENANT_SELECTABLE_DEPARTMENT_USERS}?without=admin&employee=only&with_auth=yes`"
            />

            <app-form-group-selectable
                type="select"
                v-if="parentDeptEditPermission"
                :label="$t('parent_department')"
                list-value-field="name"
                v-model="formData.department_id"
                :chooseLabel="$t('parent_department')"
                :fetch-url="apiUrl.TENANT_SELECTABLE_DEPARTMENT_DEPARTMENTS"
                :error-message="$errorMessage(errors, 'department_id')"
            />

            <app-form-group
                :label="$t('location')"
                type="text"
                v-model="formData.location"
                :placeholder="$placeholder('location')"
            />

            <app-form-group-selectable
                type="search-select"
                :label="$t('work_shift')"
                list-value-field="name"
                :chooseLabel="$t('work_shift')"
                v-model="formData.working_shift_id"
                :required="true"
                :error-message="$errorMessage(errors, 'working_shift_id')"
                :fetch-url="`${TENANT_SELECTABLE_WORK_SHIFT}`"
            />
            <upcoming-working-shift-view
                v-if="this.formData.upcoming_working_shift.length"
                :working-shifts="this.formData.upcoming_working_shift"
                @workingShiftRemoved="getEditData(selectedUrl)"
            />

            <app-form-group
                :label="$t('description')"
                type="textarea"
                v-model="formData.description"
                :placeholder="$textAreaPlaceHolder('description')"
            />
        </form>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {TENANT_SELECTABLE_WORK_SHIFT} from "../../../../../common/Config/apiUrl";
import UpcomingWorkingShiftView from "./UpcomingWorkingShiftView";

export default {
    name: "DepartmentCreateEdit",
    components: {UpcomingWorkingShiftView},
    mixins: [FormHelperMixins, ModalMixin],
    comments: {UpcomingWorkingShiftView},
    data() {
        return {
            TENANT_SELECTABLE_WORK_SHIFT,
            formData: {
                name: '',
                manager_id: '',
                department_id: '',
                upcoming_working_shift: []
            },
        }
    },
    computed: {
        parentDeptEditPermission() {
            return this.selectedUrl ? this.formData.department_id : true;
        }
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {};
            $('#department-modal').modal('hide');
            this.toastAndReload(data.message, 'department-table')
            this.$emit('input', false);
        },
        afterSuccessFromGetEditData({data}) {
            this.preloader = false
            this.formData = data
            if (this.formData.department_id === null) {
                this.formData.department_id = ''
            }
            if (this.formData.manager_id === null) {
                this.formData.manager_id = ''
            }
            this.formData.working_shift_id = data.working_shift?.id
        }
    }
}
</script>
