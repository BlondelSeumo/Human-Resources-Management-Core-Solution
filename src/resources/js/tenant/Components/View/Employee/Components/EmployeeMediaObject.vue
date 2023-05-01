<template>
    <div class="media align-items-center">
        <app-avatar :title="rowData.full_name"
                    avatar-class="avatars-w-50"
                    :status="rowData.status.class"
                    :img="imageUrl"
                    :alt-text="rowData.full_name"/>
        <div class="media-body ml-3">
            <a v-if="rowData.full_name" :href="profileUrl">{{ rowData.full_name }}</a>
            <a v-else :href="profileUrl">{{ rowData.email }}</a>
            <p v-if="rowData.designation" class="text-muted font-size-90 mb-0">{{ rowData.designation.name }}</p>
        </div>
    </div>
</template>
<script>
import {EMPLOYEES_PROFILE} from "../../../../Config/ApiUrl";
import {urlGenerator} from "../../../../../common/Helper/AxiosHelper";

export default {
    name: "EmployeeMediaObject",
    props: {
        value: {},
        rowData: {},
        index: {},
    },
    computed: {
        profileUrl() {
            return urlGenerator(`${EMPLOYEES_PROFILE}/${this.rowData.id}/profile`)
        },
        imageUrl() {
            if (this.rowData.profile_picture) {
                return urlGenerator(this.rowData.profile_picture.path);
            }
        }
    }
}
</script>