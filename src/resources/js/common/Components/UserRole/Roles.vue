<template>
    <div class="card card-with-shadow border-0 h-100">
        <div class="card-header d-flex align-items-center p-primary bg-transparent">
            <h5 class="card-title d-inline-block mb-0">{{ $t('roles') }}</h5>
            <app-search @input="getSearchValue($event)"/>
        </div>
        <div class="card-body p-0">
            <app-table
                id="role-table"
                :options="options"
                @action="triggerActions"
                :search="search"
            />
        </div>

        <app-roles-modal
            :alias="alias"
            v-if="isModalActive"
            v-model="isModalActive"
            :selected-url="selectedUrl"
            :data="{permissions: permissions, types: types}"
            @fetchPermission="getAllPermission"
            :manage="manage"
        />

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed('role-table')"
            @cancelled="cancelled"
        />

        <app-manage-roles
            :alias="alias"
            v-if="showManageRoleModal"
            v-model="showManageRoleModal"
            :role="role"
        />
    </div>
</template>

<script>
import RolesMixin from "../../Mixin/RolesMixin";
import {axiosGet} from "../../Helper/AxiosHelper";
import {APP_ROLES, PERMISSIONS, TENANT_PERMISSIONS, TENANT_ROLES} from "../../Config/apiUrl";

export default {
    name: "Roles",
    mixins: [RolesMixin],
    props: ['value', 'alias'],
    components: {
        'app-manage-roles': require('./UI/ManageRolesModal').default
    },
    data() {
        return {
            isModalActive: false,
            selectedUrl: '',
            permissions: [],
            types: [],
            manage: false,
            search: '',
            showManageRoleModal: false,
            role: {}
        }
    },
    methods: {
        getAllPermission(type = null) {
            this.permissions = {};
            axiosGet(this.getPermissionsURL).then(response => {
                this.permissions = response.data;
            });
        },
        getAllTypes() {
            axiosGet(`admin/app/types`).then(response => {
                this.types = response.data
            });
        },
        triggerActions(row, action, active) {
            const url = `${this.getBaseURL}/${row.id}`;
            this.selectedUrl = url;
            this.manage = false;
            if (action.name === 'edit') {
                this.isModalActive = true;
            } else if (action.name === 'delete') {
                this.delete_url = "/" + url
                this.confirmationModalActive = true;
            } else if (action.actionType === 'manage') {
                this.manage = true;
                this.isModalActive = true;
            } else if (action.name === 'manage-roles') {
                this.showManageRoleModal = true
                this.role = row;
            }
        },
        getSearchValue(event) {
            this.search = event;
            setTimeout(() => {
                this.$hub.$emit('reload-role-table');
            });
        },
    },
    computed: {
        getBaseURL() {
            return {
                tenant: TENANT_ROLES,
                app: APP_ROLES,
            }[this.alias];
        },
        getPermissionsURL() {
            return {
                tenant: TENANT_PERMISSIONS,
                app: PERMISSIONS,
            }[this.alias];
        },
    },
    mounted() {
        this.getAllPermission();
        this.getAllTypes();
    },
    watch: {
        value: function (value) {
            this.isModalActive = value;
            this.manage = false;
            this.selectedUrl = '';
        },
        isModalActive: function (isModalActive) {
            if (!isModalActive)
                this.$emit('input', isModalActive)
        }
    }

}
</script>
