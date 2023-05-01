<template>
    <div>
        <app-form-group
            :label="$fieldTitle('path')"
            page="page"
            v-model="delivery.path"
            :placeholder="$placeholder('path')"
            :error-message="$errorMessage(errors, 'path')"
        />
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";

export default {
    name: "Sendmail",
    mixins: [DeliveryMixin]
}
</script>

<style scoped>

</style>
