<template>
    <div class="filters-loop-wrapper" v-click-outside="closeAllFilterDropdown">
        <template v-for="(filter, index) in filterComputed">

            <date-range-filter
                v-if="filter.type === 'range-picker'"
                :key="'range-picker'+index"
                :table-id="tableId"
                :filter-key="filter.key"
                :label="filter.title"
                :visible-value="false"
                :preset-ranges="filter.option"
                @get-value="store"
            />

            <range-filter
                v-if="filter.type === 'range-filter'"
                :filter-key="filter.key"
                :table-id="tableId"
                :key="'range-filter'+index"
                :label="filter.title"
                :max-range="filter.maxRange"
                :min-range="filter.minRange"
                :max-title="filter.maxTitle"
                :min-title="filter.minTitle"
                :sign="filter.sign"
                @get-value="store"
            />

            <checkbox-filter
                :filter-key="filter.key" :key="'checkbox'+index"
                v-if="filter.type === 'checkbox'"
                :table-id="tableId"
                :label="filter.title"
                :filter-option="filter.option"
                :list-value-field="filter.listValueField"
                :active="filter.active"
                @get-value="store"
            />

            <radio-filter
                v-if="filter.type === 'radio'"
                :filter-key="filter.key"
                :table-id="tableId"
                :key="'radio'+index"
                :label="filter.title"
                :filter-option="filter.option"
                :list-value-field="filter.listValueField"
                :active="filter.active"
                :header="filter.header"
                @get-value="store"
            />

            <drop-down-filter
                v-if="filter.type === 'drop-down-filter'"
                :key="'drop-down-filter'+index"
                :filter-key="filter.key"
                :id="`${filter.key}-${tableId}`"
                :table-id="tableId"
                :list="filter.option"
                :label="filter.title"
                :list-value-field="filter.listValueField"
                :active="filter.active"
                @get-value="store"
            />

            <dropdown-menu-filter
                v-if="filter.type === 'dropdown-menu-filter'"
                :key="'dropdown-menu-filter'+index"
                :filter-key="filter.key"
                :table-id="tableId"
                :list="filter.option"
                :label="filter.title"
                :active="filter.active"
                :list-value-field="filter.listValueField"
                @get-value="store"
            />

            <multi-select-filter
                v-if="filter.type === 'multi-select-filter'"
                :key="'multi-select-filter'+index"
                :filter-key="filter.key"
                :table-id="tableId"
                :list="filter.option"
                :label="filter.title"
                :active="filter.active"
                :list-value-field="filter.listValueField"
                @get-value="store"
            />

            <date-filter
                v-if="filter.type === 'date'"
                :key="'date-filter'+index"
                :table-id="tableId"
                :label="filter.title"
                :filter-key="filter.key"
                @get-value="store"
            />

            <app-tab-filter
                v-if="filter.type === 'tab-filter'"
                :key="'tab-filter'+index"
                :table-id="tableId"
                :filter-key="filter.key"
                :filter-option="filter.option"
                @get-value="store"
            />

            <avatar-filter
                v-if="filter.type === 'avatar-filter'"
                :key="'avatar-filter'+index"
                :table-id="tableId"
                :filter-key="filter.key"
                :img-relationship="filter.imgRelationship"
                :img-key="filter.imgKey"
                :list="filter.option"
                :label="filter.title"
                :list-value-field="filter.listValueField"
                :active="filter.active"
                @get-value="store"
            />

            <toggle-filter
                v-if="filter.type === 'toggle-filter'"
                :key="'toggle-filter'+index"
                :table-id="tableId"
                :filter-key="filter.key"
                :label="filter.title"
                :header="filter.header"
                :button-label="filter.buttonLabel"
                @get-value="store"
            />

        </template>
    </div>
</template>


<script>
import AvatarFilter from "./AvatarFilter";
import RangeFilter from "./RangeFilter";
import CheckboxFilter from "./CheckboxFilter";
import DropDownFilter from "./DropDownFilter";
import RadioFilter from "./RadioFilter";
import DateRangeFilter from "./DateRangeFilter";
import coreLibrary from '../../helpers/CoreLibrary';
import DropdownMenuFilter from "./DropdownMenuFilter";
import MultiSelectFilter from "./MultiSelectFilter";
import DateFilter from "./DateFilter";
import ToggleFilter from "./ToggleFilter";

export default {
    name: "AppFilter",
    extends: coreLibrary,
    components: {
        AvatarFilter,
        RadioFilter,
        DateRangeFilter,
        DropDownFilter,
        CheckboxFilter,
        RangeFilter,
        DropdownMenuFilter,
        MultiSelectFilter,
        DateFilter,
        ToggleFilter
    },
    props: {
        tableId: String,
        filters: {
            required: true,
            type: Array,
        },
        options: {}
    },
    data() {
        return {
            filterValues: {}
        }
    },
    computed: {
        filterComputed() {
            let keys = [],
                filtersWP = this.filters.filter(item => (this.isUndefined(item.permission) || item.permission));
            filtersWP.map(i => {
                let k, t, v;
                k = _.snakeCase(_.lowerCase(i.key));
                t = _.kebabCase(_.lowerCase(i.type));
                v = {'title': i.title, 'key': k, 'type': t};
                if (i.option) {
                    v['option'] = i.option;
                }

                if (!this.isUndefined(i.active) || !this.isUndefined(i.initValue)) {
                    v['active'] = i.active || i.initValue;
                }

                if (i.header) {
                    v['header'] = i.header
                }

                if (i.listValueField) {
                    v['listValueField'] = i.listValueField;
                }

                if (i.buttonLabel) v['buttonLabel'] = i.buttonLabel

                if (i.type === 'range-filter') {
                    v['maxTitle'] = i.maxTitle;
                    v['minTitle'] = i.minTitle;
                    v['sign'] = i.sign;
                    if (!this.isUndefined(i.minRange)) {
                        v['minRange'] = i.minRange;
                    }
                    if (!this.isUndefined(i.maxRange)) {
                        v['maxRange'] = i.maxRange;
                    }
                }

                if (i.type === 'avatar-filter') {
                    if (!this.isUndefined(i.imgRelationship)) {
                        v['imgRelationship'] = i.imgRelationship;
                    }
                    if (!this.isUndefined(i.imgKey)) {
                        v['imgKey'] = i.imgKey;
                    }

                }

                keys.push(v)
            });
            return keys;

        }
    },
    methods: {
        store(v) {
            this.filterValues[v.key] = v.value;
            this.$emit('get-values', this.filterValues);
        },
        closeAllFilterDropdown() {
            $(`.filters-loop-wrapper .dropdown-menu.show`).removeClass('show')
        }
    },
    mounted() {
        this.$hub.$on('clearAllFilter-' + this.tableId, () => {
            this.filterValues = {};
        });
    }
}
</script>
