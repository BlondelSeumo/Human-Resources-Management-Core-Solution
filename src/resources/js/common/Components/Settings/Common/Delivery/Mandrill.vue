<template>
    <div>
        <app-form-group
            :label="$fieldLabel('mandrill', 'secret')"
            page="page"
            v-model="delivery.mandrill_secret"
            :error-message="$errorMessage(errors, 'mandrill_secret')"
            :placeholder="$placeholder('mandril', 'secret')"
        />
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";

export default {
    name: "Mandril",
    mixins: [DeliveryMixin]
}
</script>

<style scoped>

</style>
