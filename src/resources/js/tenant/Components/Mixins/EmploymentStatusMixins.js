import {EMPLOYMENT_STATUSES} from '../../Config/ApiUrl'

export default {
    data() {
        return {
            options: {
                name: this.$t('employment_statuses'),
                url: EMPLOYMENT_STATUSES,
                showHeader: true,
                showCount: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('preview'),
                        type: 'custom-html',
                        key: 'name',
                        isVisible: true,
                        modifier: (value, row) => `<span class="badge badge-pill badge-${row.class}">${value}</span>`
                    },
                    {
                        title: this.$t('description'),
                        type: 'custom-html',
                        key: 'description',
                        isVisible: true,
                        modifier: (description) => {
                            return  description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],
                filters: [],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        component: 'app-employment-status-create-edit-modal',
                        modalId: 'app-modal',
                        url: EMPLOYMENT_STATUSES,
                        name: 'edit',
                        modifier: () => this.$can('update_employment_statuses')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: EMPLOYMENT_STATUSES,
                        name: 'delete',
                        modifier: (row) => this.$can('delete_employment_statuses') && !parseInt(row.is_default)
                    }
                ],
            }
        }
    }
}
