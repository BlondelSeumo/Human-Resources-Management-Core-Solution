import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {DESIGNATION} from "../../Config/ApiUrl";

export default {
    mixins: [DatatableHelperMixin],
    data() {

        return {
            options: {
                name: this.$t('tenant_groups'),
                url: DESIGNATION,
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
                        title: this.$t('description'),
                        type: 'custom-html',
                        key: 'description',
                        isVisible: true,
                        modifier: (description) => {
                            return  description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('no_of_user'),
                        type: 'text',
                        key: 'users_count',
                        isVisible: true,
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],
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
                        modifier: () => this.$can('update_designations')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash-2',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: DESIGNATION,
                        name: 'delete',
                        modifier:( row) => this.$can('delete_designations') && !parseInt(row.is_default)
                    }
                ],
            },
        }
    }
}
