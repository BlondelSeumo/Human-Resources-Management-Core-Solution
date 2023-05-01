<template>
    <app-note v-if="!isMailSettingExist"
              :title="$t('no_delivery_settings_found')"
              :notes="$optional(currentTenant, 'is_single') ?
                   `<ol>
                     <li>${$t('cron_job_settings_warning',{
                      documentation: `<a href='https://payday.gainhq.com/documentation/important-settings.html#scheduler-queue' target='_blank'>${$t('documentation')}</a>`
                  })}</li>
                     <li>${$t('no_delivery_settings_warning_on_import', {
                      location: `<a href='${urlGenerator(TENANT_EMAIL_SETUP_SETTING)}'>${$t('here')}</a>`
                  })}</li>
                  </ol>`: [$t('no_delivery_settings_warning_none_on_import')]"
              content-type="html"
    />
    <div v-else class="card border-0">
        <form
            class="mb-0"
            ref="form"
            :data-url="`${apiUrl.IMPORT}/employees`"
            enctype="multipart/form-data"
        >
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div class="note-title d-flex">
                            <app-icon name="book-open"/>
                            <h6 class="card-title pl-2">{{ $t("csv_format_guideline") }}</h6>
                        </div>
                        <div class="note note-warning p-4 mb-2">
                            <ol>
                                <li class="my-1"> {{ $t("csv_format_guide") }}</li>
                                <li class="my-1"> {{ $t("csv_column_separation_guide") }}</li>
                                <li class="my-1"> {{ $t("csv_column_guide") }}</li>
                                <li class="my-1"> {{ $t("csv_required_field_guide") }}</li>
                                <li class="my-1"> {{ $t("csv_column_data_format") }}</li>
                                <li class="my-1"><b> {{ $t("csv_huge_data_guide") }}</b></li>
                            </ol>
                        </div>

                        <div class="d-flex flex-column flex-sm-row align-items-sm-center mb-primary">
                            <span class="mr-2">{{ $t("csv_download_label") }}</span>

                            <a href="#" @click.prevent="sampleDownload">{{ $t("download_sample_file") }}</a>
                        </div>

                        <div class="form-group">
                            <app-input v-model="files" type="dropzone"/>

                            <small v-if="maxFileWarning" class="text-danger">
                                Multiple file is not allowed. Please select one file.
                            </small>
                            <small v-if="errors.import_file" class="text-danger">
                                {{ errors.import_file[0] }}
                            </small>

                            <div v-if="stat.errors > 0" class="mt-2">
                                <span class="text-secondary">
                                    <app-icon class="mr-2" name="check-circle" stroke-width="1"/>
                                    <span class="text-success">{{ stat.successfull }}</span>
                                    successful rows
                                </span>
                                <div class="progress mt-1 mb-1">
                                    <div
                                        :aria-valuenow="`${stat.successRate}`"
                                        :style="`width: ${stat.successRate}%`"
                                        aria-valuemax="100"
                                        aria-valuemin="0"
                                        class="progress-bar progress-bar-striped bg-success"
                                        role="progressbar"
                                    >
                                        {{ stat.successRate }}
                                        %
                                    </div>
                                </div>
                                <span class="text-secondary">
                                    <app-icon class="mr-2" name="x-circle" stroke-width="1"/>
                                    <span class="text-warning">{{ stat.unsuccessfull }}</span>
                                    unsuccessfull rows <br/>
                                </span>
                                <div class="progress mt-1 mb-1">
                                    <div
                                        :aria-valuenow="`${stat.unsuccessRate}`"
                                        :style="`width: ${stat.unsuccessRate}%`"
                                        aria-valuemax="100"
                                        aria-valuemin="0"
                                        class="progress-bar progress-bar-striped bg-danger"
                                        role="progressbar"
                                    >
                                        {{ stat.unsuccessRate }}
                                        %
                                    </div>
                                </div>
                                <span class="text-secondary">
                                    <app-icon class="mr-2" name="info" stroke-width="1"/>
                                    <span class="text-danger">{{ stat.errors }}</span>
                                    {{ $t("errors_found") }}
                                </span>
                                <div class="progress mt-1 mb-1">
                                    <div
                                        :aria-valuenow="`${stat.errorRate}`"
                                        :style="`width: ${stat.errorRate}%`"
                                        aria-valuemax="100"
                                        aria-valuemin="0"
                                        class="progress-bar progress-bar-striped bg-secondary"
                                        role="progressbar"
                                    >
                                        {{ stat.errorRate }}
                                        %
                                    </div>
                                </div>
                                <li class="mt-2">{{ $t("csv_error_handle_guide") }}</li>
                            </div>

                            <template v-if="errors.length > 0">
                                <div
                                    class="alert alert-danger alert-dismissible fade show mt-3"
                                    role="alert"
                                >
                                    <h5 class="alert-heading">Error!</h5>
                                    <p v-if="errors.length === 1">{{ errors.length }} required
                                        {{ errors.every(val => keys.includes(val)) ? 'column' : 'field' }} is missing!</p>
                                    <p v-else>{{ errors.length }} required
                                        {{ errors.every(val => keys.includes(val)) ? 'columns' : 'fields' }} are
                                        missing!</p>
                                    <template v-for="err in errors">
                                        <li class="m-0 p-0">
                                            <em><code>{{ Array.isArray(err) ? err[0] : err }}</code></em>
                                        </li>
                                    </template>
                                    <button
                                        type="button"
                                        class="close"
                                        data-dismiss="alert"
                                        aria-label="Close"
                                    >
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <div class="card-footer p-primary bg-transparent">
            <app-submit-button :label="$t('import')" :loading="loading" @click="submitData"/>
            <button
                type="button"
                class="btn btn-secondary mr-4"
                data-dismiss="modal"
                @click="cancel"
            >
                {{ $t("cancel") }}
            </button>
            <button
                v-if="stat.errors > 0"
                class="btn btn-warning btn-sm"
                href=""
                @click.prevent="importErrorOpen"
            >
                <app-icon name="download" class="mr-2" stroke-width="1"/>
                {{ "Error Report.xlsx" }}
            </button>
        </div>

        <app-confirmation-modal
            v-if="confirmationModalActive"
            :loading="loading"
            :message="promptMessage"
            :title="promptTitle"
            :second-button-name="$t('okay')"
            :hide-first-button="true"
            :modal-class="promptClass"
            :icon="promptIcon"
            modal-id="app-confirmation-modal"
            @confirmed=""
            @cancelled="confirmationModalActive = false"
            :self-close="false"
        />
    </div>
