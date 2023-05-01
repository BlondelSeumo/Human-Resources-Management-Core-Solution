<template>
    <div>
        <modal id="punch-in-modal"
               size="large"
               v-model="showModal"
               @submit="submit"
               :preloader="preloader"
               :loading="loading || geoLoader"
               :title="!punch ? $t('punch_in') : $t('punch_out')"
               :submit-btn-class="!punch ? 'btn-success' : 'btn-warning'"
               :btn-label="!punch ? $t('punch_in') : $t('punch_out')">
            <div class="d-flex justify-content-between">
                <div v-if="punch" class="d-flex align-items-center mb-4">
                    <div class="icon-box mr-2">
                        <app-icon name="log-out" class="text-warning"/>
                    </div>
                    <div>
                        <p class="text-secondary mb-1">{{ $t('punch_out_date_and_time') }}</p>
                        <h5 class="mb-1">{{ nowDateTime }}</h5>
                        <span class="">{{ nowDate }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center mb-4">
                    <div class="icon-box mr-2">
                        <app-icon name="log-in" class="text-success"/>
                    </div>
                    <div>
                        <p class="text-secondary mb-1">
                            {{ punch ? $t('punched_in_date_and_time') : $t('punch_in_date_and_time') }}</p>
                        <h5 class="mb-1">{{ punchInDateTime ? punchInDateTime : nowDateTime }}</h5>
                        <span class="">{{ punchInDate ? punchInDate : nowDate }}</span>

                        <div class="d-flex align-items-center justify-content-between">
                            <span class="mr-3">{{$t('work_from_home')}}</span>
                            <app-input
                                type="switch"
                                class="mt-2"
                                :disabled="punch"
                                v-model="geoLocation.work_from_home"
                            />
                        </div>

                    </div>
                </div>
            </div>

            <div id='map' style='width: 100%; height: 300px;'></div>
            <div class="d-flex justify-content-center mb-primary">
                <app-overlay-loader v-if="geoLoader"/>
                <template v-else>
                    <div class="w-100 py-3 px-2"
                         :class="`${geoLocation.error ? 'alert alert-warning text-dark' : 'note note-warning'}`">
                        <p class="m-0" v-if="geoLocation.error">{{ geoLocation.error }}</p>
                        <p class="m-0" v-if="geoLocation.ip && !geoLocation.error"><label
                            class="font-weight-bold">IP:</label>
                            {{ geoLocation.ip }}</p>
                        <p class="m-0 font-xl" v-if="geoLocation.location"><label
                            class="font-weight-bold">Location:</label> {{ geoLocation.location }}</p>
                        <p class="m-0" v-if="geoLocation.city"><label class="font-weight-bold">City:</label>
                            {{ geoLocation.city }}</p>
                        <p class="m-0" v-if="geoLocation.state_prov"><label class="font-weight-bold">State:</label>
                            {{ geoLocation.state_prov }}</p>
                        <p class="m-0" v-if="geoLocation.country_name"><label
                            class="font-weight-bold mb-0">Country:</label> {{ geoLocation.country_name }}</p>
                    </div>
                </template>
            </div>
            <form class="mb-0"
                  ref="form"
            >
                <label for="description">{{ !punch ? $t('punch_in_note') : $t('punch_out_note') }}</label>
                <app-input
                    id="description"
                    type="textarea"
                    v-model="formData.note"
                    :text-area-rows="4"
                    :placeholder="!punch ? $t('punch_in_note_here') : $t('punch_out_note_here')"
                />
            </form>
        </modal>
    </div>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {TENANT_BASE_URL} from "../../../../../common/Config/UrlHelper";
import {axiosGet} from "../../../../../common/Helper/AxiosHelper";
import {
    formatDateForServer,
    formatDateToLocal,
    calenderTime, onlyTime
} from "../../../../../common/Helper/Support/DateTimeHelper";
import {ucFirst} from "../../../../../common/Helper/Support/TextHelper";
import mapboxgl from 'mapbox-gl';
import { Loader } from '@googlemaps/js-api-loader';

export default {
    name: "PunchInOutModal",
    props: {
        punch: {}
    },
    data() {
        return {
            formData: {},
            nowDate: '',
            nowDateTime: '',
            punchInDate: '',
            punchInDateTime: '',
            url: `${TENANT_BASE_URL}employees`,
            preloader: true,
            geoLoader: true,
            geoLocation: {location:"", work_from_home:false},
            map: null,
            interval: null,
            geoSettings: {},
            center: {
                lat: 0,
                lng: 0
            },
        }
    },
    mixins: [ModalMixin, FormHelperMixins],
    mounted() {
        this.nowTimes();
        this.interval = setInterval(this.nowTimes, 30 * 1000);
    },
    created() {
        if (this.punch) {
            this.getPunchInTime();
            this.url = `${this.url}/punch-out`;
        } else {
            this.getGeolocationSettings();
            this.url = `${this.url}/punch-in`;
            this.preloader = false;
        }
    },
    destroyed() {
        clearInterval(this.interval);
        if (this.map) {
            this.map.remove();
        }
    },
    methods: {
        submit() {
            this.loading = true;
            this.formData.today = formatDateForServer(new Date());
            this.formData.ip_data = this.geoLocation.error ? {work_from_home: this.geoLocation.work_from_home} : this.geoLocation;
            this.submitFromFixin(`patch`, this.url, this.formData);
        },
        afterSuccess({data}) {
            this.$toastr.s('', data.message);
            this.reloadPunchInOut();
        },
        afterError({data}) {
            this.$toastr.e('', data?.errors?.out_time[0] ? data?.errors?.out_time[0] :
                data?.errors?.in_time[0] ? data?.errors?.in_time[0] : data.message);
            this.reloadPunchInOut();
        },
        dateNameWithTime(date) {
            let dateName = calenderTime(date, false);
            let time = onlyTime(date);
            return `${ucFirst(dateName)} (${time})`;
        },
        nowTimes() {
            this.nowDate = formatDateToLocal(new Date());
            this.nowDateTime = this.dateNameWithTime(new Date());
        },
        getPunchInTime() {
            axiosGet(`${this.url}/punch-in-time`).then(({data}) => {
                this.punchInDate = formatDateToLocal(data.in_time);
                this.punchInDateTime = this.dateNameWithTime(data.in_time);
                this.preloader = false;
                this.checkPunchInFromHome(data.in_ip_data);
                this.getGeolocationSettings();
            }).catch(({response}) => {
                this.$toastr.e('', response.data?.errors?.in_time[0]);
                setTimeout(() => location.reload())
            })
        },
        reloadPunchInOut() {
            this.$hub.$emit('reload-punch-in-out-button');
            this.$hub.$emit('reload-dashboard');
            $("#punch-in-modal").modal('hide');
        },
        checkPunchInFromHome(punchInDetails){
            punchInDetails =  punchInDetails ? JSON.parse(punchInDetails) : {};
            if(punchInDetails.work_from_home){
                this.geoLocation.work_from_home=true;
            }
        },
        getGeolocationSettings() {
            axiosGet(this.apiUrl.GEOLOCATION).then(({data}) => {
                this.geoSettings = data;
                if (data.service_name) {
                    if (data.service_name === 'mapbox') {
                        this.initializeMap(data.api_key);
                    } else if (data.service_name === 'google_map') {
                        // this.getGeoLocationData(data.ip_endpoint);
                        this.initializeGoogleMap(data.api_key);
                    } else if (data.service_name === 'ip_geolocation') {
                        document.querySelector('#map').style.display = 'none'
                        this.getGeoLocationData(`${data.api_endpoint}${data.api_key}`);
                    }
                } else {
                    document.querySelector('#map').style.display = 'none'
                    this.getGeoLocationData(data.ip_endpoint);
                }
            }).catch(({response}) => {
                document.querySelector('#map').style.display = 'none'
                this.geoLocation.error = 'Can not fetch IP data.';
                console.log(response)
            });
        },
        getGeoLocationData(url) {
            fetch(url).then((response) => {
                if (response.ok) {
                    return response.json()
                }
                if (response.status == 401) {
                    throw new Error('Provided Geolocation API KEY is not valid or expired.');
                }
                throw new Error('Can not fetch IP data.');
            }).then((response) => {
                this.geoLocation = {...this.geoLocation, ...response};
            }).catch((error) => {
                this.geoLocation.error = error;
            }).finally(() => this.geoLoader = false);
        },
        initializeMap(token) {
            mapboxgl.accessToken = token;
            this.map = new mapboxgl.Map({
                container: 'map', // container ID
                style: 'mapbox://styles/mapbox/streets-v11', // style URL
            });
            this.identifyLocation(token)
        },
        initializeGoogleMap(token) {
            this.getCurrentPositionByNavigator()
                .then(coords => {
                    this.center = coords;
                    this.geoLocation.coordinate = coords;
                    this.renderGoogleMap(token);
                    this.getGoogleMapGeoLocationData(token, this.center.lat, this.center.lng);
                })
                .catch(err => {
                    this.geoLocation.error = err.message;
                    this.geoLoader = false
                })
        },
        getCurrentPositionByNavigator() {
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const coords = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        resolve(coords);
                    },
                    (error) => {
                        reject(error);
                    }
                );
            });
        },
        renderGoogleMap(token) {
            const loader = new Loader({
                apiKey: token,
                version: "weekly",
                libraries: ["places"]
            });

            const mapOptions = {
                center: this.center,
                zoom: 16,
                gestureHandling: "cooperative",
            };
            loader
                .load()
                .then((google) => {
                    const map = new google.maps.Map(document.getElementById("map"), mapOptions);
                    new google.maps.Marker({
                        position: this.center,
                        map: map,
                    });
                })
                .catch(err => {
                    console.log(err)
                    this.geoLoader = false;
                });
        },
        identifyLocation(token) {
            // Initialize the geolocate control.
            const geolocate = new mapboxgl.GeolocateControl({
                positionOptions: {
                    enableHighAccuracy: true,
                },
                trackUserLocation: true,
            });

            // Add the control to the map.
            this.map.addControl(geolocate);
            // Show the location on map.
            this.map.on('load', () => {
                geolocate.trigger();
            });

            geolocate.options.geolocation.getCurrentPosition(
                (pos) => {
                    const crd = pos.coords;
                    const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${crd.longitude},${crd.latitude}.json?limit=1&types=locality&language=en&access_token=${token}`
                    console.log(`Latitude : ${crd.latitude}`);
                    console.log(`Longitude: ${crd.longitude}`);
                    console.log(`More or less ${crd.accuracy} meters.`);
                    this.getMapboxGeoLocationData(url)
                },
                (err) => {
                    console.warn(`ERROR(${err.code}): ${err.message}`);
                    document.querySelector('#map').style.display = 'none'
                    this.geoLocation.error = `ERROR: Only secure origins are allowed to access the map.`;
                    this.geoLoader = false
                }
            )
        },
        getGoogleMapGeoLocationData(token, lat, lng) {
            let url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`;
            fetch(url).then((response) => {
                if (response.ok) {
                    return response.json()
                }
                if (response.status == 401) {
                    throw new Error('Provided API KEY is not valid or expired.');
                }
                throw new Error('Can not fetch data.');
            }).then(({ results }) => {
                if (results.length) {
                    this.getGeoLocationData(this.geoSettings.ip_endpoint);
                    this.geoLocation.location = results[0].formatted_address;
                }
            }).catch((error) => {
                this.geoLocation.error = error;
                this.geoLoader = false
            });
        },
        getMapboxGeoLocationData(url) {
            fetch(url).then((response) => {
                if (response.ok) {
                    return response.json()
                }
                if (response.status == 401) {
                    document.querySelector('#map').style.display = 'none'
                    throw new Error('Provided API KEY is not valid or expired.');
                }
                throw new Error('Can not fetch data.');
            }).then(({features}) => {
                if (features.length) {
                    this.geoLocation.location = features[0].place_name_en || features[0].place_name;
                    this.getGeoLocationData(this.geoSettings.ip_endpoint);
                }
            }).catch((error) => {
                document.querySelector('#map').style.display = 'none'
                this.geoLocation.error = error;
                this.geoLoader = false
            });
        }
    }
}
</script>

<style scoped lang="scss">
.icon-box {
    width: 90px;
    height: 90px;

    svg {
        width: 26px;
        height: 26px;
    }
}
</style>
