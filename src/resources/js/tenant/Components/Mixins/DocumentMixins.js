import {DOCUMENTS} from '../../Config/ApiUrl'
import {ucFirst} from "../../../common/Helper/Support/TextHelper";
import {urlGenerator} from "../../../common/Helper/AxiosHelper";

export default {
    data() {
        return {
            urlGenerator,
            options: {
                name: this.$t('document'),
                url: DOCUMENTS + `?user_id=${this.props.id}`,
                showHeader: true,
                tableShadow:false,
                tablePaddingClass:'pt-primary',
                // cardViewComponent: 'app-tenant-document-card-view',
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('attachment'),
                        type: 'custom-html',
                        key: 'path',
                        modifier: (value) => value ? `<a href="${urlGenerator(value)}" target="_blank">
                                                                <i data-feather="external-link"/>
                                                           </a>` : '-'
                    },
                    {
                        title: this.$t('added_by'),
                        type: 'object',
                        key: 'created_by',
                        isVisible: true,
                        modifier: (value) => {
                            return value.full_name;
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    },
                ],
                filters: [
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
                        component: 'app-document-modal',
                        modalId: 'document-modal',
                        url: DOCUMENTS,
                        name: 'edit',
                        modifier: row => true
                    },
                    {
                        title: this.$t('delete'),
                        name: 'delete',
                        icon: 'trash-2',
                        modalClass: 'warning',
                        url: DOCUMENTS,
                        modifier: row => true
                    },
                ],
            }
        }
    }
}