<template>
    <div>
        <app-form-group
            :label="$t('host')"
            page="page"
            v-model="delivery.smtp_host"
            :error-message="$errorMessage(errors, 'smtp_host')"
            :placeholder="$placeholder('host')"
        />

        <app-form-group
            :label="$t('port')"
            page="page"
            v-model="delivery.smtp_port"
            :error-message="$errorMessage(errors, 'smtp_port')"
            :placeholder="$placeholder('port')"
        />

        <app-form-group
            :label="$t('encryption')"
            page="page"
            type="select"
            :list="encryptors"
            v-model="delivery.smtp_encryption"
            :error-message="$errorMessage(errors, 'smtp_encryption')"
            :placeholder="$placeholder('encryption')"
        />

        <app-form-group
            :label="$fieldLabel('user', 'name')"
            page="page"
            v-model="delivery.smtp_user_name"
            :error-message="$errorMessage(errors, 'smtp_user_name')"
            :placeholder="$placeholder('user', 'name')"
        />

        <app-form-group
            page="page"
            :label="$t('password')"
            type="password"
            v-model="delivery.smtp_password"
            :error-message="$errorMessage(errors, 'smtp_password')"
            :placeholder="$placeholder('password')"
            :show-password="true"
        />
    </div>
</template>

<script>
import DeliveryMixin from "./DeliveryMixin";
import {addChooseInSelectArray} from "../../../../Helper/Support/FormHelper";

export default {
    mixins: [DeliveryMixin],
    name: "SMTP",
    data() {
        return {
            delivery: {
                smtp_encryption: 'tls'
            }
        }
    },
    computed: {
        encryptors() {
            return addChooseInSelectArray(
                [{id: 'ssl', value: 'SSL'}, {id: 'tls', value: 'TLS'}, {id: 'starttls', value: 'STARTTLS'}],
                'value',
                'encryption'
            )
        }
    }
}
</script>

<style scoped>

</style>
