import Vue from 'vue';
import {CUSTOM_FIELDS, TENANT_CUSTOM_FIELDS} from "../../../Config/apiUrl";
import DataTableHelperMixin from "../DatatableHelperMixin";


export default {
    mixins: [DataTableHelperMixin,Vue],

    data() {
        return {
            options: {
                name: this.$t('custom_fields'),
                url: this.props.alias === 'app' ? CUSTOM_FIELDS : TENANT_CUSTOM_FIELDS,
                showHeader: true,
                tableShadow: false,
                showManageColumn: false,
                tablePaddingClass: 'px-0 py-primary',
                columns: [
                    {
                        title: Vue.prototype.$t('name'),
                        type: 'text',
                        key: 'name',
                        uniqueKey: 'id',
                    },
                    {
                        title: Vue.prototype.$t('type'),
                        type: 'object',
                        key: 'custom_field_type',
                        modifier: custom_field_type => custom_field_type.translated_name
                    },
                    {
                        title: this.$t('context'),
                        type: 'custom-html',
                        key: 'context',
                        isVisible: true,
                        modifier: (value) =>  {
                            return this.$t(value);
                        }
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        key: 'invoice',
                        isVisible: true
                    },
                ],
                filters: [
                ],
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
                        component: 'app-brand-add-edit-modal',
                        modalId: 'brand-add-edit-modal',
                        name: 'edit'
                    }, {
                        title: this.$t('delete'),
                        icon: 'trash',
                        type: 'modal',
                        component: 'app-confirmation-modal',
                        modalId: 'app-confirmation-modal',
                        url: `/${this.props.alias === 'app' ? CUSTOM_FIELDS : TENANT_CUSTOM_FIELDS}`,
                        name: 'delete'
                    }
                ],
            }
        }
    }
}
