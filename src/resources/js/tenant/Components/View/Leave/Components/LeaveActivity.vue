<template>
    <div>
        <app-note-editor
            v-if="Object.keys(comment).length"
            :id="comment.id"
            :row-data="comment"
            :note-title="$t(comment.type)"
            :note-description="comment.comment"
            :url="`${apiUrl.LEAVE_NOTES}/${comment.id}`"
            :edit-permission="permission"
        />
        <button type="button"
                class="btn p-0 primary-text-color"
                data-toggle="dropdown"
                @click="openResponseLog()"
        >
            <app-icon name="activity" class="text-primary"/>
        </button>

        <app-leave-response-log-modal
            v-if="isResponseLogModalActive"
            v-model="isResponseLogModalActive"
            :url="responseLogUrl"
            :table-id="tableId"
        />

    </div>
</template>

<script>
import {TENANT_BASE_URL} from "../../../../../common/Config/UrlHelper";

export default {
    name: "LeaveActivity",
    props: {
        value: {},
        rowData: {},
        tableId: {}
    },
    data() {
      return {
          isResponseLogModalActive: false,
      }
    },
    computed: {
        user(){
            return window.user;
        },
        comment() {
            return this.collection(this.value.filter(comment => comment.type === 'reason-note')).first();
        },
        permission() {
            return this.$can('update_leave_notes') &&
                Number(this.user.id ) === Number(this.comment.user_id) &&
                this.rowData?.status.name == 'status_pending';
        },
        responseLogUrl() {
            return `${TENANT_BASE_URL}app/leaves/${this.rowData.id}/log`
        }
    },
    methods: {
        openResponseLog(){
            let action = {name: 'response-log'}
            this.$hub.$emit('getLeaveActivityAction', this.rowData, action, true)
        }
    }
}

</script>

<style scoped>

</style>