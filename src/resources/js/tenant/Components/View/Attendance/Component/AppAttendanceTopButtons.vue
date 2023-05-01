<template>
    <div>
        <app-default-button
            btn-class="btn btn-success mr-1"
            :title="$t('settings')"
            :url="apiUrl.ATTENDANCE_SETTINGS_FRONT_END"
            v-if="settingButton && $can('view_attendance_settings')"
        />

        <div class="btn-group dropdown">
            <app-default-button
                v-if="this.$can('update_attendance_status') || requestButton"
                :title="$fieldTitle(buttonFirstTitle, 'attendance', true)"
                @click="openAttendanceModal()"
            />
            <button v-if="this.$can('update_attendance_status') && requestButton" class="btn btn-primary rounded-right px-3" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-chevron-down fa-sm"></i>
            </button>
            <div v-if="this.$can('update_attendance_status') && requestButton" class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item" href="#" @click="openAdminReqAttendanceModal()">
                    {{ $t('request_attendance') }}
                </a>
            </div>
        </div>

        <app-attendance-create-edit-modal
            :tableId="tableId"
            v-if="isAttendanceModalActive"
            v-model="isAttendanceModalActive"
            :specific-id="specificUserId"
        />
    </div>
</template>

<script>

export default {
    name: "AppAttendanceTopButtons",
    props: {
        requestButton: {
            type: Boolean,
            default: false
        },
        settingButton: {
            type: Boolean,
            default: true
        },
        tableId: {},
        specificId: {}
    },
    data() {
        return {
            adminRequestId: null,
            isAttendanceModalActive: false,
        }
    },
    computed: {
        user() {
            return window.user
        },
        buttonFirstTitle() {
            return this.$can('update_attendance_status') ? 'add' : 'request';
        },
        specificUserId() {
            return this.specificId ? this.specificId
                : this.adminRequestId ? this.adminRequestId
                    : null
        }
    },
    methods: {
        openAdminReqAttendanceModal() {
            this.adminRequestId = window.user.id
            this.isAttendanceModalActive = true;
        },
        openAttendanceModal() {
            this.adminRequestId = null;
            this.isAttendanceModalActive = true;
        }
    }
}
</script>
