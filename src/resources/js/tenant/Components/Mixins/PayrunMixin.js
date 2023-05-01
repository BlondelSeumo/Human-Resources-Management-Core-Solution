export default {
    data() {
        return {
            options: {
                showSearch:false,
                filters: [
                    {
                        title: this.$t('payrun_date'),
                        type: 'range-picker',
                        key: 'payrun_date',
                        option: ["today", "thisMonth", "last7Days", "thisYear"],
                        permission: !!this.$can('view_payrun')
                    },
                    {
                        title: this.$t('created'),
                        type: "range-picker",
                        key: "created",
                        option: ["today", "thisMonth", "last7Days", "thisYear"]
                    },
                    {
                        title: this.$t('type'),
                        type: "multi-select-filter",
                        key: "type",
                        option:[
                            { id: 'manual', name: this.$t('manual') },
                            { id: 'default', name: this.$t('default') },
                            { id: 'employee', name: this.$t('employee') }
                        ],
                        listValueField: 'name',
                    },
                    {
                        title: this.$t('payrun_period'),
                        type: "drop-down-filter",
                        key: "payrun_period",
                        option:[
                            { id: 'monthly', name: this.$t('monthly') },
                            { id: 'weekly', name: this.$t('weekly') },
                            { id: 'customized', name: this.$t('customized') },
                        ],
                        listValueField: 'name',
                    },
                    {
                        title: this.$t('status'),
                        type: "drop-down-filter",
                        key: "status",
                        option:[
                            { id: 'generated', name: this.$t('generated') },
                            { id: 'partially', name: this.$t('partially_sent') },
                            { id: 'sent', name: this.$t('sent') },
                        ],
                        listValueField: 'name',
                    },
                    {
                        title : this.$t('show_conflicted'),
                        type: 'toggle-filter',
                        key: 'conflicted',
                        buttonLabel: {
                            active: 'Yes',
                            inactive: 'No'
                        },
                        header: {
                            title: this.$t('show_conflicted_payrun'),
                            description: this.$t('filter_data_which_are_conflicted')
                        }
                    },
                ]
            }
        }
    },
    methods: {
        setFilterValues(filterValue) {
            if (filterValue.created && typeof filterValue.created != 'string') {
                filterValue.created = JSON.stringify(filterValue.created)
            }
            if (filterValue.payrun_date && typeof filterValue.payrun_date != 'string') {
                filterValue.payrun_date = JSON.stringify(filterValue.payrun_date)
            }
            this.$store.dispatch('updateFilterObject', filterValue)
        },
        setSearchValue(search) {
            this.$store.dispatch('updateFilterObject', {search: search})
        },
    }
}