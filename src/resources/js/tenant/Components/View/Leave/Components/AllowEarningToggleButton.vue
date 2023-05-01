<template>
    <div>
        <app-input type="switch"
                   v-model="switchValue"
                   @input="changeEarning"
        />
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import CoreLibrary from "../../../../../core/helpers/CoreLibrary";
import {axiosPatch} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "AllowEarningToggleButton",
    extends: CoreLibrary,
    mixins: [FormHelperMixins],
    props: {
        value: {},
        rowData: {}
    },
    data() {
        return {
            switchValue: parseInt(this.value),
        }
    },
    methods: {
        changeEarning(event) {
            event ? this.switchValue = 1 : this.switchValue = 0;
            this.rowData.is_earning_enabled = this.switchValue;
            axiosPatch(`${this.apiUrl.LEAVE_TYPES}/${this.rowData.id}`, this.rowData)
                .then((res) => {
                    this.$toastr.s(res.data.message)
                }).catch((error)=>{
                this.$toastr.e(error.response.data.message)
                this.switchValue = !this.switchValue;
            })
        }
    }
}
</script>

