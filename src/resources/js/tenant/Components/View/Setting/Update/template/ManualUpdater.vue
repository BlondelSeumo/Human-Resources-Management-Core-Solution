<template>
    <div class="row manual-updater">
        <div class="col-12 col-xl-6">
            <p class="mb-2 text-center">{{ $t('step_one') }}</p>
            <div
                class="border-0 height-180 p-primary default-base-color d-flex align-items-center justify-content-center">
                <div>
                    <div class="d-flex justify-content-center">
                        <button
                            class="btn btn-primary text-center"
                            type="button"
                            @click.prevent="manualDownload"
                            :disabled="downloadFileLoader"
                        >
                            <span v-if="downloadFileLoader" class="w-100 d-flex">
                                {{ $t('downloading') }}
                            <app-pre-loader class="ml-2"/>
                            </span>
                            <span v-else>
                                {{ $t('download') }}
                                <app-icon class="size-16 mb-1" name="download"/>
                            </span>
                        </button>
                    </div>
                    <div class="d-flex justify-content-center mt-2">
                        <a href="https://payday.gainhq.com/documentation/change-log.html"
                           target="_blank" class="text-muted hover-underline font-size-90">{{ $t('change_logs') }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <p class="mb-2 text-center"> {{ $t('step_two') }}</p>
            <div class="border-0 height-180 p-primary default-base-color">
                <form ref="form" data-url="" class="mb-0">

                    <div class="form-group mb-primary row align-items-center">
                        <div class="col-12 default-file">
                            <app-input
                                type="file"
                                :label="$t('upload_update_file')"
                                v-model="updateFile"
                            />
                        </div>
                    </div>
                    <div class="form-group mb-0 d-flex justify-content-center">
                        <button
                            class="btn btn-primary text-center"
                            type="button"
                            @click.prevent="update"
                            :disabled="isDisabled.updateBtn">

                            <span class="w-100 d-flex" v-if="updating">
                                {{ $t('updating') }}
                                <app-pre-loader class="ml-2"/>
                            </span>

                            <span v-else>
                                {{ $t('update') }}
                                <app-icon class="size-16 mb-1" name="refresh-cw"/>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <app-modal
            v-if="updateSuccess"
            modal-id="update-success"
            @close-modal="finishingUpdate"
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
                    @click.prevent="finishingUpdate">
                    {{ $t('ok') }}
                </button>

            </div>
        </app-modal>

    </div>
</template>

<script>

import Loader from "../../../../../../core/components/preloders/Loader";
import {FormMixin} from "../../../../../../core/mixins/form/FormMixin";

export default {
    name: "ManualUpdater",
    components: {
        'loader': Loader
    },
    props: {
        nextVersion: {
            required: true
        }
    },
    mixins: [FormMixin],
    data() {
        return {
            updateFile: '',
            updating: false,
            downloadFileLoader: false,
            updateSuccess: false,
            isDisabled: {
                updateBtn: true,
            },
        }
    },
    watch: {
        updateFile: {
            handler: function (val) {
                this.isDisabled.updateBtn = !val;
            }
        }
    },
    methods: {
        finishingUpdate() {
            this.$emit('after-manual-update');
        },
        manualDownload() {
            let downloadUrl = '';
            this.downloadFileLoader = true;
            axios.get(this.getAppUrl('app/v2/manual-update/generate-download-file-url'))
                .then(({data}) => {
                    downloadUrl = data.url;
                    delete axios.defaults.headers.common['X-Requested-With'];
                    delete axios.defaults.headers.common['X-CSRF-TOKEN'];

                    axios.get(downloadUrl, {responseType: 'blob'})
                        .then((response) => {
                            const downloadUrl = window.URL.createObjectURL(new Blob([response.data], {type: 'image/zip'}));
                            const link = document.createElement('a');
                            link.href = downloadUrl;
                            link.setAttribute('download', response.headers.filename + '.zip'); //any other extension
                            document.body.appendChild(link);
                            link.click();
                            link.remove();
                        })
                        .catch(async error => {
                            let responseObj = await error.response.data.text();
                            let errorResponse = JSON.parse(responseObj)
                            this.$toastr.e(errorResponse.message);
                        })
                        .finally(() => {
                            this.downloadFileLoader = false
                        });
                });
        },
        update() {
            this.updating = true;
            //Url info to generate url
            axios.get(this.getAppUrl('app/v2/manual-update/urlInfo'))
                .then(({data}) => {

                    let url = this.getAppUrl('app/v2/manual-update'),
                        formData = new FormData();

                    if (this.updateFile) {
                        const newFile = new File([this.updateFile], this.nextVersion + '.zip', {type: 'zip'});
                        formData.append("update_file", newFile);
                    }

                    formData.append("purchaseCode", data.purchaseCode);
                    formData.append("appId", data.appId);
                    formData.append("next_version", this.nextVersion);

                    //App Update Functionality
                    axios.post(url, formData)
                        .then(({data}) => {
                            this.updateSuccess = true;
                        })
                        .catch(({response}) => {
                            this.$toastr.e(response.data.message);
                        })
                        .finally(() => {
                            this.updating = false;
                            this.clearCache();
                        });
                });
        }
    }
}
</script>

<style lang="scss">
@keyframes updateSuccessAnimation {
    from {
        transform: rotate(90deg);
    }
    to {
        transform: rotate(0);
    }
}

.manual-updater {
    .custom-file {
        .custom-file-label {
            color: #a4a5a7 !important;

            &:after {
                background: #6c757d !important;
            }
        }
    }

    .spinner-border {
        height: 20px;
        width: 20px;
    }

    .check-circle-wrapper {
        svg {
            stroke-width: 1.5 !important;
            color: #4466F2 !important;
        }

        animation-name: updateSuccessAnimation;
        animation-duration: .4s;
    }
}

</style>