<template>
    <modal id="payslip-edit-modal"
           size="large"
           v-model="showModal"
           :title="this.$t('conflicted_payslip_for_employee')"
           :cancel-btn-label="$t('close')"
           :hide-submit-button="true"
           @=""
           :preloader="preloader">

        <div class="d-flex justify-content-between mb-primary">
            <div class="">
                <app-user-media
                    :row-data="payslip.user"
                    :value="payslip.user.profile_picture"
                />
            </div>
            <div>

            </div>
        </div>

        <conflicted-payslip-row
            v-if="conflictedPayslips.length"
            :payrunId="payslip.payrun.id"
            :payslips="conflictedPayslips"
            @payslipDeleted="getConflictedPayslips"
        />

        <app-empty-data-block v-else :message="$t('no_payslip_found')"/>


    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {PAYRUN} from "../../../../Config/ApiUrl";
import ConflictedPayslipRow from "./ConflictedPayslipRow";

export default {
    name: "PayslipConflictModal",
    components: {ConflictedPayslipRow},
    mixins: [ModalMixin],
    props: {
        payslip: {}
    },
    data() {
        return {
            conflictedPayslips: [],
        }
    },
    mounted() {
        this.getConflictedPayslips();
    },
    methods: {
        getConflictedPayslips() {
            this.preloader = true;
            axiosGet(`${PAYRUN}/${this.payslip.payrun.id}/user/${this.payslip.user.id}/conflicted`).then(({data}) => {
                this.conflictedPayslips = data;
            }).finally(() => {
                this.preloader = false;
            })
        }
    }
}
</script>

<style scoped>

</style>