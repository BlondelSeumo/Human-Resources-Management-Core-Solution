import {EMPLOYEES} from '../../Config/ApiUrl'
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";
import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('company_assets'),
                url: `${EMPLOYEES}/${this.props.id}/company-assets`,
                showHeader: true,
                tableShadow:false,
                tablePaddingClass:'pt-primary',
                columns: [
                    {
                        title: this.$t('asset_name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('asset_code'),
                        type: 'text',
                        key: 'code',
                        isVisible: true,
                    },
                    {
                        title: this.$t('serial_no'),
                        type: 'text',
                        key: 'serial_number',
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
                        title: this.$t('type'),
                        type: 'object',
                        key: 'type',
                        isVisible: true,
                        modifier: type => type ? type.name : ''
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
                    // {
                    //     title: this.$t('actions'),
                    //     type: 'action',
                    //     isVisible: true
                    // },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                orderBy: 'desc',
                actionType: "default",
                // actions: [
                //     {
                //         title: this.$t('preview'),
                //         icon: 'eye',
                //         type: 'modal',
                //         name: 'preview',
                //     },
                // ],
            }
        }
    }
}