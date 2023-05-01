<template>
    <div>
        <div class="d-flex align-items-center">
            <span class="mr-1">{{ inTime }} </span>
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
        <span v-html="punchInFrom"></span>
    </div>
</template>

<script>
    import {dateTimeFormat} from '../../../../../../common/Helper/Support/DateTimeHelper'

    export default {
        name:'DateTimeWithNote',
        props: {
            details: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            comment: {
                type: Object,
                default: function () {
                    return {};
                }
            },
            type: {
                type: String,
                default: 'punch-out'
            }
        },
        computed: {
            inTime(){
                const field = {'punch-in': 'in_time', 'punch-out': 'out_time'}[this.type];
                if (this.details[field]) {
                    return dateTimeFormat(this.details[field])
                }
                return this.$t('not_yet')
            },
            punchInFrom(){
                const field = {'punch-in': 'in_time'}[this.type];
                if (this.details[field]) {
                    let punchInDetails =  this.details.in_ip_data ? JSON.parse(this.details.in_ip_data) : {};
                    if(punchInDetails.work_from_home){
                        return `<span class="badge badge-pill badge-sm badge-secondary">Home</span>`;
                    }
                }
            },
            user() {
                return window.user;
            },
            permission() {
                return false;
                //return this.$can('update_attendance_notes') && Number(this.user.id ) === Number(this.comment.user_id);
            }
        },
    }
</script>