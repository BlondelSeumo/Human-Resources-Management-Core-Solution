<template>
    <div>
        <app-form-group
            :label="$fieldTitle('api_region')"
            page="page"
            v-model="delivery.api_region"
            :placeholder="$placeholder('api_region')"
            :error-message="$errorMessage(errors, 'api_region')"
        />

        <app-form-group
            :label="$t('access_key_id')"
            page="page"
            v-model="delivery.access_key_id"
            :placeholder="$placeholder('access_key_id')"
            :error-message="$errorMessage(errors, 'access_key_id')"
        />

        <app-form-group
            :label="$t('secret_access_key')"
            page="page"
            v-model="delivery.secret_access_key"
            :placeholder="$placeholder('secret_access_key')"
            :error-message="$errorMessage(errors, 'secret_access_key')"
        />

        <!--<app-form-group
          :label="$t('configuration_set')"
          page="page"
          v-model="delivery.configuration_set"
          :placeholder="$placeholder('amazon_ses', 'configuration_set')"
          :error-message="$errorMessage(errors, 'configuration_set')"
        />-->
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";

export default {
    name: "SES",
    mixins: [DeliveryMixin]
}
</script>

<style scoped>

</style>
