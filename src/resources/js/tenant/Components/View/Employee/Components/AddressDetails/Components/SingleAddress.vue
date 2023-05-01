<template>
    <div class="row mb-primary"
         :class="{'align-items-center': !address.value}">
        <div class="col-lg-4">
            <address-title :identifier="type"/>
        </div>
        <div class="col-lg-4">
            <template v-if="address.value">
                <address-value :value="address.value" identifier="details"/>
                <address-value :value="address.value" identifier="city"/>
                <p class="mb-0">
                    {{ addressDetails }}
                </p>
                <div v-if="$optional(address, 'value', 'phone_number')" class="d-flex align-items-center">
                    <app-icon
                        name="phone"
                        class="primary-text-color mr-2"
                        width="15"
                        sheight="15"
                    />
                    <address-value :value="address.value" identifier="phone_number"/>
                </div>
            </template>
            <p v-else class="text-muted mb-0">{{ $t('not_added_yet') }}</p>
        </div>
        <div class="col-lg-3">
            <div class="text-right mt-3 mt-lg-0">
                <div v-if="address.value" class="btn-group btn-group-action" role="group"
                     aria-label="Default action">
                    <button class="btn"
                            data-toggle="tooltip"
                            data-placement="top"
                            :title="$t('edit')"
                            v-if="$can('update_employee_address')"
                            @click.prevent="$emit('edit', type)">
                        <app-icon name="edit"/>
                    </button>
                    <button class="btn"
                            data-toggle="tooltip"
                            data-placement="top"
                            :title="$t('delete')"
                            v-if="$can('delete_employee_address')"
                            @click.prevent="$emit('delete', type)">
                        <app-icon name="trash-2"/>
                    </button>
                </div>
                <template v-else>
                    <button class="btn btn-primary"
                            @click="$emit('add', type)"
                            v-if="$can('update_employee_address')">
                        {{ $t('add') }}
                    </button>
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import AddressValue from "./AddressValue";
import AddressTitle from "./AddressTitle";

export default {
    name: "Address",
    props: {
        address: {},
        type: {}
    },
    components: {AddressTitle, AddressValue},
    computed: {
        addressDetails() {
            const {area, state, country, zip_code} = this.address.value
            const addresses = [area, state, zip_code, country]
            let stringAddresses = '';
            addresses.map(address => {
                stringAddresses += address ? `${address}, ` : '';
            })
            return stringAddresses.replace(/,\s*$/, "");
        }
    }
}
</script>