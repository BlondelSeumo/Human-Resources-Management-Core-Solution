<template>
    <div>
        <app-form-group
            :label="$t('domain_name')"
            page="page"
            v-model="delivery.domain_name"
            :placeholder="$placeholder('domain_name')"
            :error-message="$errorMessage(errors, 'domain_name')"
        />

        <app-form-group
            :label="$t('api_key')"
            page="page"
            v-model="delivery.api_key"
            :placeholder="$placeholder('api_key')"
            :error-message="$errorMessage(errors, 'api_key')"
        />

        <!--<app-form-group
            :label="$t('webhook_signing_key')"
            page="page"
            v-model="delivery.webhook_key"
            :placeholder="$placeholder('webhook_signing_key')"
            :error-message="$errorMessage(errors, 'webhook_key')"
        />-->
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";

export default {
    mixins: [DeliveryMixin],
    name: "Mailgun",
    data() {
        return {}
    },

}
</script>

<style scoped>

</style>
