<template>
    <modal id="manage-role"
           v-model="showModal"
           :title="$fieldTitle('manage', 'users', true)"
           @submit="submitData"
           :scrollable="false"
           body-class="px-0"
           :loading="loading"
           :cancel-btn-label="$t('close')"
           :preloader="preloader"
           :hide-submit-button="role.alias === 'department_manager'"
    >
        <template v-if="role.alias === 'department_manager'">
            <div class="px-primary">
                <app-note
                    class="mb-primary"
                    :title="$t('note')"
                    content-type="html"
                    :notes="`
                        <p>${$t('department_manager_manage_user_message',{
                            department : `<a href='${urlGenerator(apiUrl.DEPARTMENT_FRONTEND)}'>${$t('department')}</a>`
                        })}</p>`"
                />
            </div>
        </template>
        <template v-else>
            <div class="custom-scrollbar px-primary" style="max-height: 300px; overflow-y: auto">
                <div v-for="(user, index) in user_list" :key="index"
                     class="d-flex align-items-center justify-content-between"
                     :class="{'pb-3 mb-3 border-bottom': index !== user_list.length - 1}">
                    <div class="media d-flex align-items-center">
                        <div class="avatars-w-50 ml-2">
                            <app-avatar :title="user.full_name"
                                        :shadow="true"
                                        :img="$optional(user.profile_picture, 'full_url')"
                                        :alt-text="user.full_name"/>
                        </div>
                        <div class="media-body ml-3">
                            {{ user.full_name }}
                            <p class="text-muted font-size-90 mb-0">{{ user.email }}</p>
                        </div>
                    </div>
                    <div>
                        <a href="#" @click="removeUser(user, index)"
                           class="d-flex align-items-center text-muted font-size-90">
                            <app-icon name="trash" class="mr-1" width="19"/>
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-primary" ref="form" :data-url="attachURL">
                <app-input type="multi-select"
                           :list="attachableUsers"
                           list-value-field="full_name"
                           v-model="formData.users"
                           :error-message="$errorMessage(errors, 'users')"
                           :is-animated-dropdown="true"/>
            </div>
        </template>
    </modal>
</template>

<script>
import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";
import ModalMixin from "../../../Mixin/Global/ModalMixin";
import {DETACH_ROLES, TENANT_DETACH_ROLES, TENANT_ROLES} from "../../../Config/apiUrl";

export default {
    mixins: [FormHelperMixins, ModalMixin],
    name: "ManageRolesModal",
    props: {
        role: {
            required: true
        },
        alias: {}
    },
    data() {
        return {
            user_list: [],
            formData: {
                users: []
            },
            selectedUser: null,
        }
    },
    computed: {
        attachableUsers() {
            return this.$store.state.user.users;
        },
        attachURL() {
            return {
                tenant: `${TENANT_ROLES}/${this.role.id}/attach-users`,
                app: `admin/auth/roles/${this.role.id}/attach-users`,
            }[this.alias];
        },
        detachURL() {
            return {
                tenant: `${TENANT_DETACH_ROLES}/${this.selectedUser.id}`,
                app: `${DETACH_ROLES}/${this.selectedUser.id}`,
            }[this.alias];
        }
    },
    methods: {
        removeUser(user, index) {
            this.preloader = true;
            this.selectedUser = user;
            const url = this.detachURL;

            this.axiosPost({url, data: {roles: this.role.id}}).then(response => {
                this.toastAndReload(response.data.message, 'user-table')
                this.$hub.$emit('reload-role-table');
                this.user_list = this.collection(this.user_list).removeObject(user.id);
                this.$store.dispatch('addUser', user);
            }).catch(error => {
                this.toastException(error.response.data)
            }).finally(response => {
                this.preloader = false;
            })
        },
        afterSuccess(response) {
            this.$toastr.s('', response.data.message);
            this.user_list = this.attachableUsers.filter(user => {
                return this.formData.users.includes(user.id);
            }).concat(this.user_list);

            this.$store.dispatch('getUsers', {
                users: this.collection(this.role.users).pluck('id').concat(this.formData.users),
                alias: this.alias
            });

            this.formData.users = [];

            this.$emit('input', false);

            $("#manage-role").modal('hide');

            this.$hub.$emit('reload-role-table');
        }
    },
    watch: {
        'role.users.length': {
            handler: function (users) {
                this.user_list = this.role.users;
                this.$store.dispatch(
                    'getUsers', {
                        users: this.collection(this.role.users).pluck('id'),
                        alias: this.alias
                    });
            },
            immediate: true
        }
    }
}
</script>

<style scoped>

</style>
