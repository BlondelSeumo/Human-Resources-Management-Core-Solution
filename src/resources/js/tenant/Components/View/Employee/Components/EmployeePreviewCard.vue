<template>
    <div class="card card-with-shadow border-0 h-100 employee-preview-card">
        <div class="card-body position-relative d-flex flex-column justify-content-between">
            <div v-if="showAction" class="dropdown options-dropdown position-absolute">
                <button type="button"
                        class="btn-option btn d-flex align-items-center justify-content-center"
                        data-toggle="dropdown">
                    <app-icon name="more-horizontal"/>
                </button>
                <div class="dropdown-menu dropdown-menu-right py-2 mt-1">
                    <a class="dropdown-item px-4 py-2"
                       href="#"
                       v-for="(action,index) in actions"
                       :key="index"
                       v-if="checkIfVisible(action, employee)"
                       @click.prevent="callAction(action)">
                        {{ action.title }}
                    </a>
                </div>
            </div>
            <div class="text-center">
                <app-avatar :title="employee.full_name"
                            :status="employee.status.class"
                            avatar-class="avatars-w-50 mb-2 mx-auto d-inline-block"
                            :img="getImageUrl(employee)"/>

                <p class="mb-2">{{ employee.full_name ? employee.full_name : employee.email }}</p>
                <p class="mb-2 font-size-80 text-muted"
                   data-toggle="tooltip"
                   data-placement="top"
                   :title="$t('designation')"
                >
                    {{ $optional(employee, 'designation', 'name') }}
                </p>
                <p class="mb-0 font-size-80 text-muted"
                   data-toggle="tooltip"
                   data-placement="top"
                   :title="$t('employee_id')"
                >
                    {{ $optional(employee, 'profile', 'employee_id') }}
                </p>

                <div v-if="employee.employment_status" class="my-3">
                    <app-employee-status
                        :row-data="employee"
                        :alignment-class="'justify-content-center'"
                    />
                </div>

                <div class="text-muted">
                    <p class="mb-2"
                       data-toggle="tooltip"
                       data-placement="top"
                       :title="$t('department')"
                    >
                        {{ $optional(employee, 'department', 'name') }}
                    </p>
                    <p class="mb-0"
                       data-toggle="tooltip"
                       data-placement="top"
                       :title="$t('working_shift')"
                    >
                        {{ $optional(employee, 'working_shift', 'name') }}
                    </p>
                </div>
            </div>
            <div class="text-center mt-4">
                <a :href="profileUrl"
                   class="btn btn-transparent-primary rounded-pill">
                    {{ $t('view_details') }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import CoreLibrary from "../../../../../core/helpers/CoreLibrary";
import {EMPLOYEES_PROFILE} from "../../../../Config/ApiUrl";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "EmployeePreviewCard",
    extends: CoreLibrary,
    props: {
        employee: {
            type: Object,
            required: true
        },
        actions: {
            type: Array,
            require: true
        },
        showAction: {
            type: Boolean,
            default: true
        },
        id: {
            type: String,
            require: true
        }
    },
    methods: {
        callAction(action) {
            this.$emit('action-' + this.id, this.employee, action, true)
        },
        checkIfVisible(action, employee) {
            if (this.isFunction(action.modifier)) {
                return action.modifier(employee);
            }
            return true;
        },
        getImageUrl(employee) {
            if (employee.profile_picture) {
                return urlGenerator(employee.profile_picture.path);
            }
        }
    },
    computed: {
        profileUrl() {
            return urlGenerator(`${EMPLOYEES_PROFILE}/${this.employee.id}/profile`)
        }
    }
}
</script>