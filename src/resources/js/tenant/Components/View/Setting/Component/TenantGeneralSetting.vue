<template>
    <div>
        <app-overlay-loader v-if="preloader"/>
        <form v-else
              ref="form"
              method="post"
              :data-url="apiUrl.TENANT_GENERAL_SETTING_URL"
              @submit.prevent="submitData"
              enctype="multipart/form-data"
              class="mb-0"
              :class="{'loading-opacity': preloader}">

            <!-- Company Info -->
            <fieldset class="form-group mb-5">
                <div class="row">
                    <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                        {{ $t('company_info') }}
                    </legend>
                    <div class="col-md-12">
                        <div class="form-group row align-items-center">
                            <label for="appSettingsCompanyName" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('company_name') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsCompanyName"
                                    type="text"
                                    v-model="settings.tenant_name"
                                    :placeholder="$placeholder('company','name')"
                                    :required="true"
                                    :error-message="$errorMessage(errors, 'tenant_name')"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="appSettingsCompanyLogo" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('company_logo') }}<br>
                                <small class="text-muted font-italic">
                                    {{ $t('recommended_company_logo_size') }}
                                </small>
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsCompanyLogo"
                                    v-if="imageUploaderBoot"
                                    type="custom-file-upload"
                                    :generate-file-url="false"
                                    v-model="image.tenant_logo"
                                    :label="$t('change_logo')"
                                    :error-message="$errorMessage(errors, 'tenant_logo')"
                                />
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="appSettingsCompanyIcon" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('company_icon') }}<br>
                                <small class="text-muted font-italic">
                                    {{ $t('recommended_company_icon_size') }}
                                </small>
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsCompanyIcon"
                                    v-if="imageUploaderBoot"
                                    type="custom-file-upload"
                                    :generate-file-url="false"
                                    v-model="image.tenant_icon"
                                    :label="$t('change_icon')"
                                    :error-message="$errorMessage(errors, 'tenant_icon')"
                                />
                            </div>
                        </div>
                        <div class="form-group row" v-if="tenant.is_single">
                            <label for="appSettingsCompanyBanner" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('company_banner') }}<br>
                                <small class="text-muted font-italic">
                                    {{ $t('recommended_company_banner_size') }}
                                </small>
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsCompanyBanner"
                                    v-if="imageUploaderBoot"
                                    type="custom-file-upload"
                                    :generate-file-url="false"
                                    v-model="image.tenant_banner"
                                    :label="$t('change_banner')"
                                    :error-message="$errorMessage(errors, 'tenant_banner')"
                                />
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label for="appSettingsLanguage" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('language') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsLanguage"
                                    type="select"
                                    v-model="settings.language"
                                    :list="languages"
                                    :required="true"
                                    :error-message="$errorMessage(errors, 'language')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Address Details -->
            <fieldset class="form-group mb-5">
                <div class="row">
                    <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                        {{ $t('address_details') }}
                    </legend>
                    <div class="col-md-12">
                        <app-form-group
                            type="select"
                            page="page"
                            :label="$t('country')"
                            :chooseLabel="$t('country')"
                            v-model="settings.country"
                            :list="countries"
                            :error-message="$errorMessage(errors, 'country')"
                        />
                        <app-form-group
                            page="page"
                            :label="$t('area')"
                            :placeholder="$placeholder('area')"
                            v-model="settings.area"
                            :error-message="$errorMessage(errors, 'area')"
                        />
                        <app-form-group
                            page="page"
                            :label="$t('city')"
                            :placeholder="$placeholder('city')"
                            v-model="settings.city"
                            :error-message="$errorMessage(errors, 'city')"
                        />
                        <app-form-group
                            page="page"
                            :label="$t('state')"
                            :placeholder="$placeholder('state')"
                            v-model="settings.state"
                            :error-message="$errorMessage(errors, 'state')"
                        />
                        <app-form-group
                            page="page"
                            :label="$t('zip_code')"
                            :placeholder="$placeholder('zip_code')"
                            v-model="settings.zip_code"
                            :error-message="$errorMessage(errors, 'zip_code')"
                        />
                        <app-form-group
                            page="page"
                            type="textarea"
                            :label="$t('address')"
                            :placeholder="$placeholder('address')"
                            v-model="settings.address"
                            :error-message="$errorMessage(errors, 'address')"
                        />
                    </div>
                </div>
            </fieldset>

            <!-- Date & Time Setting -->
            <fieldset class="form-group mb-5">
                <div class="row">
                    <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                        {{ $t('date_and_time_setting') }}
                    </legend>
                    <div class="col-md-12">
                        <div class="form-group row align-items-center">
                            <label for="appSettingsDateFormat" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('date_format') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsDateFormat"
                                    type="select"
                                    v-model="settings.date_format"
                                    :list="dateFormat"
                                    :error-message="$errorMessage(errors, 'date_format')"
                                />
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('time_format') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    type="radio-buttons"
                                    v-model="settings.time_format"
                                    :list="timeFormat"
                                    :error-message="$errorMessage(errors, 'time_format')"
                                />
                            </div>
                        </div>
                        <div class="form-group row align-items-center" v-if="settings.time_zone != 'UTC'">
                            <label for="appSettingsTimeZone" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('time_zone') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsTimeZone"
                                    type="select"
                                    v-model="settings.time_zone"
                                    :list="timeZones"
                                    :error-message="$errorMessage(errors, 'time_zone')"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <!-- Currency Settings -->
            <fieldset class="form-group mb-5">
                <div class="row">
                    <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                        {{ $t('currency_setting') }}
                    </legend>
                    <div class="col-md-12">
                        <div class="form-group row align-items-center">
                            <label for="appSettingsCurrencySymbol" class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('currency_symbol') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    id="appSettingsCurrencySymbol"
                                    type="text"
                                    v-model="settings.currency_symbol"
                                    :required="true"
                                    :placeholder="$placeholder('currency','symbol')"
                                    :error-message="$errorMessage(errors, 'currency_symbol')"
                                />
                                <small class="text-muted font-italic">
                                    {{ $t('currency_symbol_note') }}
                                </small>
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('decimal_separator') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    type="radio-buttons"
                                    v-model="settings.decimal_separator"
                                    @input="changeValue('decimal_separator')"
                                    :list="decimalSeparator"
                                    :required="true"
                                    :error-message="$errorMessage(errors, 'decimal_separator')"
                                />
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('thousand_separator') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    type="radio-buttons"
                                    v-model="settings.thousand_separator"
                                    @input="changeValue('thousand_separator')"
                                    :list="thousandSeparator"
                                    :required="true"
                                    :error-message="$errorMessage(errors, 'thousand_separator')"
                                />
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('number_of_decimal') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    type="radio-buttons"
                                    v-model="settings.number_of_decimal"
                                    :list="numberOfDecimal"
                                    :required="true"
                                    :error-message="$errorMessage(errors, 'number_of_decimal')"
                                />
                            </div>
                        </div>
                        <div class="form-group row align-items-center">
                            <label class="col-lg-3 col-xl-3 mb-lg-0">
                                {{ $t('currency_position') }}
                            </label>
                            <div class="col-lg-8 col-xl-8">
                                <app-input
                                    type="radio-buttons"
                                    v-model="settings.currency_position"
                                    :list="currencyPosition"
                                    :required="true"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>

            <div class="mt-5">
                <app-submit-button
                    :loading="loading"
                    @click="submitData"
                />
            </div>
        </form>
    </div>
