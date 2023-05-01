<template>
    <div class="d-flex align-items-center mb-3 mb-lg-0">
        <div>
            <div class="icon-box mr-2">
                <app-icon v-if="identifier === 'permanent_address'" name="home"/>
                <app-icon v-else name="map-pin"/>
            </div>
        </div>
        {{ identifier === 'present_address' ? $t('current_address') : $t('permanent_address')  }}
    </div>
</template>

<script>
export default {
    name: "AddressTitle",
    props: {
        identifier: {
            type: String,
            default: 'present_address'
        }
    }
}
</script>