<template>
    <div class="row">
        <div class="col-md-4 col-lg-4 mb-primary">
            <div class="default-base-color text-center rounded p-3">
                <h5>{{ summaries.total }}</h5>
                <p class="text-muted mb-0">{{ this.$t('total_payslip') }}</p>
            </div>
        </div>
        <div class="col-md-4 col-lg-4 mb-primary">
            <div class="default-base-color text-center rounded p-3">
                <h5>{{ summaries.sent }}</h5>
                <p class="text-muted mb-0">{{ this.$t('payslip_sent') }}</p>
            </div>
        </div>
        <div class="col-md-4 col-lg-4 mb-primary">
            <div class="default-base-color text-center rounded p-3">
                <h5>{{ summaries.conflicted }}</h5>
                <p class="text-muted mb-0">{{ this.$t('conflicted_payslip')}}</p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "PayrollSummeryCards",
    props:{
        summaries:{}
    }
}
</script>

<style scoped>

</style>