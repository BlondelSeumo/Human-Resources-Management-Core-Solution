<template>
    <div>
        <div class="bulk-floating-action-wrapper">
            <div class="actions" :class="{'loading-opacity': loading}">

                <app-overlay-loader v-if="loading"/>

                <template v-else>
                    <app-context-button
                        icon="activity"
                        :title="this.$t('actions')">
                        <a href="#" class="dropdown-item" @click="openActionModal('joining_date')">
                            {{ this.$t('add_joining_date') }}
                        </a>
                        <a v-if="notTerminated" href="#" class="dropdown-item" @click="openActionModal('terminate')">
                            {{ this.$t('terminate') }}
                        </a>
                        <a v-else href="#" class="dropdown-item" @click="openActionModal('rejoining')">
                            {{ this.$t('rejoining') }}
                        </a>
                        <a href="#" class="dropdown-item" @click="openActionModal('remove-employee')">
                            {{ this.$t('remove_from_employee_list') }}
                        </a>
                    </app-context-button>

                    <app-context-button
                        :class="{'loading-opacity': loading}"
                        :keep-open="true"
                        :title="this.$t('change_work_shift')"
                        dropdown-menu-class="dropdown-menu-with-search"
                        icon="clock">

                        <app-list-dropdown
                            v-model="formData.work_shift_id"
                            :url="apiUrl.SELECTABLE_WORKING_SHIFT"
                            :title="$t('change_work_shift')"
                            @filteredFlag="workshiftFlag = $event"
                        />
                        <template v-if="formData.work_shift_id && workshiftFlag">
                            <div class="dropdown-divider my-0"/>
                            <div class="p-3 text-right">
                                <app-submit-button
                                    :label="$t('apply')"
                                    :loading="loading"
                                    btn-class="btn btn-primary"
                                    @click="handleLists('workshift')"
                                />
                            </div>
                        </template>
                    </app-context-button>

                    <app-context-button
                        :class="{'loading-opacity': loading}"
                        :keep-open="true"
                        :title="this.$t('change_department')"
                        dropdown-menu-class="dropdown-menu-with-search"
                        icon="briefcase">

                        <app-list-dropdown
                            v-model="formData.department_id"
                            :url="apiUrl.SELECTABLE_DEPARTMENT"
                            :title="$t('change_department')"
                            @filteredFlag="departmentFlag = $event"
                        />
                        <template v-if="formData.department_id && departmentFlag">
                            <div class="dropdown-divider my-0"/>
                            <div class="p-3 text-right">
                                <app-submit-button
                                    :label="$t('apply')"
                                    :loading="loading"
                                    btn-class="btn btn-primary"
                                    @click="handleLists('department')"
                                />
                            </div>
                        </template>
                    </app-context-button>
                </template>

            </div>
        </div>

        <employee-context-action-modal
            v-if="contextModal"
            v-model="contextModal"
            :modal-type="contextType"
            :employees="employees"
        />

        <app-confirmation-modal
            :title="promptTitle"
            :message="promptMessage"
            :modal-class="modalClass"
            :icon="promptIcon"
            v-if="confirmationModelActive"
            modal-id="app-confirmation-modal"
            @confirmed="triggerConfirm"
            @cancelled="cancelled"
            :loading="loading"
            :self-close="false"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import EmployeeContextActionModal from "./Context/EmployeeContextActionModal";
import {axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "ContextMenu",
    components: {EmployeeContextActionModal},
    mixins: [FormHelperMixins],
    props: {
        employees: {}
    },
    data() {
        return {
            departmentFlag: true,
            workshiftFlag: true,
            confirmationModelActive: false,
            contextModal: false,
            contextType: '',
            loading: false,
            formData: {},
            promptIcon: '',
            promptTitle: '',
            promptMessage: '',
            modalClass: '',
        }
    },
    methods: {
        closeContextMenu() {
            this.$emit('close')
        },
        openActionModal(context) {
            this.contextModal = true;
            this.contextType = context;
        },
        handleLists(action) {
            this.contextType = action;
            this.confirmationModelActive = true;
            this.promptTitle = this.$t('are_you_sure');
            this.promptIcon = 'check-circle';
            this.modalClass = 'primary';
            action === 'department' ?
                this.promptMessage = this.$t('you_are_going_to_change_the_department_of_selected_employees') :
                this.promptMessage = this.$t('you_are_going_to_change_the_work_shift_of_selected_employees')
        },
        triggerConfirm() {
            this.loading = true;
            this.formData.users = this.employees.map(employee => employee.id)
            const url = this.contextType === 'department' ?
                `${this.apiUrl.DEPARTMENTS}/move-employees` :
                `${this.apiUrl.EMPLOYEES}/${this.contextType}/update`;
            axiosPost(
                url,
                this.formData
            ).then(({data}) => {
                this.loading = false;
                this.confirmationModelActive = false;
                this.toastAndReload(data.message, 'employee-table');
            }).catch(({response}) => {
                this.closeContextMenu();
                this.loading = false;
                this.$toastr.e('', response.data.message);
            }).finally(() => this.closeConfirmation());
        },
        cancelled() {
            this.confirmationModelActive = false;
        },
        closeConfirmation() {
            $("#app-confirmation-modal").modal('hide');
            $(".modal-backdrop").remove();
            this.loading = false;
            this.confirmationModalActive = false;
        },
    },
    computed: {
        notTerminated() {
            return this.employees.filter(employee => employee.employment_status.alias !== 'terminated').length;
        }
    }
}
</script>