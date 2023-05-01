import DatatableHelperMixin from "./Global/DatatableHelperMixin";
import {TENANT_ROLES} from "../Config/apiUrl";
import {urlGenerator} from "../Helper/AxiosHelper";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        let url = this.alias === 'tenant' ? TENANT_ROLES : 'admin/auth/roles'
        return {
            manage: false,
            options: {
                name: 'roles',
                url: url.split('/').filter(d => d).join('/'),
                columns: [
                    {
                        title: this.$fieldTitle('role', 'name'),
                        type: 'text',
                        key: 'name',
                        sortAble: true,
                    },
                    this.$can('update_roles') ?
                        {
                            title: this.$t('permission'),
                            type: 'button',
                            key: 'id',
                            className: 'btn btn-sm btn-primary rounded-pill px-3 py-1',
                            actionType: 'manage',
                            modifier: (id, role) => {
                                return (role.is_default && role.is_admin) ? false :
                                    (role.alias === 'employee' || role.alias === 'department_manager') ? this.$t('view') : this.$t('manage')
                            }
                        }
                        : {},
                    {
                        title: this.$t('users'),
                        type: 'component',
                        key: 'users',
                        isVisible: true,
                        componentName: 'image-group',
                    },
                    {
                        title: this.$t('manage_users'),
                        type: 'action',
                        key: 'invoice',
                        isActive: true
                    },
                ],
                datatableWrapper: false,
                showHeader: true,
                tableShadow: false,
                showSearch: false,
                showFilter: false,
                paginationType: "loadMore",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-roles-modal',
                        modalId: 'role-modal',
                        url: '',
                        name: 'edit',
                        modifier: role => this.$can('update_roles') && !(role.is_default && role.is_admin) && !['manager', 'employee', 'department_manager'].includes(role.alias)
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        component: 'app-confirmation-modal',
                        type: 'modal',
                        modalId: 'role-delete-modal',
                        url: '',
                        name: 'delete',
                        modifier: role => this.$can('delete_roles') && !(role.is_default && role.is_admin) && !['manager', 'employee', 'department_manager'].includes(role.alias)
                    },
                    {
                        title: this.$fieldTitle('manage', 'users', true),
                        icon: 'tool',
                        type: 'none',
                        modalId: 'role-manage-modal',
                        url: '',
                        name: 'manage-roles',
                        modifier: (role) => this.$can('attach_users_to_roles')
                    }
                ],
            },
        }
    }
}
