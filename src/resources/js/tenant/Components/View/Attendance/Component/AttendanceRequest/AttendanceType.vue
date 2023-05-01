<template>
    <div class="d-inline-block">
        <span v-if="!hideType">{{ type }}</span>
        <app-note-editor
            v-if="Object.keys(comment).length"
            :id="comment.id"
            :row-data="comment"
            :note-title="$t(comment.type)"
            :note-description="comment.comment"
            :url="`${apiUrl.ATTENDANCE_NOTES}/${comment.id}`"
            :edit-permission="editPermission"
        />
    </div>
</template>

<script>
export default {
    name: "AttendanceType",
    props: {
        value: {

        },
        tableId: {

        },
        hideType: {
            default: false
        }
    },
    computed: {
        editPermission(){
            return this.$can('update_attendance_notes') &&
                Number(user.id ) === Number(this.comment.user_id) &&
                this.value.status?.name == 'status_pending'
        },
        type() {
            if (this.value.length > 1) {
                const types = this.value.reduce((prev, curr) => {
                    prev.auto += curr.review_by ? 0 : 1;
                    prev.manual += curr.review_by ? 1 : 0;
                    return prev;
                }, {
                    auto: 0,
                    manual: 0
                });
                const {auto, manual} = types;

                return auto > manual ? this.$t('auto') : this.$t('manual');
            }

            const details = this.collection(this.value).first();

            return details.review_by ? this.$t('manual') : this.$t('auto');
        },
        comment() {
            if (this.value.length === 1) {
                return this.collection(this.collection(this.value).first().comments.filter(comment => comment.type === 'manual')).first();
            }

            return {};
        },
        user() {
            return window.user;
        }
    }
}
</script>

<style scoped>

</style>