<template>
    <div class="row">
        <div class="col-sm-6 pb-2">
            <div class="d-flex justify-content-between border-bottom py-1 mb-1">
                <span class="font-size-90">{{ $t('earning')}}</span>
                <span class="font-size-90">
                    {{ !countTotal('earning').count && !countTotal('earning').percentage ? `${formatCurrency(0)}` :
                    `${countTotal('earning').percentage || ''} ${countTotal('earning').count ?
                        `${(countTotal('earning').percentage ? '+' : '')} ${countTotal('earning').count}` : ''}` }}
                </span>
            </div>
            <template v-for="earning in earnings">
                <div class="d-flex justify-content-between">
                    <span class="font-size-80">{{ earning.beneficiary.name }}</span>
                    <span class="font-size-80">
                        {{ parseInt(earning.is_percentage) > 0 ? `${earning.amount} %` :
                        `${formatCurrency(earning.amount)}` }}
                    </span>
                </div>
            </template>
        </div>
        <div class="col-sm-6">
            <div class="d-flex justify-content-between border-bottom py-1 mb-1">
                <span class="font-size-90">{{ $t('deduction') }}</span>
                <span class="font-size-90">
                    {{
                        !countTotal('deduction').count && !countTotal('deduction').percentage ? `${formatCurrency(0)}` :
                            `${countTotal('deduction').percentage || ''} ${countTotal('deduction').count ?
                                `${(countTotal('deduction').percentage ? '+' : '')} ${countTotal('deduction').count}` : ''}`
                    }}
                </span>
            </div>
            <template v-for="deduction in deductions">
                <div class="d-flex justify-content-between">
                    <span class="font-size-80">{{ deduction.beneficiary.name }}</span>
                    <span class="font-size-80">
                        {{parseInt(deduction.is_percentage) > 0 ? `${deduction.amount} %` :
                        `${ formatCurrency(deduction.amount) }` }}
                    </span>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import {formatCurrency} from "../../../../../common/Helper/Support/SettingsHelper";

export default {
    name: "BeneficiaryBadgeShow",
    props: {
        earnings: {},
        deductions: {}
    },
    data() {
        return {
            formatCurrency
        }
    },
    methods: {
        countTotal(type){
            let items = type === 'earning' ? this.earnings : this.deductions
            let count = 0
            let percent = 0
            items.forEach((item) => {
                parseInt(item.is_percentage) > 0 ? percent += parseInt(item.amount) : count += parseInt(item.amount)
            })
            return {
                'count': count > 0 ? formatCurrency(count) : 0,
                'percentage': percent > 0 ? `${percent} %` : null
            };
        },
    },
}
</script>

<style scoped>

</style>