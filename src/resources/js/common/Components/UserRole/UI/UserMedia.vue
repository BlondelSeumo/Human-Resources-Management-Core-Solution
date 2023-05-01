<template>
    <div class="media align-items-center">
        <div class="avatars-w-50">
            <app-avatar :title="fullName"
                        :shadow="true"
                        :status="rowData.status.class"
                        :border="false"
                        :img="$optional(value, 'full_url')"
                        :alt-text="fullName"/>
        </div>
        <div class="media-body ml-3">
            <a :href="profileUrl">{{ fullName }}</a>
            <p class="text-muted font-size-90 mb-0">
                {{ rowData.email }}
            </p>
        </div>
    </div>
</template>

<script>
import {urlGenerator} from "../../../Helper/AxiosHelper";

export default {
    name: "UserMedia",
    props: {
        value: {},
        rowData: {},
        index: {}
    },
    computed: {
        fullName() {
            return this.rowData.full_name ?
                this.rowData.full_name
                : '';
        },
        profileUrl() {
            return urlGenerator(`${this.apiUrl.EMPLOYEES_PROFILE}/${this.rowData.id}/profile`)
        },
    }
}
</script>

<style scoped>

</style>
