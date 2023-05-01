<template>
    <div>
        <legend class="col-form-label text-primary pt-0 mb-3">
            {{ $t('set_credentials') }}
        </legend>
        <app-overlay-loader v-if="preLoading"/>
        <div v-else>
            <app-form-group
                :label="$fieldLabel('host', '')"
                page="page"
                v-model="formData.host"
                :placeholder="$placeholder('host', '')"
                :error-message="$errorMessage(errors, 'host')"
            />

            <app-form-group
                :label="$fieldLabel('port', '')"
                page="page"
                v-model="formData.port"
                :placeholder="$placeholder('port', '')"
                :error-message="$errorMessage(errors, 'port')"
            />

            <app-form-group
                :label="$fieldLabel('db', 'name')"
                page="page"
                v-model="formData.db_name"
                :placeholder="$placeholder('db', 'name')"
                :error-message="$errorMessage(errors, 'db_name')"
            />

            <app-form-group
                :label="$fieldLabel('db', 'username')"
                page="page"
                v-model="formData.db_username"
                :placeholder="$placeholder('db', 'username')"
                :error-message="$errorMessage(errors, 'db_username')"
            />

            <app-form-group
                :label="$fieldLabel('db', 'password')"
                page="page"
                type="password"
                v-model="formData.db_password"
                :placeholder="$placeholder('db', 'password')"
                :error-message="$errorMessage(errors, 'db_password')"
                :show-password="true"
            />

            <div class="form-group mt-5 mb-0">
                <app-submit-button @click="submit" :loading="loading"/>
            </div>

            <app-confirmation-modal
                v-if="confirmationModalActive"
                icon="alert-circle"
                modal-id="app-confirmation-modal"
                message="All database is formatted and restore from imported database. Current changes may remove"
                @confirmed="confirmed()"
                @cancelled="cancelled"
            />
            <app-import-loading-modal
                v-if="isImportLoadingActive"
                v-model="isImportLoadingActive"
                :error="errorStatus"
                :response="responseMessage"
                :total-attendance = 'totalAttendance'
                :attendance-complete = 'attendanceComplete'
            />
        </div>
    </div>
</template>

<script>
import {FormMixin} from "../../../../../core/mixins/form/FormMixin";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {axiosGet, axiosPatch, axiosPost} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "ImportDatabase",
    mixins: [FormMixin, FormHelperMixins],
    props: {
        id: {}
    },
    data() {
        return {
            errorStatus: false,
            responseMessage: '',
            confirmationModalActive: false,
            isImportLoadingActive: false,
            preLoading: true,
            totalAttendance: 0,
            attendanceComplete: 0,
            errors: {}
        }
    },
    mounted() {
        this.$hub.$on('headerButtonClicked-' + this.id, (component) => {
            this.confirmationModalActive = true;
        })
        this.getCredentials()
    },
    methods: {
        confirmed(){
            this.isImportLoadingActive = true;
            this.confirmationModalActive = false;
            this.startImport();
        },
        cancelled(){
            this.confirmationModalActive = false;
        },
        getCredentials(){
            this.preLoading = true
            axiosGet(`app/database-2/credentials`).then(({data}) => {
                this.formData = data
                this.preLoading = false
            });
        },
        startImport(){
            $('body').css('pointer-events', 'none')
            axiosPost('app/restore-data').then(({data}) => {
                this.totalAttendance = data.attendance;
                this.responseMessage = 'Successfully store Employee, department, designation, holiday, leave type, leaves, working shift';
                this.startImportAttendance();
                console.log(data.attendance)
            }).catch(({response}) => {
                $('body').css('pointer-events', 'auto')
                console.log(response.data?.message)
                this.errorStatus = true;
                this.responseMessage = response.data?.message;
                this.$toastr.e('', response.data?.message);
                //setTimeout(function(){ location.reload(); }, 2000);
            })
        },

        startImportAttendance(){
            let skip = 0;
            let take = 1000;
            axiosPatch(`app/database-2/credentials`, this.formData).then(({data}) => {
                this.ImportAttendance(skip, take)
            }).catch(({response}) => {
                $('body').css('pointer-events', 'auto')
                this.errorStatus = true;
                this.responseMessage = response.data?.message;
            });

        },
        ImportAttendance(skip, take){
            if (skip < this.totalAttendance){
                axiosPost(`app/restore-data/attendance?skip=${skip}&take=${take}`, this.formData).then(({data}) => {
                    this.attendanceComplete += take
                    skip += take;
                    this.ImportAttendance(skip, take)
                }).catch(({response}) => {
                    $('body').css('pointer-events', 'auto')
                    this.errorStatus = true;
                    this.responseMessage = response.data?.message;
                })
            }else{
                this.attendanceComplete = this.totalAttendance
                this.responseMessage = 'Import Successfully done';
            }
        },
        submit(){
            this.loading = true;
            axiosPatch(`app/database-2/credentials`, this.formData).then(({data}) => {
                this.loading = false
                this.$toastr.s('', data.message);
                this.errors = {}
                this.getCredentials()
            }).catch(({response}) => {
                this.loading = false
                this.errors = response.data.errors
            });
        }
    }

}
</script>

<style scoped>

</style>