</template>

<script>
    import { mapState, mapGetters, mapActions } from 'vuex';
    import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
    import {addChooseInSelectArray, formDataAssigner} from '../../../../../common/Helper/Support/FormHelper'
    import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";
    import countries from "../../Employee/Helper/countries";

    export default {
        name: "TenantGeneralSetting",
        mixins: [FormHelperMixins],
        data() {
            return {
                settings: {},
                image: {
                    tenant_logo: '',
                    tenant_icon: '',
                    tenant_banner: ''
                },
                imageUploaderBoot: false
            }
        },
        methods: {
            beforeSubmit() {
                this.preloader = true;
                this.loading = true;
                this.message = '';
                this.errors = {};
            },

            submitData() {
                let formData = formDataAssigner(new FormData, this.settings);
                Object.keys(this.image).forEach(key => {
                    if (this.image[key] && this.image[key] instanceof File) {
                        formData.append(key, this.image[key]);
                    }
                })
                return this.save(formData);
            },

            afterSuccess({data}) {
                this.toastAndReload(data.message);
                location.reload();
            },

            ...mapActions([
                'getConfig',
                'getTenantSettings'
            ]),

            changeValue(type) {
                if (type === 'thousand_separator') {
                    if (this.settings.thousand_separator === ',') {
                        this.settings.decimal_separator = '.'
                    }
                    else if (this.settings.thousand_separator === '.') {
                        this.settings.decimal_separator = ','
                    }
                }
                else {
                    this.settings.thousand_separator = this.settings.decimal_separator === ',' ? '.' : ','
                }
            },

        },
        computed: {
            ...mapState({
                languages: state => state.support.languages,
                generalSettings: state => state.tenant_settings.tenantSettings
            }),

            dateFormat() {
                return this.$store.getters.getFormattedConfig('date_format')
            },

            timeFormat() {
                return this.$store.getters.getFormattedConfig('time_format')
            },

            timeZones() {
                return this.$store.getters.getFormattedConfig('time_zones')
            },

            decimalSeparator() {
                return this.$store.getters.getFormattedConfig('decimal_separator')
            },

            thousandSeparator() {
                return this.$store.getters.getFormattedConfig('thousand_separator')
            },

            numberOfDecimal() {
                return this.$store.getters.getFormattedConfig('number_of_decimal')
            },

            currencyPosition() {
                return this.$store.getters.getFormattedConfig('currency_position')
            },
            tenant() {
                return window.tenant;
            },
            countries() {
                return addChooseInSelectArray(countries, 'value', this.$t('country'))
            }
        },
        created() {
            this.getConfig();
            this.getTenantSettings();
            this.preloader = true;
        },
        watch: {
            generalSettings: {
                handler: function (settings) {
                    if (!Object.keys(this.settings).length) {
                        this.settings = settings;
                        this.image = {
                            tenant_logo: settings.tenant_logo ? urlGenerator(settings.tenant_logo) : urlGenerator('/images/logo.png'),
                            tenant_icon: settings.tenant_icon ? urlGenerator(settings.tenant_icon) : urlGenerator('/images/icon.png'),
                            tenant_banner: settings.tenant_banner ? urlGenerator(settings.tenant_banner) : urlGenerator('/images/default_banner.jpg')
                        }
                        this.preloader = false;
                        this.imageUploaderBoot = true;
                        delete this.settings.tenant_logo;
                        delete this.settings.tenant_icon;
                        delete this.settings.tenant_banner;
                    }
                },
                deep: true
            }
        },

    }
</script>
