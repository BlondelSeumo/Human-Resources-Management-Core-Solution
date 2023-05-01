<template>
    <modal id="response-log-modal"
           v-model="showModal"
           size="large"
           :title="$t('response_log')"
           :preloader="preloader">
        <div class="mb-5">
            <leave-log-content
                :leaves="leaves"
                :user="user"/>

            <template v-for="review in leaves.reviews">
                <leave-log-note :review ="review" />
            </template>
        </div>

        <div v-if="permissions">
            <app-form-group
                type="textarea"
                :text-area-rows="4"
                v-model="formData.note"
                :label="$t('response_note')"
                :placeholder="$t('add_response_note_here')"
            />
            <div class="form-group">
                <app-input
                    type="single-checkbox"
                    v-if="allowBypass && bypassPermissions"
                    v-model="formData.bypassed"
                    :error-message="$errorMessage(errors, 'bypassed')"
                    :list-value-field="$t('bypassed_to_manager')"
                />
            </div>
            <div class="row align-items-center my-primary" v-if="!this.formData.bypassed">
                <div class="col-md-3">
                    <label>
                        {{ $t('update_status') }}
                    </label>
                </div>
                <div class="col-md-9">
                    <app-input
                        type="radio"
                        v-model="status_name"
                        :list="leaveStatuses"
                        list-value-field="name"/>
                </div>
            </div>
        </div>

        <template slot="footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                {{ $t('close') }}
            </button>
            <app-submit-button
                btn-class="btn btn-success ml-2"
                @click="updateStatus()"
                :label="$t('save')"
                :loading="btnLoader"
                v-if="permissions"
            />
        </template>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet, axiosPost} from "../../../../../common/Helper/AxiosHelper";
import LeaveLogNote from "./LeaveLogNote";
import LeaveLogContent from "./LeaveLogContent";
import FormHelperMixins from "../../../../../common/Mixin/Global/FormHelperMixins";
import {canUpdate} from "../Helper/Helper";

export default {
    name: "ResponseLogModal",
    components: {LeaveLogContent,LeaveLogNote},
    mixins: [ModalMixin, FormHelperMixins],
    props: {
        url : {},
        tableId: {},
        managerDept: {}
    },
    data(){
        return {
            leaves: {},
            user: {},
            allowBypass: false,
            formData: {
                bypassed: false
            },
            status_name: {},
            btnLoader: false,
            leaveStatuses: [
                {id: 'pending', name: 'Pending'},
                {id: 'approved', name: 'Approved'},
                {id: 'rejected', name: 'Rejected'}
            ]
        }
    },
    mounted() {
        this.preloader = true;
        this.getLeaveLog()
    },
    computed: {
        authUser(){
            return window.user
        },
        permissions(){
            return this.leaves.status?.name === 'status_pending' && canUpdate(this.managerDept, this.leaves) &&
                this.leaves.user_id && this.leaves.user_id != window.user.id
        },
        bypassPermissions(){
            if (!this.$isOnlyDepartmentManager()){
                return false;
            }

            if(this.leaves.reviews.length > 0) {
                return !!this.collection(this.leaves.reviews).last().department?.department_id;
            }

            return !!this.leaves?.user?.department && !!this.leaves?.user?.department?.parent_department;

        }
    },
    methods: {
        getLeaveLog(){
            axiosGet(this.url).then(({data}) => {
                this.preloader = false;
                this.leaves = data
                this.status_name = data.status.name.replace('status_', '')
                this.user = data.user
                this.allowBypass = data.allowBypass;
            })
        },
        updateStatus(){
            this.btnLoader = true;
            this.formData.id = this.leaves.id

            if (this.status_name === 'pending' && !this.formData.bypassed) {
                this.btnLoader = false;
                return this.$toastr.e('', this.$t('nothing_to_change'));
            }
            this.formData.status_name = this.formData.bypassed ? 'bypassed' : this.status_name

            axiosPost(
                `${this.apiUrl.LEAVES}/request/${this.leaves.id}/${this.formData.status_name}`,
                this.formData
            ).then(({data}) => {
                this.formData = {};
                this.btnLoader = false;
                this.$toastr.s('', data.message);
                $('#response-log-modal').modal('hide');
                this.showModal = false;
                this.$hub.$emit(`reload-${this.tableId}`);
            }).catch(({response}) => {
                this.btnLoader = false;
                this.$toastr.e('', response.data.message);
            })
        }
    },
}
</script>