</template>

<script>
import {FormMixin} from "../../../../../core/mixins/form/FormMixin";
import HelperMixin from "../../../../../common/Mixin/Global/HelperMixin";
import {axiosGet, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {TENANT_EMAIL_SETUP_SETTING, TENANT_MAIL_CHECK_URL} from "../../../../../common/Config/apiUrl";

export default {
    name: "ImportEmployees",
    mixins: [FormMixin, HelperMixin],
    props: {
        validKeys: {
            type: Array,
        },
    },
    data() {
        return {
            urlGenerator,
            TENANT_EMAIL_SETUP_SETTING,
            confirmationModalActive: false,
            promptClass: '',
            promptIcon: '',
            promptMessage: '',
            promptTitle: this.$t('are_you_sure'),
            isMailSettingExist: true,
            files: [],
            maxFileWarning: false,
            errors: {},
            stat: {},
            loading: false,
            customFieldType: 0,
            sampleFileType: [],
            keys: [
                "name",
                "email",
                "gender",
                "employee_id",
                "department",
                "designation",
                "employment_status",
                "roles",
                "salary",
                "joining_date",
            ],
        };
    },
    created() {
        this.checkMailSettings();
    },
    methods: {
        checkMailSettings() {
            this.loading = true;
            axiosGet(TENANT_MAIL_CHECK_URL).then(response => {
                this.isMailSettingExist = !!response.data;
            }).finally(() => {
                this.loading = false;
            });
        },
        beforeSubmit() {
            this.loading = true;
        },
        submitData() {
            if (this.files.length > 1) {
                this.maxFileWarning = true;
                return;
            }
            this.maxFileWarning = false;

            let formData = new FormData();
            if (this.files.length) {
                formData.append("import_file", this.files[0]);
            }
            this.save(formData, {
                responseType: "blob",
            });
        },
        afterError(response) {
            if (response.status == 500) {
                this.confirmationModalActive = true;
                this.promptClass = 'warning';
                this.promptIcon = 'alert-triangle';
                this.promptTitle = response.data.message || this.$t('maximum_execution_time_exceeded');
                this.promptMessage = this.$t("csv_huge_data_guide");
            } else {
                this.errors = response.data.errors || response.data;
            }
            this.loading = false;
        },
        afterSuccess(response) {
            this.loading = false;
            if (response.data.stat?.errors > 0) {
                if (response.data.stat.successfull > 0) {
                    this.confirmationModalActive = true;
                    // this.$toastr.i(response.data.message);
                    this.promptClass = 'primary';
                    this.promptIcon = 'check-circle';
                    this.promptTitle = this.$t('partially_data_imported');
                    this.promptMessage = this.$t('after_employee_import_message');
                }
                this.stat = response.data.stat;
            } else {
                this.confirmationModalActive = true;
                // this.$toastr.s(response.data.message);
                this.promptClass = 'success';
                this.promptIcon = 'check-circle';
                this.promptTitle = this.$t('data_imported_successfully');
                this.promptMessage = this.$t('after_employee_import_message');
                this.scrollToTop(false);
                this.files = [];
                this.errors = {};
                this.stat = {};
                // setTimeout(() => {
                //     location.reload();
                // }, 1000);
            }
        },
        afterFinalResponse() {
            this.loading = false;
        },
        cancel() {
            location.reload();
        },
        sampleDownload() {
            let commas = "";
            let keys = this.keys;

            if (this.sampleFileType.length) {
                keys = [...this.validKeys];
                commas = ",".repeat(keys.slice(5).length);
            }

            this.downloadCSV(
                `${keys.join(",")}\n` +
                `sample name,email1@demo.com,male,emp-01,Main Department,Director,permanent,App Admin,10000, 2010-08-20${commas}\n` +
                `example name,email2@demo.com,female,emp-02,Main Department,Director,probation,Manager,10000, 2010-08-20${commas}\n` +
                `demo name,email3@demo.com,other,emp-03,Accounts,Director,permanent,Employee,10000, 2010-08-20${commas}\n`
            );
        },

        downloadCSV(csv) {
            let e = document.createElement("a");
            e.href = "data:text/csv;charset=utf-8," + encodeURI(csv);
            e.target = "_blank";
            e.download = `${this.$t("employees")}.csv`;
            e.click();
        },
        importErrorOpen() {
            var downloadLink = window.document.createElement("a");
            downloadLink.href = urlGenerator("/storage/importReport.xlsx");
            downloadLink.download = "Import Report.xlsx";
            // document.body.appendChild(downloadLink);
            downloadLink.click();
        },
    },
    watch: {
        files: {
            handler: function () {
                this.errors = {};
            },
        },
    }
};
</script>
