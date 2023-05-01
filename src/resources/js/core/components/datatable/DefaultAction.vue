<template>
    <div v-if="visibleActions.length > 0"
         class="btn-group btn-group-action"
         role="group"
         aria-label="Default action">
        <template v-for="(action, index) in visibleActions">
            <span v-if="attendanceType === 'auto' && action.name === 'cancel'"></span>
            <button
                v-else
                :key="index"
                type="button"
                class="btn"
                :class="action.className"
                data-toggle="tooltip"
                data-placement="top"
                :title="action.title"
                @click.prevent="callMethod('',action)">
                <app-icon v-if="action.icon" :name="action.icon"/>
                <span v-else>{{ action.title }}</span>
            </button>
        </template>
    </div>
</template>

<script>
import {ActionMixin} from "./mixin/ActionMixin.js";

export default {
    name: "DefaultAction",
    mixins: [ActionMixin],
    props: {
        rowData: {}
    },
    computed: {
        attendanceType() {
            return this.rowData.review_by ||
            (this.rowData.status && this.rowData.status.name === 'status_pending')
                ? 'manual' : 'auto';
        }
    },
    mounted() {
        setTimeout(() => {
            $('[data-toggle="tooltip"]').tooltip()
        }, 3000);
    },
}
</script>
