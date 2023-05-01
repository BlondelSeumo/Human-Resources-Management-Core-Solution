import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {COMPANY_ASSETS} from "../../Config/ApiUrl";
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [DatatableHelperMixin],
    created() {
        if (this.$can('view_asset_type')) {
            this.getAssetTypes();
        }
    },
    data() {
        return {
            options: {
                name: this.$t('company_asset'),
                url: COMPANY_ASSETS,
                showHeader: true,
                responsive: true,
                showSearch: true,
                showFilter: true,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('asset_name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('type'),
                        type: 'object',
                        key: 'type',
                        isVisible: true,
                        modifier: type => type ? type.name : ''
                    },
                    {
                        title: this.$t('asset_code'),
                        type: 'text',
                        key: 'code',
                        isVisible: true,
                    },
                    {
                        title: this.$t('is_working'),
                        type: 'object',
                        key: 'is_working',
                        isVisible: true,
                        modifier: is_working => this.$t(is_working)
                    },
                    {
                        title: this.$t('employee'),
                        type: 'object',
                        key: 'user',
                        isVisible: true,
                        modifier: user => user ? user.full_name : ''
                    },
                    {
                        title: this.$t('date'),
                        type: 'custom-html',
                        key: 'date',
                        isVisible: true,
                        modifier: date => date ? formatDateToLocal(date) : '-'
                    },
                    {
                        title: this.$t('note'),
                        type: 'component',
                        componentName: 'app-company-asset-note',
                        key: 'note',
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    },
                ],
                filters: [
                    {
                        title: this.$t('asset_type'),
                        type: "multi-select-filter",
                        key: "asset_type",
                        option: [],
                        listValueField: 'name'
                    },
                ],
                paginationType: "pagination",
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('preview'),
                        icon: 'eye',
                        type: 'modal',
                        name: 'preview',
                        modifier: row => this.$can('view_company_assets')
                    },
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        name: 'edit',
                        modifier: row => this.$can('update_company_assets')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        message: this.$t('you_are_going_to_delete_message', { resource: this.$t('asset') }),
                        name: 'delete',
                        modifier: row => this.$can('delete_company_assets')
                    },
                ],
            }
        }
    }
}