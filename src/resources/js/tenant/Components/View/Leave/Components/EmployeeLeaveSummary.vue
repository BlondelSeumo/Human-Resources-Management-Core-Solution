<template>
  <div class="row">
    <div class="col-md-6 col-lg-4 mb-primary">
      <div class="default-base-color text-center rounded p-3">
        <h5>{{ numberFormatter(summaries.spent) }}</h5>
        <p class="text-muted mb-0">{{ $t('leave_taken') }} ({{ $t('days') }})</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-primary">
      <div class="default-base-color text-center rounded p-3">
        <h5>{{ numberFormatter(summaries.upcoming) }}</h5>
        <p class="text-muted mb-0">{{ $t('upcoming_leave') }} ({{ $t('days') }})</p>
      </div>
    </div>
    <div class="col-md-6 col-lg-4 mb-primary">
      <div class="default-base-color text-center rounded p-3">
        <h5>{{ numberFormatter(summaries.pending) }}</h5>
        <p class="text-muted mb-0">{{ $t('pending_request') }}</p>
      </div>
    </div>
<!--    <div class="col-md-6 col-lg-3 mb-primary">-->
<!--      <div class="default-base-color text-center rounded p-3">-->
<!--        <h5>{{ numberFormatter(summaries.available) }}</h5>-->
<!--        <p class="text-muted mb-0">-->
<!--          {{ $t('total_leave_availability') }}-->
<!--        </p>-->
<!--      </div>-->
<!--    </div>-->
  </div>
</template>

<script>
import {numberFormatter} from "../../../../../common/Helper/Support/SettingsHelper";

export default {
  name: "EmployeeLeaveSummary",
  props: {
    summaries: {
      required: true,
      type: Object
    }
  },
    data(){
      return{
          numberFormatter
      }
    }
}
</script>