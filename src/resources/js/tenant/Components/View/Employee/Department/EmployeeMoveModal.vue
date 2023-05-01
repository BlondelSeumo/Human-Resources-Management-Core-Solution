<template>
    <modal id="employee-move-modal"
           v-model="showModal"
           :title="$t('move_employee')"
           @submit="submitData"
           :scrollable="false"
           :btn-label="$t('move')"
           :loading="loading"
           :preloader="preloader">
        <form v-if="employees.length"
              method="POST"
              ref="form"
              :data-url='`${apiUrl.DEPARTMENTS}/move-employees`'>

            <div class="form-group">
                <label class="d-flex align-items-start">
                    {{ departmentName }}
                    <app-icon name="corner-right-down"
                              class="ml-2 mt-2 primary-text-color"
                              width="20"
                              height="20"/>
                </label>
                <app-input type="select"
                           :label="departmentName"
                           :list="departments"
                           list-value-field="name"
                           v-model="formData.department_id"
                           :required="true"
                           :error-message="$errorMessage(errors, 'department_id')"/>
            </div>

            <app-form-group
                type="multi-select"
                :label="$t('select_employee')"
                :list="employees"
                list-value-field="full_name"
                :isAnimatedDropdown="true"
                v-model="formData.users"
                :error-message="$errorMessage(errors, 'users')"
            />

            <app-input
                type="single-checkbox"
                :list-value-field="$t('choose_all_employee')"
                @input="chooseAllEmployee($event)"
                v-model="formData.is_all_employee"
            />
        </form>

        <app-note v-else
                  :title="$t('no_employees_found')"
                  :notes="[$t('no_employees_warning')]"/>

        <template v-if="employees.length == 0" slot="footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                {{ $t('close') }}
            </button>
        </template>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {addChooseInSelectArray} from "../../../../../common/Helper/Support/FormHelper";

export default {
    name: "EmployeeMoveModal",
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        departmentId: {},
        departmentName: {}
    },
    data() {
        return {
            departments: [],
            employees: [],
            formData: {
                department_id: '',
                users: []
            }
        }
    },
    mounted() {
        this.getAvailableDepartments();
        this.getDepartmentEmployees();
    },
    methods: {
        chooseAllEmployee(event) {
            this.formData.users = event ? this.collection(this.employees).pluck() : [];
        },
        getDepartmentEmployees() {
            this.preloader = true;

            axiosGet(`${this.apiUrl.DEPARTMENTS}/${this.departmentId}/employees`)
                .then(({data}) => {
                    this.employees = data;
                })
                .finally(() => {
                    this.preloader = false;
                });
        },
        getAvailableDepartments() {
            axiosGet(`${this.apiUrl.SELECTABLE_DEPARTMENT}?exclude=${this.departmentId}`).then(response => {
                this.departments = response.data
                this.departments = addChooseInSelectArray(this.departments, 'name', this.$t('department'));
            })
        },
        afterSuccess({data}) {
            this.loading = false;
            this.$emit('input', false);
            $("#employee-move-modal").modal('hide');
            this.toastAndReload(data.message, 'department-table');
        }
    }
}
</script>
