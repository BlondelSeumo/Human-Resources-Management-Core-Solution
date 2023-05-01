<template>
    <app-overlay-loader v-if="preloader"/>
    <form v-else method="post" ref="form" :data-url="this.apiUrl.GEOLOCATION_SETTINGS">

        <app-form-group
            :label="$t('chose_geolocation_service')"
            v-model="formData.service_name"
            page="page"
            type="radio"
            radio-checkbox-wrapper="row"
            label-alignment="align-item-start"
            :error-message="$errorMessage(errors, 'service_name')"
            :list="[{id:'google_map', value: 'Google Map'},{id:'mapbox', value: 'Mapbox'}, {id:'ip_geolocation', value: 'Ip Geolocation'}]"
        />

        <app-note
            v-if="formData.service_name === 'google_map'"
            class="mb-primary"
            :title="$t('note')"
            :notes="[$t('about_google_map_geo_location_service', {
                      google_map: `<a href='https://www.google.com/maps' target='_blank'>${$t('google_map')}</a>`,
                      website: `<a href='https://console.cloud.google.com' target='_blank'>${$t('website')}</a>`,
                      })]"
            content-type="html"
        />

        <app-note
            v-if="formData.service_name === 'ip_geolocation'"
            class="mb-primary"
            :title="$t('note')"
            :notes="[$t('about_geo_location_api', {
                      ip_geolocation: `<a href='https://ipgeolocation.io/' target='_blank'>${$t('ip_geolocation')}</a>`,
                      website: `<a href='https://ipgeolocation.io/signup.html' target='_blank'>${$t('website')}</a>`,
                      })]"
            content-type="html"
        />
        <app-note
            v-if="formData.service_name === 'mapbox'"
            class="mb-primary"
            :title="$t('note')"
            :notes="[$t('about_mapbox_geo_location_service', {
                      mapbox: `<a href='https://www.mapbox.com/' target='_blank'>Mapbox</a>`,
                      website: `<a href='https://account.mapbox.com/auth/signup/' target='_blank'>${$t('website')}</a>`,
                      })]"
            content-type="html"
        />
<!--        <app-form-group-->
<!--            :disabled="true"-->
<!--            page="page"-->
<!--            :label="$t('ip_endpoint')"-->
<!--            type="text"-->
<!--            v-model="formData.ip_endpoint"-->
<!--            :error-message="$errorMessage(errors, 'ip_endpoint')"-->
<!--        />-->
<!--        <app-form-group-->
<!--            :disabled="true"-->
<!--            page="page"-->
<!--            :label="$t('api_endpoint')"-->
<!--            type="text"-->
<!--            v-model="formData.api_endpoint"-->
<!--            :error-message="$errorMessage(errors, 'api_endpoint')"-->
<!--        />-->

        <app-form-group
            page="page"
            :label="$t('api_key')"
            type="text"
            v-model="formData.api_key"
            :error-message="$errorMessage(errors, 'api_key')"
        />

        <div class="row">
            <div class="col-12">
                <app-submit-button :label="$t('save')" :loading="loading" @click="submitData"/>
            </div>
        </div>
    </form>
</template>

<script>
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "GeolocationApiSettings",
    mixins: [FormHelperMixins],
    methods: {
        afterSuccess(response) {
            this.toastAndReload(response.data.message)
        },
        getGeolocationSettings() {
            this.preloader = true;
            axiosGet(this.apiUrl.GEOLOCATION_SETTINGS).then(({data}) => {
                this.formData = data.api_key || data.service_name ? data : {};
            }).finally(() => this.preloader = false)
        }
    },
    mounted() {
        this.getGeolocationSettings();
    }
}
</script>
