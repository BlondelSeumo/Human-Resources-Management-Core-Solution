<template>
    <div class="content-wrapper">
        <app-page-top-section :title="$fieldTitle('users_roles')" icon="user-check">
            <app-default-button btn-class="btn btn-success mr-2"
                                :title="$fieldTitle('add', 'user', true)"
                                @click="isEmployeeCreateOpenModalActive = true"
                                v-if="$can('add_employees')"
            />
            <app-default-button btn-class="btn btn-success mr-2"
                                :title="$fieldTitle('invite', 'user', true)"
                                @click="isInviterOpenModalActive = true"
                                v-if="$can('invite_user')"
            />
<!--            <app-default-button-->
<!--                :title="$addLabel('role')"-->
<!--                @click="isRoleModalActive = true"-->
<!--                v-if="$can('create_roles')"-->
<!--            />-->
        </app-page-top-section>

        <!--Users And Roles Pages Started Here....-->
        <div class="row">
            <div class="col-12 col-sm-12 col-md-12 col-lg-5 col-xl-5">
                <app-users
                    :alias="alias"
                    :roleList="roleList"
                />
            </div>

            <div v-if="$can('view_roles')" class="col-12 col-sm-12 col-md-12 col-lg-7 col-xl-7">
                <app-roles
                    :alias="alias"
                    @selectedUrl="openEditModal"
                    v-model="isRoleModalActive"
                />
            </div>
        </div>
        <div v-if="alias === 'tenant'">
            <app-employee-invite
                from="user"
                v-if="isInviterOpenModalActive "
                v-model="isInviterOpenModalActive"
                :selected-url="''"
            />
            <app-employee-create
                from="user"
                v-if="isEmployeeCreateOpenModalActive "
                v-model="isEmployeeCreateOpenModalActive"
                :selected-url="''"
            />
        </div>
        <div v-else>
            <app-user-invite-modal
                :alias="alias"
                v-if="isInviterOpenModalActive"
                v-model="isInviterOpenModalActive"
                :roles="roleList"
            />
        </div>

    </div>
</template>

<script>
import {axiosGet} from "../../Helper/AxiosHelper";
import {ROLES, TENANT_SELECTABLE_ROLES} from "../../Config/apiUrl";
export default {
    props: {
        alias: {
            require: true,
            default: 'app'
        }
    },
    components: {
        'app-users': require('./Users').default,
        'app-roles': require('./Roles').default,
        'app-employee-create': require('../../../tenant/Components/View/Employee/EmployeeCreateEditModal').default,
    },
    data() {
        return {
            isInviterOpenModalActive: false,
            isEmployeeCreateOpenModalActive: false,
            isRoleModalActive: false,
            selectedUrl: '',
            roleList: []
        }
    },
    methods: {
        openEditModal(url) {
            this.selectedUrl = url;
            this.isRoleModalActive = true
        },
        getAllRoles() {
            axiosGet(this.getRolesURL)
                .then(({data}) => {
                    this.roleList = data.filter(role => role.alias !== 'department_manager')
                })
        }
    },
    computed: {
        getRolesURL() {
            return {
                tenant: TENANT_SELECTABLE_ROLES,
                app: ROLES,
            }[this.alias]
        }
    },
    mounted() {
        if (this.$can('view_roles')) {
            this.getAllRoles();
            this.$hub.$on('rolesAffected', () => this.getAllRoles())
        }
    }
}
</script>