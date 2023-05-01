<template>
    <date-time-with-note
        :details="details"
        :comment="comment"
        type="punch-in"
    />
</template>

<script>
import DateTimeWithNote from "./AttendanceRequest/DateTimeWithNote";
import {filterLastPendingData} from "../Helper/Helper";

export default {
    name: "PunchInDate",
    components: {DateTimeWithNote},
    props: {
        value: {},
        tableId: {},
    },
    computed: {
        details() {
            if (this.tableId === 'attendance-request-table') {
                return filterLastPendingData(this.value);
            }

            return this.collection(this.value).last();
        },
        comment() {
            if (this.details.comments) {
                return this.collection(this.details.comments.filter(comment => comment.type === 'in-note')).first();
            }
            return {};
        }
    }
}
</script>

<style scoped>

</style>