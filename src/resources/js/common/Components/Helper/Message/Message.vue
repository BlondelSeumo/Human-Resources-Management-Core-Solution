<template>
    <div v-if="message" :class="divClass">
        <message-error v-if="type === 'error'" :message="message"></message-error>
        <message-success v-else-if="type === 'success'" :message="message"></message-success>
    </div>
</template>

<script>
import Error from './Component/Error'
import Success from "./Component/Success";

export default {
    props: {
        type: {
            required: false,
            default: function () {
                return 'success'
            }
        },
        message: {
            required: true,
            type: String
        },
        divClass: {
            required: false,
            default: function () {
                return '';
            }
        }
    },
    name: "Message",
    components: {
        'message-error': Error,
        'message-success': Success
    }
}
</script>

<style scoped>

</style>
