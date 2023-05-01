<template>
    <div class="d-flex timeline timeline-response-log mb-4">
        <div class="text-right timeline-title">
            <p class="d-flex align-items-center justify-content-end mb-0">
                {{ $t('at_date', {date: leaveDateAndTimeFormat(review.created_at)}) }}
            </p>
            <p class="mb-0 font-size-80">
                <span class="primary-text-color"></span>
            </p>
            <p class="mb-0">
                            <span class="text-muted font-size-80">
                                {{ `${review.status.translated_name} by` }}
                            </span>
                <span class="font-size-80">
                                <a v-if="this.$can('view_employees')" :href="profileUrl(review.reviewed_by)">
                                    {{ review.reviewed_by.full_name }}
                                </a>
                                <a v-else class="cursor-default" @click.prevent="" href="#">{{ review.reviewed_by.full_name }}</a>
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
                    <div class="mb-1">{{ $t('response_note') }}</div>
                    <div class="text-muted font-size-80">
                        <app-icon name="file-text" class="size-15"/>
                        {{ comment(review.comments, 'response-note').comment || $t('not_added') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import {leaveDateAndTimeFormat} from "../Helper/Helper";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {EMPLOYEES_PROFILE} from "../../../../Config/ApiUrl";

export default {
    name: "LeaveLogNote",
    props: {
        review: {}
    },
    data() {
        return {
            leaveDateAndTimeFormat
        }
    },
    methods: {
        comment(comments, type) {
            return comments ? this.collection(comments.filter(comment => comment.type === type)).last() : {}
        },
        profileUrl(user) {
            return urlGenerator(`${EMPLOYEES_PROFILE}/${user.id}/profile`);
        },
    }
}
</script>

<style scoped>

</style>