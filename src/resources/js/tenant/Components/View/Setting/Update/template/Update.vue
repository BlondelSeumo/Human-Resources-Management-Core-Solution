<template>
    <div v-if="!loading">
        <div class="mb-primary">
            <a href="https://payday.gainhq.com/documentation/upgrade.html" target="_blank"
               class="float-right text-muted hover-underline">Need help?</a>
            <app-note
                :class="'mb-primary clearfix'"
                :title="$fieldTitle('update')"
                content-type="html"
                :notes="[Object.keys(no_update_message).length ?
             `${no_update_message.message_1} ${no_update_message.message_2 ? `\n ${no_update_message.message_2}` : ''}`
             : $t('update_warning_details')]"
            />
        </div>

<!--        <app-confirmation-modal-->
<!--            v-if="!confirmationModalActive"-->
<!--            :title="this.$t('important_announcement')"-->
<!--            :message="this.$t('version_update_message')"-->
<!--            :second-button-name="$t('okay')"-->
<!--            :hide-first-button="true"-->
<!--            modal-class="primary"-->
<!--            icon="alert-triangle"-->
<!--            modal-id="app-confirmation-modal"-->
<!--        />-->

        <app-modal
            v-if="defaultUpdateSuccess"
            modal-id="default-update-success"
            @close-modal="redirectHomePage"
            modal-alignment="center">

            <div class="p-primary d-flex justify-content-center align-items-center flex-column">

                <h5>{{ $t('congratulations') }}</h5>

                <div class="check-circle-wrapper my-primary">
                    <app-icon name="check-circle" class="size-80"/>
                </div>

                <p>{{ $t('your_app_has_been_updated_successfully') }}</p>

                <button
                    class="btn btn-primary"
                    type="button"
                    data-dismiss="modal"
                    @click.prevent="redirectHomePage">
                    {{ $t('ok') }}
                </button>

            </div>
        </app-modal>

        <div v-if="defaultUpdate && updates.result.length">
            <div class="border-0 height-180 p-primary default-base-color d-flex flex-column align-items-center justify-content-center">
                <h5 class="mb-1">{{$t('version')}}: {{nextUpdatableVersion}}</h5>
                <a href="https://payday.gainhq.com/documentation/change-log.html"
                   target="_blank" class="text-muted hover-underline font-size-90">{{ $t('change_logs') }}</a>
                <button
                    type="button"
                    class="btn btn-primary mt-primary"
                    @click="updateApp(nextUpdatableVersion)">
                    {{$t('update')}}
                    <app-icon class="ml-2 size-16" name="download"/>
                </button>
            </div>
        </div>

        <app-manual-updater
            v-if="!defaultUpdate"
            :next-version="nextVersion"
            @after-manual-update="redirectHomePage"
        />

        <app-update-confirmation-modal
            v-if="confirmationModalActive"
            modal-id="app-confirmation-modal"
            @confirmed="confirmed"
            @cancelled="cancelled"
            :message="$t('this_will_update_entire_app')"
        />

    </div>
    <div v-else>
        <app-overlay-loader/>
        <p class="text-center margin-top-100">{{ $t('please_wait_it_might_take_time') }}</p>
    </div>

</template>

<script>

import {axiosGet, axiosPost, urlGenerator} from "../../../../../../common/Helper/AxiosHelper";
import {FormMixin} from "../../../../../../core/mixins/form/FormMixin";
import {APP_UPDATE, APP_UPDATE_INSTALL, CLEAR_CACHE, GET_UPDATE_URL} from "../../../../../Config/ApiUrl";

export default {
    name: "Update",
    props: ["props"],
    mixins: [FormMixin],
    data() {
        return {
            urlGenerator,
            updates: {},
            loading: true,
            confirmationModalActive: false,
            selectedVersion: '',
            no_update_message: {},
            afterUpdateInstruction: '',
            defaultUpdate: true,
            defaultUpdateSuccess: false,
            nextVersion: '',
        }
    },
    created() {
        this.verify();
    },
    computed: {
        nextUpdatableVersion() {
            return this.updates.result.map(i => i.version).sort()[0]
        }
    },
    mounted() {
        setTimeout(function () {
            $('[data-toggle="tooltip"]').tooltip();
        }, 6000);
    },
    methods: {
        verify() {
            let url = '', purchase_response = '';
            axiosGet(GET_UPDATE_URL)
                .then(response => {
                    url = response.data;
                })
                .then(() => {
                    delete axios.defaults.headers.common['X-Requested-With'];
                    delete axios.defaults.headers.common['X-CSRF-TOKEN'];
                    axios.get(url)
                        .then(response => {
                            purchase_response = response.data.data;
                            if (purchase_response === 'Verified') {
                                this.getUpdates();
                            } else {
                                this.updates = {result: []};
                                this.loading = false;
                                this.$toastr.e(response.data.data);
                            }
                        });
                })
        },
        getUpdates() {
            axiosGet(APP_UPDATE)
                .then(response => {
                    //Auto update
                    this.updates = response.data;
                    this.loading = false;

                })
                .catch(({response}) => {

                    if (response.data.status == false) {
                        this.loading = false;
                        try{
                            this.no_update_message = JSON.parse(response.data.message);
                        } catch (e) {
                            this.no_update_message.message_1 = response.data.message;
                        }

                    } else {
                        this.no_update_message = {};
                        //Manual update
                        this.checkIfUpdatesExists();
                    }
                    this.updates = {result: []};
                })
        },
        checkIfUpdatesExists() {
            let downloadUrl = '';
            this.downloadFileLoader = true;
            axios.get(this.getAppUrl('app/v2/manual-update/generate-download-file-url'))
                .then(({data}) => {
                    downloadUrl = data.url;
                    delete axios.defaults.headers.common['X-Requested-With'];
                    delete axios.defaults.headers.common['X-CSRF-TOKEN'];

                    axios.get(downloadUrl + '&check_version=true')
                        .then((response) => {
                            this.defaultUpdate = !response.data.availableVersions.length;

                            if (response.data.availableVersions.length) {
                                this.nextVersion = response.data.availableVersions[0]
                            }else {
                                this.no_update_message = {};
                                this.no_update_message.message_1 = this.$t('no_update_found');
                            }
                        })
                        .finally(() => {
                            this.loading = false;
                        });
                });
        },
        updateApp(version) {
            this.selectedVersion = version;
            this.confirmationModalActive = true;
        },
        confirmed() {
            this.loading = true;
            let url = '', purchase_response = '';
            axiosGet(GET_UPDATE_URL)
                .then(response => {
                    url = response.data;
                })
                .then(() => {
                    delete axios.defaults.headers.common['X-Requested-With'];
                    delete axios.defaults.headers.common['X-CSRF-TOKEN'];
                    axios.get(url)
                        .then(response => {
                            purchase_response = response
                        })
                        .then(() => {
                            axiosPost(`${APP_UPDATE_INSTALL}` + '/' + this.selectedVersion, {})
                                .then(({data}) => {
                                    this.loading = false;
                                    this.defaultUpdateSuccess = true;
                                    this.confirmationModalActive = false;
                                })
                                .catch(({response}) => {
                                    this.$toastr.e('', response.data.message);
                                })
                                .finally(()=>{
                                    this.clearCache();
                                });
                        });
                });
        },
        redirectHomePage() {
            window.location = urlGenerator('/');
        },
        cancelled() {
            this.confirmationModalActive = false;
            this.selectedVersion = '';
        },
        clearCache(){
            axiosGet(CLEAR_CACHE);
        },
    },
}
</script>
