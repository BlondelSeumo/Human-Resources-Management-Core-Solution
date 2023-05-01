<template>
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col-12">
                <form method="post" data-url="admin/app/settings" @submit.prevent="submitData" ref="form"
                      enctype="multipart/form-data">
                    <div>
                        <fieldset class="form-group mb-5">
                            <div class="row">
                                <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                                    {{ $t('app_info') }}
                                </legend>

                                <div class="col-12">
                                    <app-form-group
                                        :label="$fieldLabel('app')"
                                        page="page"
                                        v-model="settings.app_name"
                                        :placeholder="$placeholder('app', 'name')"
                                        :error-message="$errorMessage(errors, 'app_name')"
                                    />

                                    <app-form-group
                                        :label="$fieldLabel('app', 'logo')"
                                        page="page"
                                        type="custom-file-upload"
                                        v-if="imageUploaderBoot"
                                        v-model="image.app_logo"
                                        :error-message="$errorMessage(errors, 'app_logo')"
                                        :generate-file-url="false"
                                        :recommendation="$t('recommended_app_logo_size')"
                                        :file-label="$fieldLabel('app', 'logo')"
                                    />

                                    <app-form-group
                                        :label="$fieldLabel('app', 'icon')"
                                        page="page"
                                        type="custom-file-upload"
                                        v-if="imageUploaderBoot"
                                        v-model="image.app_icon"
                                        :error-message="$errorMessage(errors, 'app_icon')"
                                        :generate-file-url="false"
                                        :recommendation="$t('recommended_app_icon_size')"
                                        :file-label="$fieldLabel('app', 'icon')"
                                    />

                                    <app-form-group
                                        :label="$fieldLabel('app', 'banner')"
                                        page="page"
                                        type="custom-file-upload"
                                        v-if="imageUploaderBoot"
                                        v-model="image.app_banner"
                                        :error-message="$errorMessage(errors, 'app_banner')"
                                        :generate-file-url="false"
                                        :recommendation="$t('recommended_app_banner_size')"
                                        :file-label="$fieldLabel('app', 'icon')"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="form-group mb-5">
                            <div class="row">
                                <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                                    {{ $fieldLabel('language', 'settings') }}
                                </legend>

                                <div class="col-12">
                                    <app-form-group
                                        page="page"
                                        :label="$t('language')"
                                        type="select"
                                        v-model="settings.language"
                                        :list="languages"
                                        :error-message="$errorMessage(errors, 'language')"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="form-group mb-5">
                            <div class="row">
                                <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                                    {{ $fieldLabel('date_and_time', 'settings') }}
                                </legend>

                                <div class="col-12">
                                    <app-form-group
                                        page="page"
                                        :label="$fieldLabel('date', 'format')"
                                        type="select"
                                        v-model="settings.date_format"
                                        :list="dateFormat"
                                        :error-message="$errorMessage(errors, 'date_format')"
                                    />

                                    <div class="form-group row">
                                        <div class="col-lg-3 col-xl-3">
                                            <label class="text-left d-block mb-2 mb-lg-0">
                                                {{ $fieldLabel('time', 'format') }}
                                            </label>
                                        </div>
                                        <div class="col-lg-8 col-xl-8">
                                            <app-input type="radio-buttons"
                                                       v-model="settings.time_format"
                                                       :list="timeFormat"
                                                       :error-message="$errorMessage(errors, 'time_format')"/>
                                        </div>
                                    </div>

                                    <app-form-group
                                        page="page"
                                        :label="$fieldLabel('time', 'zone')"
                                        type="select"
                                        v-model="settings.time_zone"
                                        :list="timeZones"
                                        :error-message="$errorMessage(errors, 'time_zone')"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <fieldset class="form-group mb-5">
                            <div class="row">
                                <legend class="col-12 col-form-label text-primary pt-0 mb-3">
                                    {{ $fieldLabel('number', 'settings') }}
                                </legend>
                                <div class="col-12">
                                    <app-form-group
                                        page="page"
                                        :label="$fieldTitle('currency', 'symbol')"
                                        type="text"
                                        v-model="settings.currency_symbol"
                                        :placeholder="$placeholder('currency', 'symbol')"
                                        :error-message="$errorMessage(errors, 'currency_symbol')"
                                    />

                                    <div class="form-group row">
                                        <div class="col-lg-3 col-xl-3">
                                            <label class="text-left d-block mb-2 mb-lg-0">
                                                {{ $fieldLabel('decimal', 'separator') }}
                                            </label>
                                        </div>
                                        <div class="col-lg-8 col-xl-8">
                                            <app-input type="radio-buttons"
                                                       v-model="settings.decimal_separator"
                                                       @input="changeValue('decimal_separator')"
                                                       :list="decimalSeparator"
                                                       :error-message="$errorMessage(errors, 'decimal_separator')"/>
                                        </div>
                                    </div>
                                    <div class="form-group row">
                                        <div class="col-lg-3 col-xl-3">
                                            <label class="text-left d-block mb-2 mb-lg-0">
                                                {{ $fieldLabel('thousand', 'separator') }}
                                            </label>
                                        </div>
                                        <div class="col-lg-8 col-xl-8">
                                            <app-input type="radio-buttons"
                                                       v-model="settings.thousand_separator"
                                                       :list="thousandSeparator"
                                                       @input="changeValue('thousand_separator')"
                                                       :error-message="$errorMessage(errors, 'thousand_separator')"/>
                                        </div>
                                    </div>

                                    <app-form-group
                                        page="page"
                                        :label="$fieldTitle('number', 'decimal', false, 'of')"
                                        type="radio-buttons"
                                        v-model="settings.number_of_decimal"
                                        :list="numberOfDecimal"
                                        :error-message="$errorMessage(errors, 'number_of_decimal')"
                                    />
                                </div>
                            </div>
                        </fieldset>

                        <div class="form-group mt-5 mb-0">
                            <app-submit-button :loading="loading"
                                               @click="submitData"/>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import FormHelperMixins from "../../../../../Mixin/Global/FormHelperMixins";
