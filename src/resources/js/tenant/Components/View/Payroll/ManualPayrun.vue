<template>

    <div class="card card-with-shadow border border-0 mb-primary">
        <div class="card-body">
            <app-form-wizard :tabs="tabs"
                             @selectedComponentName="selectedComponent"
                             @disabledTab="currentDisableTab"/>
        </div>
    </div>

</template>

<script>
import FormHelperMixins from "../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../common/Helper/AxiosHelper";
import moment from "moment";

export default {
    name: "ManualPayrun",
    mixins: [FormHelperMixins],
    props: {
        propsPayrunId: ''
    },
    data() {
        return {
            tabs: [
                {
                    "name": this.$t('payee'),
                    "component": "app-audience-wizard",
                    "props": null
                },
                {
                    "name": this.$t('payrun_period'),
                    "component": "app-payrun-period-wizard",
                    "props": null
                },
                {
                    "name": this.$t('payslip_note'),
                    "component": "app-manual-payrun-note-wizard",
                    "props": null
                },
                {
                    "name": this.$t('beneficiary_badge'),
                    "component": "app-beneficiary-badge-wizard",
                    "props": ''
                }
            ]
        }
    },
    created() {
        this.$store.dispatch('clearPayrunData');
        if (this.payrunId) return this.getAndSetPayrunDataToState()
    },
    computed: {
        payrunId() {
            return this.propsPayrunId;
        }
    },
    methods: {
        selectedComponent(value) {
            //console.log(value);
        },
        currentDisableTab(disableTab) {
            //console.log(disableTab);
        },
        getAndSetPayrunDataToState(){
            axiosGet(`${this.apiUrl.PAYRUNS}/${this.payrunId}`).then(({data}) => {
                let payrunData = JSON.parse(data?.data)
                let payrun = {
                    "id": data.id,
                    "consider_type": payrunData.consider_type,
                    "payrun_period": payrunData.period,
                    "consider_overtime": payrunData.consider_overtime,
                    "departments": payrunData.departments,
                    "users": payrunData.users,
                    "executable_month": moment(payrunData.time_range[0], 'DD-MM-YYYY').format('MMM'),
                    "executable_year": moment(payrunData.time_range[0]).format('YYYY'),
                    "start_date": payrunData.time_range[0],
                    "end_date": payrunData.time_range[1],
                    "allowances": this.setArrays(data.beneficiaries, 'allowances'),
                    "allowanceValues": this.setArrays(data.beneficiaries, 'allowanceValues'),
                    "allowancePercentages": this.setArrays(data.beneficiaries, 'allowancePercentages'),
                    "deductions": this.setArrays(data.beneficiaries, 'deductions'),
                    "deductionValues": this.setArrays(data.beneficiaries, 'deductionValues'),
                    "deductionPercentages": this.setArrays(data.beneficiaries, 'deductionPercentages'),
                    "note": payrunData.note,
                }

                this.$store.dispatch('setPayrunData', payrun);
            });
        },
        setArrays(beneficiaries, $type){
            let arr = []
            beneficiaries.map((item) => {
                if ($type === 'allowances' && item.beneficiary.type === 'allowance') arr.push(item.beneficiary.id)
                if ($type === 'allowanceValues' && item.beneficiary.type === 'allowance') arr.push(item.amount)
                if ($type === 'allowancePercentages' && item.beneficiary.type === 'allowance') arr.push(item.is_percentage)
                if ($type === 'deductions' && item.beneficiary.type === 'deduction') arr.push(item.beneficiary.id)
                if ($type === 'deductionValues' && item.beneficiary.type === 'deduction') arr.push(item.amount)
                if ($type === 'deductionPercentages' && item.beneficiary.type === 'deduction') arr.push(item.is_percentage)
            })
            return arr
        }
    }
}
</script>

<style scoped>

</style>