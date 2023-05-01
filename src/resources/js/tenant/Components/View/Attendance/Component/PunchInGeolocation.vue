<template>
    <app-punch-geolocation-data
        v-if="inDetails.in_ip_data"
        :details="inDetails"
    />
    <div v-else>-</div>
</template>

<script>

export default {
    name: "PunchInGeolocation",
    props: {
        value: {},
        rowData: {},
        tableId: {},
    },

    computed: {
        inDetails(){
            return this.collection(this.value).last();
        }
    },
}
</script>

