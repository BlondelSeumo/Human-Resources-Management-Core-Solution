<template>
    <div v-if="visibleActions.length > 0"
         class="dropdown options-dropdown d-inline-block">
        <button type="button"
                :key="'more-vertical-'+visibleActions.length"
                class="btn-option btn"
                data-toggle="dropdown"
                :title="$t('actions')">
            <app-icon name="more-vertical"/>
        </button>

        <div :key="'dropdown-'+visibleActions.length"
             class="dropdown-menu dropdown-menu-right py-2 mt-1">
            <a v-for="(action, index) in visibleActions"
               :key="index"
               class="dropdown-item px-4 py-2"
               :class="action.className"
               @click.prevent="callMethod('',action)"
               href="#">
                {{ action.title }}
            </a>
        </div>
    </div>
</template>

<script>
import {ActionMixin} from "./mixin/ActionMixin.js";

export default {
    name: "DropdownAction",
    mixins: [ActionMixin],
}
</script>
