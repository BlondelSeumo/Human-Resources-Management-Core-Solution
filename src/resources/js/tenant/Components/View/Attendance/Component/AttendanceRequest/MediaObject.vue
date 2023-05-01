<template>
    <div class="media align-items-center min-width-200">
        <app-avatar :title="value.full_name"
                    avatar-class="avatars-w-50"
                    :status="value.status.class"
                    :img="$optional(value.profile_picture, 'full_url')"
                    :alt-text="value.full_name"/>
        <div class="media-body ml-3">
            <a v-if="value.full_name" :href="profileUrl">{{ value.full_name }}</a>
            <a v-else :href="profileUrl">{{ value.email }}</a>
            <p class="text-muted font-size-90 mb-0">{{ $optional(value, 'department', 'name') }}</p>
        </div>
    </div>
</template>
<script>
    import {EMPLOYEES_PROFILE} from "../../../../../Config/ApiUrl";
    import {urlGenerator} from "../../../../../../common/Helper/AxiosHelper";

    export default {
        name: "EmployeeMediaObject",
        props: {
            value: {},
            rowData: {},
            index: {},
        },
        computed: {
            profileUrl() {
                return urlGenerator(`${EMPLOYEES_PROFILE}/${this.value.id}/profile`)
            }
        }
    }
</script>