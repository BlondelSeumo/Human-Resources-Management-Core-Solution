<template>
    <div class="d-inline-block">
        {{ data.attendance_details_id ? $t('change') : $t('new') }}
        <app-note-editor
            v-if="Object.keys(comment).length"
            :id="comment.id"
            :row-data="comment"
            :note-title="$t(comment.type)"
            :note-description="comment.comment"
            :url="`${apiUrl.ATTENDANCE_NOTES}/${comment.id}`"
            :edit-permission="permission"
        />
    </div>
</template>

<script>
import {filterLastPendingData} from "../../Helper/Helper";

export default {
    name: 'AttendanceDetailsType',
    props: {
        value: {},

    },
    data() {
        return {}
    },
    computed: {
        data() {
            return filterLastPendingData(this.value)
        },
        comment() {
            if (this.data.comments) {
                return this.collection(this.data.comments.filter(comment => comment.type === 'request')).first();
            }
            return {};
        },
        user() {
            return window.user;
        },
        permission() {
            return Boolean(this.$can('update_attendance_notes')) &&
                //Number(this.user.id) === Number(this.comment.user_id) &&
                this.data?.status?.name == 'status_pending'
        }
    },
}
</script>