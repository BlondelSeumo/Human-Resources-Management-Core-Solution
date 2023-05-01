<template>
    <app-punch-geolocation-data
        v-if="outDetails.out_ip_data"
        :details="outDetails"
        key-data="out_ip_data"
    />
    <div v-else>-</div>
</template>

<script>

export default {
    name: "PunchOutGeolocation",
    props: {
        value: {},
        rowData: {},
        tableId: {},
    },

    computed: {
        outDetails(){
            return this.collection(this.value).first();
        }
    },
}
</script>

