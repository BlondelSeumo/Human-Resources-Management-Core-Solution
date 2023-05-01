<template>
    <div class="card card-with-shadow border-0 h-100 min-height-350 mt-primary">
        <div class="card-header bg-transparent p-primary d-flex justify-content-between align-items-center">
            <h4 class="card-title text-capitalize mb-0">{{$t('announcements')}}</h4>
        </div>
        <div class="card-body d-flex flex-column p-0">
            <app-table
                id="dashboard-announcement-table"
                :options="options"
            />
        </div>
    </div>

</template>

<script>
import DashboardAnnouncementMixin from "../../../Mixins/DashboardAnnouncementMixin";

export default {
    name: "EmployeeAnnouncement",
    mixins: [DashboardAnnouncementMixin],
    data() {
        return {
            message: '',
        }
    },
    created() {
    },
    methods: {
    },
}
</script>
