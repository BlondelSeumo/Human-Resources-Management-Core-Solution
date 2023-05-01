<template>
    <div class="d-flex align-items-center" :class="alignmentClass">
        <span
            :class="`badge badge-pill badge-${rowData.employment_status ? rowData.employment_status.class : ''} text-capitalize`">
            {{ $optional(rowData, 'employment_status', 'name') }}
        </span>
        <app-note-editor
            v-if="$optional(rowData, 'employment_status', 'alias') === 'terminated'"
            :id="rowData.id"
            :row-data="rowData"
            icon-class="size-26"
            :note-title="$t('termination_reason')"
            :note-description="description"
            :url="`${apiUrl.EMPLOYEES}/${rowData.id}/update-termination-note`"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";

export default {
    name: "EmployeeStatus",
    mixins: [FormHelperMixins],
    props: {
        rowData: {},
        alignmentClass: {}
    },
    computed: {
        description() {
            if (this.rowData?.employment_status?.pivot?.description) {
                return this.rowData?.employment_status?.pivot?.description;
            }
            return this.$t('default_termination_reason_note');
        }
    }
}
</script>
