import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {ANNOUNCEMENTS} from "../../Config/ApiUrl";
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";
import {DepartmentFilterMixin} from "./FilterMixin";

export default {
    mixins: [DatatableHelperMixin, DepartmentFilterMixin],
    data() {
        return {
            options: {
                name: this.$t('announcement'),
                url: ANNOUNCEMENTS,
                showHeader: true,
                responsive: true,
                showSearch: true,
                showFilter: true,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('title'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('department'),
                        type: 'object',
                        key: 'departments',
                        isVisible: true,
                        modifier: (departments) => {
                            let departmentName = '';
                            departments.forEach((element, index) => {
                                departmentName += index > 0 ? ', ' + element.name : element.name;
                            });
                            departmentName = departmentName ? departmentName : '-';
                            return departmentName;
                        }
                    },
                    {
                        title: this.$t('start_date'),
                        type: 'custom-html',
                        key: 'start_date',
                        isVisible: true,
                        modifier: start_date => start_date ? formatDateToLocal(start_date) : '-'
                    },
                    {
                        title: this.$t('end_date'),
                        type: 'custom-html',
                        key: 'end_date',
                        isVisible: true,
                        modifier: end_date => end_date ? formatDateToLocal(end_date) : '-'
                    },
                    {
                        title: this.$t('description'),
                        type: 'component',
                        componentName: 'app-announcement-description',
                        key: 'description',
                    },
                    {
                        title: this.$t('created_by'),
                        type: 'custom-html',
                        key: 'created_by',
                        isVisible: true,
                        modifier: created_by => created_by ? created_by.full_name : '-'
                    },
                    {
                        title: this.$t('actions'),
                        type: 'action',
                        isVisible: true
                    },
                ],
                filters: [
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "date",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('department'),
                        type: "multi-select-filter",
                        key: "departments",
                        option: [],
                        listValueField: 'name',
                        permission: !!this.$can('view_departments')
                    },
                ],
                paginationType: "pagination",
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "default",
                actions: [
                    {
                        title: this.$t('edit'),
                        icon: 'edit',
                        type: 'modal',
                        name: 'edit',
                        modifier: row => this.$can('update_announcements')
                    },
                    {
                        title: this.$t('delete'),
                        icon: 'trash',
                        message: this.$t('you_are_going_to_delete_message', { resource: this.$t('announcement') }),
                        name: 'delete',
                        modifier: row => this.$can('delete_announcements')
                    },
                ],
            }
        }
    }
}
