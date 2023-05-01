<template>
    <app-pre-loader v-if="loading"/>
    <div v-else>
        <div v-if="payslips.length > 1" class="mb-primary">
            <h6 class="text-warning">{{ $t('no_worries') }}</h6>
            <p>{{ $t('payslip_confliction_message', {'count': payslips.length}) }}</p>
        </div>
        <div v-else class="mb-primary">
            <h6 class="text-success">{{ $t('no_conflicts') }}</h6>
            <p>{{ $t('this_payslip_has_no_confliction_with_other_payslips') }}</p>
        </div>

        <div class="d-flex justify-content-between mb-primary">
            <div class="d-flex align-items-center">
                <div
                    class="width-55 height-55 default-base-color rounded-circle d-inline-flex flex-shrink-0 align-items-center justify-content-center">
                    <app-icon name="file-text" class="size-24 primary-text-color"/>
                </div>
                <div class="ml-2">
                    <h6 class="mb-0">
                        {{ getDateDifferenceString(currentPayslip.start_date, currentPayslip.end_date) }},
                        <small>{{ $t('payrun_id') }}: <span class="text-info">{{
                                currentPayslip.payrun.uid
                            }}</span></small>
                    </h6>
                    <small>
                        {{ ucFirst(JSON.parse(currentPayslip.payrun.data).period) }} {{
                            JSON.parse(currentPayslip.payrun.data).consider_type !== 'none' ? `- ${JSON.parse(currentPayslip.payrun.data).consider_type.split('_').join(' ')} base` : ''
                        }}, {{ `${$t('created_at')} : ${dateTimeFormat(currentPayslip.created_at)}` }}
                    </small><br>
                    <small>
                        {{
                            `${$t('followed_by', {'setting': $t(currentPayslip.payrun.followed)})}, ${ucFirst(JSON.parse(currentPayslip.payrun.data).type)}`
                        }}
                    </small>
                </div>
            </div>
            <div>
                <a :href="urlGenerator(`${PAYSLIP}/${currentPayslip.id}/pdf`)" target="_blank"
                   class="btn btn-link text-primary">
                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                    {{ $t('view_pdf') }}
                </a>
                <a href="#" @click.prevent="deletePayslip(currentPayslip)" class="btn btn-link text-danger">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    {{ $t('drop') }}
                </a>
            </div>
        </div>

        <div v-if="conflictedPayslips.length" class="border-bottom mb-3">
            <h6 class="text-secondary">{{ $t('similar_payslips') }}:</h6>
        </div>

        <div v-for="payslip in conflictedPayslips" class="d-flex justify-content-between mb-primary">
            <div class="d-flex align-items-center">
                <div
                    class="width-55 height-55 default-base-color rounded-circle d-inline-flex flex-shrink-0 align-items-center justify-content-center">
                    <app-icon name="file-text" class="size-24 primary-text-color"/>
                </div>
                <div class="ml-2">
                    <h6 class="mb-0">
                        {{ getDateDifferenceString(payslip.start_date, payslip.end_date) }},
                        <small>{{ $t('payrun_id') }}: <span class="text-info">{{ payslip.payrun.uid }}</span></small>
                    </h6>
                    <small>
                        {{ ucFirst(JSON.parse(payslip.payrun.data).period) }} {{
                            JSON.parse(payslip.payrun.data).consider_type !== 'none' ? `- ${JSON.parse(payslip.payrun.data).consider_type.split('_').join(' ')} base` : ''
                        }}, {{ `${$t('created_at')} : ${dateTimeFormat(payslip.created_at)}` }}
                    </small><br>
                    <small>
                        {{
                            `${$t('followed_by', {'setting': $t(payslip.payrun.followed)})}, ${ucFirst(JSON.parse(payslip.payrun.data).type)}`
                        }}
                    </small>
                </div>
            </div>
            <div>
                <a :href="urlGenerator(`${PAYSLIP}/${payslip.id}/pdf`)" target="_blank" class="btn btn-link text-primary" >
                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                    {{ $t('view_pdf') }}
                </a>
                <a href="#" @click.prevent="deletePayslip(payslip)" class="btn btn-link text-danger">
                    <i class="fa fa-times-circle" aria-hidden="true"></i>
                    {{ $t('drop') }}
                </a>
            </div>
        </div>
    </div>
</template>

<script>
import {axiosGet, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {PAYSLIP} from "../../../../Config/ApiUrl";
import {
    dateTimeFormat,
    getDateDifferenceString
} from "../../../../../common/Helper/Support/DateTimeHelper";
import {ucFirst} from "../../../../../common/Helper/Support/TextHelper";

export default {
    name: "ConflictedPayslipRow",
    props: {
        payslips: {
            require: true,
            type: Array
        },
        payrunId: {}
    },
    data() {
        return {
            ucFirst,
            dateTimeFormat,
            getDateDifferenceString,
            urlGenerator,
            loading: false,
            PAYSLIP,
        }
    },
    methods: {
        deletePayslip(payslip) {
            this.loading = true;
            axiosGet(`${PAYSLIP}/${payslip.id}/delete`).then((response) => {
                this.$toastr.s(response.data.message);
                this.$emit('payslipDeleted');
            }).catch(({response}) => {
                this.$toastr.e('', response.data.message);
            }).finally(() => {
                this.loading = false;
            })
        },
    },
    computed: {
        currentPayslip() {
            return this.payslips.find((payslip) => payslip.payrun.id == this.payrunId);
        },
        conflictedPayslips() {
            return this.payslips.filter((payslip) => payslip.payrun.id != this.payrunId);
        }
    }
}
</script>

<style scoped>

</style>