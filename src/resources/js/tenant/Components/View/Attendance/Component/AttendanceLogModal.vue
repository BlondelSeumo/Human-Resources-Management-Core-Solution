<template>
    <modal id="attendance-log-modal"
           size="large"
           v-model="showModal"
           :title="$t('change_log')"
           :scrollable="false"
           :preloader="preloader">
        <div v-for="(details, index) in detailsWithParents">
            <div class="d-flex timeline timeline-change-log mb-4">
                <div class="text-right timeline-title">
                    <p class="mb-0">
                        {{
                            $t('at_date', {date: dateTimeFormat(details.review_by &&
                                details.status.name !== 'status_log' ? details.updated_at : details.created_at)})
                        }}
                    </p>
                    <p class="mb-0">
                        <span v-if="details.status && details.status.name === 'status_pending'"
                              class="font-size-80 text-muted">
                            {{ $t('still_pending') }}
                        </span>
                        <span v-else class="font-size-80 text-muted d-block">
                            {{
                                $t('action_by', {action: details.reviewer && details.status.name !== 'status_log'?
                                        details.status.translated_name :
                                        details.attendance_details_id ? $t('updated'): $t('added')})
                            }}
                            <a v-if="$can('view_employees')" :href="profileUrl(addedUser(details))">
                                {{ addedUser(details).full_name }}
                            </a>
                            <a v-else class="cursor-default" @click.prevent="" href="#">{{ addedUser(details).full_name }}</a>
                        </span>
                        <span v-if="index === 0" class="font-size-80 primary-text-color">
                            {{ $t('last_updated') }}
                        </span>
                    </p>
                </div>
                <div class="d-flex">
                    <div class="timeline-icon mx-3">
                        <div class="svg-wrapper">
                            <app-icon name="clock"/>
                        </div>
                    </div>
                    <div class="timeline-content">
                        <div class="single-record mb-primary">
                            <log-date-time-with-note
                                type="punch-in"
                                :details="details"
                                :user="user"
                                class-name="success"
                            />
                        </div>
                        <div v-if="details.out_time" class="single-record mb-primary">
                            <log-date-time-with-note
                                type="punch-out"
                                :details="details"
                                :user="user"
                                class-name="warning"
                            />
                        </div>
                        <div v-for="(comment, index) in getExtraComment(details)" class="single-record mb-primary">
                            <log-note
                                :class="{'mb-primary': getExtraComment(details).length - 1 !== index}"
                                :user-name="comment.user.full_name"
                                :user-id="comment.user.id"
                                :comment="comment"
                            />
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <template slot="footer">
            <div v-if="updatePermissions" class="mr-1">
                <app-submit-button
                    btn-class="btn btn-success mr-2"
                    @click="updateStatus('approve')"
                    :label="$t('approve_and_close')"
                    :loading="approveLoader"
                    v-if="$can('update_attendance_status')"
                />
                <app-submit-button
                    btn-class="btn btn-danger mr-2"
                    @click="updateStatus('reject')"
                    :label="$t('reject_and_close')"
                    :loading="rejectLoader"
                    v-if="$can('update_attendance_status')"
                />
            </div>
            <button type="button"
                    class="btn btn-secondary"
                    data-dismiss="modal">
                {{ $t('close') }}
            </button>
        </template>
    </modal>
</template>

<script>
import ModalMixin from "../../../../../common/Mixin/Global/ModalMixin";
import {axiosGet, axiosPost, urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {dateTimeFormat} from "../../../../../common/Helper/Support/DateTimeHelper";
import LogDateTimeWithNote from "./LogDateTimeWithNote";
import LogNote from "./LogNote";
import {EMPLOYEES_PROFILE} from "../../../../Config/ApiUrl";
import {TENANT_BASE_URL} from "../../../../../common/Config/UrlHelper";
import {filterLastPendingData} from "../Helper/Helper";

export default {
    name: "attendanceLogModal",
    mixins: [ModalMixin],
    components: {LogDateTimeWithNote, LogNote},
    props: {
        url: '',
        tableId: {}
    },
    data() {
        return {
            approveLoader: false,
            rejectLoader: false,
            user: {},
            status: {},
            detailsId: {},
            detailsWithParents: [],
            formData: {},
            dateTimeFormat,
        }
    },
    mounted() {
        this.preloader = true;
        this.getDetailsLog()
    },
    computed: {
        updatePermissions(){
            let conditions = this.$can('update_attendance_status') &&
                this.status.name === 'status_pending' &&
                (this.user.id != window.user.id || this.$isAdmin())

            return conditions && (!this.$isOnlyDepartmentManager() ||
                this.$isOnlyDepartmentManager() && this.user.id != window.user.id)
        }
    },
    methods: {
        addedUser(details){
            return details.reviewer && details.status.name !== 'status_log' ? details.reviewer : details.assign_by ? details.assign_by : this.user
        },
        getDetailsLog() {
            axiosGet(this.url).then(({data}) => {
                this.preloader = false
                this.status = data.status
                this.detailsId = data.id
                this.user = data.attendance?.user
                this.formatDetailsInArray(data)
            });
        },
        formatDetailsInArray(details) {
            let i = 0;
            this.detailsWithParents[i] = {...details}
            while (this.detailsWithParents[i].parent_attendance_details) {
                i++;
                this.detailsWithParents.push(this.detailsWithParents[i - 1].parent_attendance_details)
            }
        },
        getExtraComment(details) {
            return details.comments.map(comment => {
                return (comment.type !== 'in-note' && comment.type !== 'out-note') ? comment : null
            }).filter((comment) => comment != null)
        },
        profileUrl(user) {
            return urlGenerator(`${EMPLOYEES_PROFILE}/${user.id}/profile`);
        },
        updateStatus(status) {
            this.formData.status_name = status
            this.setLoader(status)
            axiosPost(
                `${this.apiUrl.ATTENDANCES}/${this.detailsId}/status/${this.formData.status_name}`,
                this.formData
            ).then(({data}) => {
                this.formData = {};
                this.setLoader(status, false);
                this.$toastr.s('', data.message);
                $('#attendance-log-modal').modal('hide');
                this.showModal = false;
                this.$hub.$emit(`reload-${this.tableId}`);
            }).catch(({response}) => {
                this.setLoader(status, false);
                this.$toastr.e('', response.data.message);
            })
        },
        setLoader(status = null, action = true) {
            let variableName = status ? `${status}Loader` : 'loader';
            let setStatusValue = 'this.' + variableName + ' = ' + action.toString();
            eval(setStatusValue);
        },
    }
}
</script>

<style scoped>

</style>