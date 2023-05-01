<template>
    <div>
        <template v-for="file in value">
            <a :href="urlGenerator(file.path)" target="_blank">
                <app-icon name="external-link"/>
            </a>
        </template>
        <div v-if="value.length === 0">
            {{ '-' }}
        </div>
    </div>
</template>

<script>
import {urlGenerator} from '../../../../../common/Helper/AxiosHelper';

export default {
    name: "AttachmentsColumn",
    props: {
        value: {},
        rowData: {}
    },
    data(){
        return{
            urlGenerator,
        }
    },
    computed: {
        file(){
            return this.value;
        }
    }
}
</script>

<style scoped>

</style>