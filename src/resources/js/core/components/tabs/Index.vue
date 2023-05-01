<template>
    <vertical-tab
        v-if="type === 'vertical'"
        :tabs="tabs" :icon="icon"
    />
    <horizontal-tab
        v-else
        :tabs="tabs"
    />
</template>
<script>
import VerticalTab from "./VerticalTab";
import HorizontalTab from "./HorizontalTab";

export default {
    name: "AppTab",
    components: {HorizontalTab, VerticalTab},
    props: {
        tabs: {
            type: Array,
            require: true
        },
        type: {
            type: String,
            default: 'vertical'
        },
        icon: {
            type: String
        }
    }
}
</script>

