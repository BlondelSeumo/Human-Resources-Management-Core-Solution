<template>
    <div>
        <div class="row mb-primary" v-if="this.$can('view_all_leaves')">
            <div class="col-md-6 col-lg-3">
                <div class="default-base-color text-center rounded p-3">
                    <h5>{{ summaries.on_leaves }}</h5>
                    <p class="text-muted mb-0">{{ $t('employees_on_leave') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="default-base-color text-center rounded p-3">
                    <h5>{{ summaries.leave_hours }}</h5>
                    <p class="text-muted mb-0">{{ $t('total_leave_hours') }}</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="default-base-color text-center rounded p-3">
                    <h5>{{ summaries.approved_stats.single_day ||  0 }}</h5>
                    <p class="text-muted mb-0">
                        {{ $t('on_leave') }} <span class="default-font-color">({{ $t('single_day') }})</span>
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="default-base-color text-center rounded p-3">
                    <h5>{{ summaries.approved_stats.multi_day || 0 }}</h5>
                    <p class="text-muted mb-0">
                        {{ $t('on_leave') }} <span class="default-font-color">({{ $t('multi_day') }})</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="collapse" id="requestedLeaves">
            <div class="row mb-primary" v-if="this.$can('view_all_leaves')">
                <div class="col-md-6 col-lg-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.pending_request }}</h5>
                        <p class="text-muted mb-0">{{ $t('leave_request') }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.request_hours }}</h5>
                        <p class="text-muted mb-0">{{ $t('total_request_hours') }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.pending_stats.single_day || 0 }}</h5>
                        <p class="text-muted mb-0">{{ $t('single_day_request') }}</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.pending_stats.multi_day || 0}}</h5>
                        <p class="text-muted mb-0">{{ $t('multi_day_request') }}</p>
                    </div>
                </div>
            </div>

            <div class="row mb-primary" v-else>
                <div class="col-md-4">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.request_hours }}</h5>
                        <p class="text-muted mb-0">{{ $t('total_request_hours') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.pending_stats.single_day || 0 }}</h5>
                        <p class="text-muted mb-0">{{ $t('single_day_request') }}</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="default-base-color text-center rounded p-3">
                        <h5>{{ summaries.pending_stats.multi_day || 0}}</h5>
                        <p class="text-muted mb-0">{{ $t('multi_day_request') }}</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
export default {
    name: "LeaveCalendarSummary",
    props: {
      summaries: {
          type :  Object,
          required : true,
      }
    },
}
</script>
