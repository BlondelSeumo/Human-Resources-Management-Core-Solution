<template>
    <a v-if="url"
       :class="btnClass"
       :href="urlGenerator(url)">{{ title }}</a>

    <button v-else
            :class="btnClass"
            @click.prevent="$emit('click')">
        {{ title }}
    </button>
</template>

<script>
import {urlGenerator} from "../../../Helper/AxiosHelper";

export default {
    name: "AddNewButton",
    props: {
        title: {},
        url: {
            default: false
        },
        icon: {
            default: false
        },
        btnClass: {
            default: 'btn btn-primary'
        },
    },
    data() {
        return {
            urlGenerator
        }
    }
}
</script>

<style scoped>

</style>
