<template>
    <div>
        <app-form-group
            :label="$fieldLabel('sparkpost', 'secret')"
            page="page"
            v-model="delivery.sparkpost_secret"
            :error-message="$errorMessage(errors, 'sparkpost_secret')"
            :placeholder="$placeholder('sparkpost', 'secret')"
        />
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";

export default {
    name: "Sparkpost",
    mixins: [DeliveryMixin]
}
</script>

<style scoped>

</style>
