<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else @submit.prevent="submitData" ref="form" :data-url="`${apiUrl.PAYROLL_SETTINGS}/payslip`">


        <app-form-group
            type="radio"
            page="page"
            :label="$t('payslip_logo')"
            radio-checkbox-name="payslip_logo"
            v-model="payslipLogo"
            :list="[
                {id: 'default', value:this.$t('use_default_logo')},
                {id: 'customize', value:this.$t('customize')},
             ]"
        />

        <div v-if="payslipLogo === 'customize'" class="mt-primary">
            <app-form-group
                :label="$fieldLabel('payslip', 'logo')"
                page="page"
                type="custom-file-upload"
                v-model="image.logo"
                :error-message="$errorMessage(errors, 'logo')"
                :generate-file-url="false"
                :recommendation="this.$t('recommended_company_logo_size')"
                :file-label="$fieldLabel('payslip', 'logo')"
            />
        </div>

        <app-form-group
            type="radio"
            page="page"
            :label="$t('payslip_title')"
            radio-checkbox-name="payslip_title"
            v-model="payslipTitle"
            :list="[
                {id: 'default', value:this.$t('use_default_title')},
                {id: 'customize', value:this.$t('customize')},
             ]"
        />

        <div v-if="payslipTitle === 'customize'" class="mt-primary">
            <app-form-group
                page="page"
                :label="''"
                type="text"
                v-model="formData.title"
                :placeholder="$placeholder('title', '')"
                :error-message="$errorMessage(errors, 'title')"
            />
        </div>

        <app-form-group
            type="radio"
            page="page"
            :label="$t('payslip_address')"
            radio-checkbox-name="payslip_address"
            v-model="payslipAddress"
            :list="[
                {id: 'default', value:this.$t('use_default_address')},
                {id: 'customize', value:this.$t('customize')},
             ]"
        />
        <!-- Address Details -->
        <div v-if="payslipAddress === 'customize'" class="mt-primary">
            <app-form-group
                page="page"
                type="textarea"
                :label="''"
                :placeholder="$placeholder('address')"
                v-model="formData.address"
                :error-message="$errorMessage(errors, 'address')"
            />
        </div>

        <div class="row mt-4">
            <div class="col-3"></div>
            <div class="col-9">
                <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
            </div>
        </div>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet, urlGenerator} from "../../../../../../common/Helper/AxiosHelper";
import {formDataAssigner} from "../../../../../../common/Helper/Support/FormHelper";

export default {
    name: "PayslipSetting",
    mixins: [FormHelperMixins],
    data() {
        return {
            image: {},
            payslipAddress: 'default',
            payslipLogo: 'default',
            payslipTitle: 'default',
        }
    },
    computed: {},
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getSetting() {
            this.preloader = true;
            axiosGet(`${this.apiUrl.PAYROLL_SETTINGS}/payslip`).then(({data}) => {
                this.formData = data;
                this.payslipLogo = data.logo ? 'customize' : 'default';
                this.payslipTitle = data.title ? 'customize' : 'default';
                this.payslipAddress = data.address ? 'customize' : 'default';
            }).finally(() => {
                this.preloader = false;
            })
        },
        submitData() {
            if (this.payslipTitle === 'default') {
                this.formData.title = ''
            }
            if (this.payslipLogo === 'default') {
                this.formData.logo = ''
            }
            this.formData.payslipTitle = this.payslipTitle;
            this.formData.payslipLogo = this.payslipLogo;
            this.formData.payslipAddress = this.payslipAddress;
            let formData = formDataAssigner(new FormData, this.formData);

            if (this.image.logo && this.image.logo instanceof File && this.payslipLogo === 'customize') {
                formData.append('logo', this.image.logo);
            }

            this.setBasicFormActionData()
                .save(formData);
        },
    },
    mounted() {
        this.getSetting();
    },
    watch: {
        payslipLogo: {
            handler: function (payslipLogo) {
                this.image = {
                    logo: this.formData.logo ? urlGenerator(this.formData.logo) : urlGenerator('/images/core.png'),
                }
            },
            deep: true
        }
    }
}
</script>
