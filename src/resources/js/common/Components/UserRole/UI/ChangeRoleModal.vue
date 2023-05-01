<template>
    <modal
        id="app-change-role-modal"
        v-model="showModal"
        :title="$fieldTitle('manage', 'role', true)"
        @submit="submitData"
        :loading="loading"
        :preloader="preloader"
        :scrollable="false"
    >
        <form ref="form"
              :data-url='`${getSubmitURL}/${user}`'
              @submit.prevent="submitData"
        >
            <div class="form-group">
                <app-input
                    type="multi-select"
                    v-model="formData.roles"
                    :list="roleList"
                    listValueField="name"
                    :isAnimatedDropdown="true"
                />
                <app-message type="error" :message="$errorMessage(errors, 'roles')"/>
            </div>
            <app-note v-if="hasDeptManagerRole"
                      :title="$t('note')"
                      :notes="[$t('department_manager_role_note', {
                      edit: `<a href='${urlGenerator(TENANT_DEPARTMENTS_URL)}'>${$t('edit')}</a>`
                      })]"
                      content-type="html"
            />
        </form>
    </modal>
</template>

<script>
import ModalMixin from "../../../Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";
import {TENANT_ATTACH_ROLES, TENANT_USERS} from "../../../Config/apiUrl";
import {TENANT_DEPARTMENTS_URL} from "../../../Config/apiUrl";

export default {
    name: "ChangeRoleModal",
    mixins: [ModalMixin, FormHelperMixins],
    props: ['roleList', 'user', 'alias'],
    data() {
        return {
            formData: {
                roles: []
            },
            userRoles: [],
            TENANT_DEPARTMENTS_URL,
        }
    },
    methods: {
        afterSuccess({data}) {
            this.formData = {}
            $('#app-change-role-modal').modal('hide');
            this.$emit('input', false)
            this.toastAndReload(data.message, 'user-table')
            this.$hub.$emit('reload-role-table')
        },

        afterSuccessFromGetEditData({data}) {
            this.formData.roles = this.collection(data.roles).pluck();
            this.userRoles = data.roles;
            this.preloader = false;
        },
    },
    computed: {
        getSubmitURL() {
            return {
                tenant: TENANT_ATTACH_ROLES,
                app: `admin/auth/users/attach-roles`,
            }[this.alias];
        },
        getBaseURL() {
            return {
                tenant: TENANT_USERS,
                app: `admin/auth/users`
            }[this.alias];
        },
        hasDeptManagerRole() {
            return this.userRoles.find((role) => role.alias === 'department_manager')
        }
    },
    mounted() {
        if (this.user) {
            this.getEditData(`${this.getBaseURL}/${this.user}`);
        }
    }
}
</script>
