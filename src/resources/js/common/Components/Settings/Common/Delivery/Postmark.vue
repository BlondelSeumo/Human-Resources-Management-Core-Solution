<template>
    <div>
        <app-form-group
            :label="$fieldLabel('postmark', 'token')"
            page="page"
            v-model="delivery.postmark_token"
            :error-message="$errorMessage(errors, 'postmark_token')"
            :placeholder="$placeholder('postmark', 'token')"
        />
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";

export default {
    mixins: [DeliveryMixin],
    name: "Postmark"
}
</script>

<style scoped>

</style>
