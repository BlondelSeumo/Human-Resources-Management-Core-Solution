<template>
    <div>
        <p class="font-size-90 mb-0">
            <a v-if="this.$can('view_employees')" :href="profileUrl">
                {{ userName }}
            </a>
            <a v-else class="cursor-default" @click.prevent="" href="#">{{ userName }}</a>
            <span class="text-muted font-size-90">
                {{ comment.parent_id ? $t('has_changed') : $t('has_added') }}
            </span>
            {{ `${$t('note_for')} ${comment.type}` }}
            <span class="text-muted font-size-90">
                {{ $t('entry') }}
            </span>
        </p>
        <p v-if="comment.comment" class="font-size-80 text-muted mb-0">
            <app-icon name="file-text" class="size-15"/>
            {{ comment.comment }}
        </p>
    </div>
</template>

<script>
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";
import {EMPLOYEES_PROFILE} from "../../../../Config/ApiUrl";

export default {
    name: "LogNote",
    props: {
        comment: {},
        userName: {},
        userId: {}
    },
    computed: {
        profileUrl() {
            return urlGenerator(`${EMPLOYEES_PROFILE}/${this.userId}/profile`)
        }
    }
}
</script>

<style scoped>

</style>