<template>
    <div class="column-filter single-filter">
        <div class="dropdown keep-inside-clicks-open">
            <button class="btn btn-filter" type="button" id="dropdownMenuButton" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <app-icon :name="'layout'"/>
            </button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton-manage">
                <div class="btn-dropdown-close d-sm-none">
                    <span class="title">
                        {{ $t('manage_columns') }}
                    </span>
                    <span class="back float-right" @click.prevent="closeDropDown">
                        <app-icon :name="'x'"/>
                    </span>
                </div>
                <div class="dropdown-item">
                    <h6>{{ $t('want_to_manage_datatable') }}</h6>
                    <p class="text-justify mb-0 filter-subtitle-text">
                        {{ $t('please_drag_and_drop_your_column_to_reorder_your_table_and_enable_see_option_as_you_want') }}
                    </p>
                </div>

                <div class="dropdown-item manage-column-options custom-scrollbar pt-0">
                    <draggable :forceFallback="true" v-model="list" tag="div" v-bind="dragOptions" @start="dragstart($event)"
                               @end="dragend" @change="change">

                        <div v-for="(element,index) in list"
                             v-if="!isUndefined(element.title)"
                             :key="'manage-column-'+index"
                             class="row py-2">

                            <div class="col-12 d-flex justify-content-between">
                                <div class="content-type">
                                    <app-icon name="menu"/>
                                    {{ element.title }}
                                </div>
                                <label class="custom-control border-switch mb-0 mr-3">
                                    <input type="checkbox"
                                           class="border-switch-control-input"
                                           :checked="element.isVisible"
                                           @click="element.isVisible = !element.isVisible"
                                           :id="'switch-'+index">
                                    <span class="border-switch-control-indicator"></span>
                                </label>
                            </div>
                        </div>
                    </draggable>
                </div>
                <div class="dropdown-divider d-none d-sm-block"/>

                <filter-action @apply="applyColumnSort" @clear="clear"/>
            </div>
        </div>
    </div>
</template>

<script>
import CoreLibrary from "../../helpers/CoreLibrary";
import {FilterMixin} from "../filter/mixins/FilterMixin";
import FilterAction from "../filter/FilterAction";
import draggable from 'vuedraggable';

export default {
    name: "ManageColumns",
    components: {draggable, FilterAction},
    mixins: [FilterMixin],
    extends: CoreLibrary,
    props: {
        manageOption: {
            type: Array
        },
        initOption: {
            type: Array
        }
    },
    data() {
        return {
            list: []
        }
    },
    mounted() {
        this.list = _.cloneDeep(this.manageOption);
    },
    computed: {
        dragOptions() {
            return {
                animation: 300
            };
        }
    },
    watch: {
        initOption: function (data) {
            this.clear();
            this.$emit('get-columns-options', data);
        }
    },
    methods: {
        dragstart(ev) {
            this.drag = true;
            ev.target.classList.add('catch-container');
            ev.item.classList.add('catch-item');
        },
        dragend(ev) {
            this.drag = false;
            ev.target.classList.remove('catch-container');
            ev.item.classList.remove('catch-item');
        },
        change() {
        },
        clear() {
            this.list = _.cloneDeep(this.initOption);
        },
        applyColumnSort() {
            this.$emit('get-columns-options', this.list);
            this.closeDropDown();
        }
    }
}
</script>
