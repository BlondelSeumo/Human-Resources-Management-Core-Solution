<template>
    <modal id="role-modal"
           v-model="showModal"
           :title="['employee', 'department_manager'].includes(formData.alias) ? this.$t('permissions') : (this.manage ? $fieldTitle('manage', 'permissions', true) : generateModalTitle('role'))"
           @submit="submitData"
           :loading="loading"
           :hide-submit-button="['employee', 'department_manager'].includes(formData.alias)"
           :cancel-btn-label="['employee', 'department_manager'].includes(formData.alias) ? this.$t('close') : this.$t('cancel')"
           :hide-footer="(formData.is_admin && formData.is_default)"
           :preloader="preloader">

        <div v-if="formData.id !== 1 && !(formData.is_default && formData.is_admin)">
            <form ref="form"
                  :class="{'disabled-section' : ['employee', 'department_manager'].includes(formData.alias)}"
                  :data-url='selectedUrl ? `${getSubmitURL}/${this.formData.id}` : getSubmitURL'
                  @submit.prevent="submitData">
                <template v-if="!manage">
                    <app-form-group
                        v-model="formData.name"
                        :label="$t('name')"
                        :placeholder="$placeholder('name')"
                        :error-message="$errorMessage(errors, 'name')"
                    />
                    <div class="mb-primary" v-if="$optional(currentTenant, 'id') && formData.alias !== 'department_manager'">
                        <app-input
                            class="d-inline-block"
                            type="single-checkbox"
                            :list-value-field="$t('is_manager')"
                            v-model="formData.is_manager"
                        />
                        <small class="mt-2 font-italic">
                            ({{ $t('checked_to_get_access_for_all_user') }})
                        </small>
                    </div>
                </template>
                <div class="form-group" v-if="Object.keys(data.permissions).length">
                    <app-message type="error" :message="$errorMessage(errors, 'permissions')"/>

                    <div id="accordionExample" class="accordion">
                        <div class="card" v-for="(permission, index) in permissionsExceptAccessDepartment">
                            <div class="card-header border-0">
                                <div
                                    class="custom-checkbox-default d-block position-relative text-capitalize collapsible-link py-2 cursor-pointer"
                                    data-toggle="collapse"
                                    :data-target="`#${permission}`"
                                    aria-expanded="false"
                                    :aria-expanded="`${checkForVisibility(index, permission)? true : false}`"
                                    aria-controls="permission">
                                    <div class="customized-checkbox checkbox-default">
                                        <input type="checkbox"
                                               :name="`single-checkbox-${permission}`"
                                               :id="`single-checkbox-${permission}`"
                                               :value="permission"
                                               :checked="ifChecked(permission)"
                                               @input="checkGroup($event, permission)"
                                               ref="checkbox"
                                               v-if="loadChecked"
                                               @click="$event.stopPropagation()"/>
                                        <label class="mb-0"
                                               :for="`single-checkbox-${permission}`"
                                               @click="$event.stopPropagation()">
                                            {{ $t(permission) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div :id="permission"
                                 data-parent="#accordionExample"
                                 :class="`collapse ${checkForVisibility(index, permission)? 'show' : ''}`">
                                <div class="card-body p-primary">
                                    <app-input type="checkbox"
                                               v-if="loaded"
                                               :list="data.permissions[permission]"
                                               v-model="checkedPermissions[permission]"
                                               @input="checkPermissions(permission)"
                                               list-value-field="translated_name"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group" v-else>
                    <app-pre-loader/>
                </div>
            </form>
        </div>
        <div v-else>
            {{ $t('action_not_allowed') }}
        </div>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../Mixin/Global/ModalMixin";
import {APP_ROLES, TENANT_ROLES} from "../../../Config/apiUrl";

export default {
    name: "AppRolesModal",
    components: {},
    mixins: [FormHelperMixins, ModalMixin],
    props: {
        data: {
            default: function () {
                return {
                    permissions: {},
                    types: []
                }
            }
        },
        manage: {},
        alias: {}
    },
    data() {
        return {
            permissions: [],
            checkedPermissions: {},
            brands: [],
            loaded: true,
            loadChecked: true
        }
    },
    mounted() {
        this.setFormData()
    },
    created() {
      this.permissionsExceptAccessDepartment.map(permission => this.checkedPermissions[permission] = []);
    },
    methods: {
        submitData() {
            this.formData.is_manager == true ? this.formData.is_manager = 1 : this.formData.is_manager = 0;
            const role = {
                ...this.formData,
                permissions: this.permissions.map(permission => {
                    return {
                        permission_id: permission
                    }
                })
            }
            this.loading = true;
            this.save(role);
        },
        afterSuccess({data}) {
            this.toastAndReload(data.message, 'role-table');
            this.$hub.$emit('rolesAffected')
            this.loading = false;
            this.closeModal()
        },
        afterSuccessFromGetEditData(response) {
            this.formData = response.data;
            this.formData.is_manager = response.data.permissions.find(permission => permission.name === 'access_all_departments') ? 1 :0;
            this.preloader = false;
            this.permissions = this.collection(response.data.permissions).pluck('id');
            Object.keys(this.data.permissions).map(permission => {
                let checked = this.data.permissions[permission].filter(p => {
                    return this.permissions.includes(p.id);
                })
                this.checkedPermissions[permission] = this.collection(checked).pluck('id');
            })
        },
        closeModal() {
            $('#role-modal').modal('hide');
        },
        checkGroup(event, permission) {
            this.loaded = false;
            const permissions = this.collection(this.data.permissions[permission]).pluck('id');
            if (event.target.checked) {
                this.$set(this.checkedPermissions, permission, permissions);
                this.checkPermissions(permission);
            } else {
                this.$set(this.checkedPermissions, permission, []);
                this.permissions = this.permissions.filter(p => !permissions.includes(parseInt(p)));
            }
            this.loaded = true;
        },
        checkForVisibility(index, permission) {
            return (this.checkedPermissions[permission] && this.checkedPermissions[permission].length)
        },
        checkPermissions(permission) {
            this.loadChecked = false;
            const all_permission_of_group = this.collection(this.data.permissions[permission]).pluck('id');
            const checked_permissions = this.checkedPermissions[permission].map(p => parseInt(p));
            const removable_permissions = all_permission_of_group.filter(permission => !checked_permissions.includes(permission));
            this.permissions = this.permissions.filter(permission => !removable_permissions.includes(parseInt(permission)));
            this.permissions = Array.from(new Set(this.permissions.concat(checked_permissions)));
            this.loadChecked = true;
        },
        ifChecked(permission) {
            const permissions = this.collection(this.data.permissions[permission]).pluck();
            const checked = this.checkedPermissions[permission];
            return permissions.length === checked.length;
        },
        setFormData() {
            const type = this.data.types.find(type => type.alias === this.alias);
            this.formData.type_id = type.id;
            this.formData.tenant_id = null;
            if (this.alias === 'tenant') {
                this.formData.tenant_id = window.tenant.id
            }
        }
    },
    computed: {
        getSubmitURL() {
            return {
                tenant: TENANT_ROLES,
                app: APP_ROLES,
            }[this.alias];
        },
      permissionsExceptAccessDepartment(){
        return Object.keys(this.data.permissions).filter(name => name !== 'access_department')
      }
    }
}
</script>
