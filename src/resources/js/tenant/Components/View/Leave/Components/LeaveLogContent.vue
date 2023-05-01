<template>
    <div class="d-flex timeline timeline-response-log mb-4">
        <div class="text-right timeline-title">
            <p class="d-flex align-items-center justify-content-end mb-0">
                {{ $t('at_date', {date: leaveDateAndTimeFormat((leaves.created_at))}) }}
            </p>
            <p class="mb-0">
                        <span class="text-muted font-size-80">
                            {{ leaves.assigned_by ? $t('assigned_by') : $t('applied_by') }}
                        </span>
                <span class="font-size-80">
                            <a v-if="this.$can('view_employees')"
                               :href="profileUrl(leaves.assigned_by ? leaves.assigned_by : user)">
                                {{ leaves.assigned_by ? leaves.assigned_by.full_name : user.full_name }}
                            </a>
                            <a v-else class="cursor-default" @click.prevent="" href="#">
                                {{ leaves.assigned_by ? leaves.assigned_by.full_name : user.full_name }}
                            </a>
                        </span>
            </p>
        </div>
        <div class="d-flex">
            <div class="timeline-icon mx-3">
                <div class="svg-wrapper">
                    <app-icon name="activity"/>
                </div>
            </div>
            <div class="timeline-content">
                <div class="single-record mb-primary">
                    <div class="mb-1 d-flex align-items-center">
                        <div>{{ leaveTypeName }} <span class="dot-icon size-5 mx-2"/></div>
                        <div class="ml-1" v-html="leaveDurations(leaves)"></div>
                    </div>
                    <div v-if="multiDay" class="mb-1">
                        <span class="text-muted"> {{ $t('from') }} </span>
                        {{ leaveDateAndTimeFormat(leaves.start_at) }}
                        <span class="text-muted"> {{ $t('to') }} </span>
                        {{ leaveDateAndTimeFormat(leaves.end_at) }}
                    </div>
                    <div v-else-if="hours" class="mb-1">
                        <span class="text-muted"> {{ $t('from') }} </span>
                        {{ onlyTime(leaves.start_at) }}
                        <span class="text-muted"> {{ $t('to') }} </span>
                        {{ leaveDateAndTimeFormat(leaves.end_at) }}
                    </div>
                    <div v-else class="mb-1">
                        {{ `${calenderTime(leaves.start_at, false)}` }}
                    </div>
                    <div class="text-muted font-size-80">
                        <app-icon name="file-text" class="size-15"/>
                        {{ comment(leaves.comments, 'reason-note').comment }}
                    </div>
                    <template v-for="attachment in leaves.attachments">
                        <a :href="urlGenerator(attachment.path)" class="mr-2 mt-1" target="_blank">
                            <app-icon name="external-link" class="size-15"/>
                        </a>
                    </template>
                </div>
                <div v-if="leaves.attendances && leaves.attendances.length" class="single-record mb-primary">
                    <span class="text-warning">{{ $t('has_attendance') }}</span>
                    <div v-for="attendance in leaves.attendances">
                        <li class="text-muted font-size-80">On {{ formatDateToLocal(attendance.in_date) }}
                            ({{ convertSecondToHourMinutes(getTotalWorked(attendance).asSeconds()) }}h)
                        </li>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {leaveDateAndTimeFormat, leaveDurations} from "../Helper/Helper";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {EMPLOYEES_PROFILE} from "../../../../Config/ApiUrl";
import {
    calenderTime,
    convertSecondToHourMinutes,
    formatDateToLocal,
    onlyTime
} from "../../../../../common/Helper/Support/DateTimeHelper";
import AttendanceHelperMixin from "../../../Mixins/AttendanceHelperMixin";

export default {
    name: "LeaveLogContent",
    props: {
        leaves: {},
        user: {}
    },
    mixins: [AttendanceHelperMixin],
    data() {
        return {
            convertSecondToHourMinutes,
            formatDateToLocal,
            leaveDateAndTimeFormat,
            calenderTime,
            onlyTime,
            leaveDurations,
            urlGenerator
        }
    },
    methods: {
        comment(comments, type) {
            return comments ? this.collection(comments.filter(comment => comment.type === type)).last() : {}
        },
        profileUrl(user) {
            return urlGenerator(`${EMPLOYEES_PROFILE}/${user.id}/profile`);
        },
    },
    computed: {
        multiDay() {
            return this.leaves?.duration_type === 'multi_day';
        },
        hours() {
            return this.leaves?.duration_type === 'hours';
        },
        leaveTypeName() {
            return this.leaves?.type?.name
        }
    }
}
</script>