import DatatableHelperMixin from "../../../common/Mixin/Global/DatatableHelperMixin";
import {DEPARTMENTS} from "../../Config/ApiUrl";
import {formatDateToLocal} from "../../../common/Helper/Support/DateTimeHelper";

export default {
    mixins: [DatatableHelperMixin],
    data() {
        return {
            options: {
                name: this.$t('department'),
                url: DEPARTMENTS,
                showHeader: true,
                showCount: true,
                showClearFilter: true,
                columns: [
                    {
                        title: this.$t('name'),
                        type: 'text',
                        key: 'name',
                        isVisible: true,
                    },
                    {
                        title: this.$t('manager'),
                        type: 'custom-html',
                        key: 'manager',
                        modifier: (manager) => {
                            return manager.full_name;
                        }
                    },
                    {
                        title: this.$t('description'),
                        type: 'custom-html',
                        key: 'description',
                        isVisible: true,
                        modifier: (description) => {
                            return description ? description : '-';
                        }
                    },
                    {
                        title: this.$t('status'),
                        type: 'custom-html',
                        key: 'status',
                        isVisible: true,
                        modifier: status => {
                            return `<span class="badge badge-pill badge-${status.class}">
                                ${status.translated_name}
                            </span>`
                        }
                    },
                    {
                        title: this.$t('parent_department'),
                        type: 'custom-html',
                        key: 'parent_department',
                        modifier: (department) => {
                            return department ? this.$optional(department, 'name') : '-';
                        }
                    },
                    {
                        title: this.$t('location'),
                        type: 'custom-html',
                        key: 'location',
                        isVisible: true,
                        modifier: (location) => {
                            return location ? location : '-';
                        }
                    },
                    {
                        title: this.$t('created'),
                        type: 'custom-html',
                        key: 'created_at',
                        isVisible: true,
                        modifier: (value) => {
                            return formatDateToLocal(value);
                        }
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
                        title: this.$t('status'),
                        type: "checkbox",
                        key: "status",
                        option: [],
                        listValueField: 'value'
                    },
                ],
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions: [
                    {
                        title: this.$t('edit'),
                        type: 'modal',
                        component: 'app-department-modal',
                        modalId: 'department-modal',
                        url: DEPARTMENTS,
                        name: 'edit',
                        modifier: row => this.$can('update_departments')
                    },
                    {
                        title: this.$t('deactivate'),
                        key: "deactivate",
                        icon: 'slash',
                        promptSubtitle: this.$t('you_are_going_to_deactivate_a_department'),
                        modalClass: 'warning',
                        modifier: row => {
                            return row.status.name === "status_active" && Boolean(row.department_id) &&
                                this.$can('update_departments_status')
                        }
                    },
                    {
                        title: this.$t('activate'),
                        key: "activate",
                        icon: 'check-circle',
                        promptSubtitle: this.$t('you_are_going_to_activate_a_department'),
                        modalClass: 'primary',
                        modifier: row => {
                            return row.status.name === "status_inactive" && this.$can('update_departments_status')
                        }
                    },
                    {
                        title: this.$t('move_employee'),
                        type: 'modal',
                        component: 'app-employee-move-modal',
                        modalId: 'employee-move-modal',
                        name: 'move-employee',
                        modifier: row => this.$can('move_department_employees')
                    }
                ],
            }
        }
    }
}