import {mapActions, mapState} from 'vuex'
import {formDataAssigner} from '../../../../../Helper/Support/FormHelper'
import {urlGenerator} from "../../../../../Helper/AxiosHelper";

export default {
    name: "GeneralSetting",
    mixins: [FormHelperMixins],
    data() {
        return {
            settings: {},
            image: {
                app_logo: '',
                app_icon: '',
                app_banner: ''
            },
            imageUploaderBoot: false
        }
    },
    methods: {
        ...mapActions([
            'getConfig',
            'getSettings'
        ]),
        submitData() {
            this.loading = true;
            this.message = '';
            this.errors = {};
            let formData = formDataAssigner(new FormData, this.settings);
            Object.keys(this.image).forEach(key => {
                if (this.image[key] && this.image[key] instanceof File)
                    formData.append(key, this.image[key]);
            })
            return this.save(formData);
        },
        afterSuccess({data}) {
            this.toastAndReload(data.message);
            location.reload();
        },
        changeValue(type) {
            if (type === 'thousand_separator') {
                if (this.settings.thousand_separator === ',') {
                    this.settings.decimal_separator = '.'
                } else if (this.settings.thousand_separator === '.') {
                    this.settings.decimal_separator = ','
                }
            } else {
                this.settings.thousand_separator = this.settings.decimal_separator === ',' ? '.' : ','
            }
        }
    },
    computed: {
        ...mapState({
            languages: state => state.support.languages,
            generalSettings: state => state.settings.settings
        }),
        dateFormat() {
            return this.$store.getters.getFormattedConfig('date_format')
        },
        timeFormat() {
            return this.$store.getters.getFormattedConfig('time_format')
        },
        currencyPosition() {
            return this.$store.getters.getFormattedConfig('currency_position')
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
        timeZones() {
            return this.$store.getters.getFormattedConfig('time_zones')
        },
    },
    created() {
        this.scrollToTop();
        this.getConfig();
        this.getSettings();
    },
    watch: {
        generalSettings: {
            handler: function (settings) {
                if (!Object.keys(this.settings).length) {
                    this.settings = settings;
                    this.image = {
                        app_logo: settings.app_logo ? urlGenerator(settings.app_logo) : urlGenerator('/images/core.png'),
                        app_icon: settings.app_icon ? urlGenerator(settings.app_icon) : urlGenerator('/images/core.png'),
                        app_banner: settings.app_banner ? urlGenerator(settings.app_banner) : urlGenerator('/images/default_banner.png')
                    }
                    this.imageUploaderBoot = true;
                    delete this.settings.app_logo;
                    delete this.settings.app_icon;
                    delete this.settings.app_banner;
                }
            },
            deep: true
        }
    }
}
</script>

<style scoped>
label {
    margin-top: 12px;
}
</style>
