<template>
    <div :id="'dropdown-'+details.id"
         class="dropdown keep-inside-clicks-open dropdown-note-editor d-inline-block">
        <button type="button"
                class="btn p-0 mb-1 primary-text-color"
                data-toggle="dropdown">
            <app-icon name="map-pin" class="size-18"/>
        </button>
        <div class="dropdown-menu p-primary mt-1">
            <div>
                <p class="text-secondary">
                    {{ $t('geo_location_data') }}
                </p>
                <div class="note note-warning custom-scrollbar p-4 mb-5">
                    <p class="m-0" v-if="value.ip"><label class="font-weight-bold">IP:</label> {{ value.ip }}</p>
                    <p class="m-0" v-if="value.location"><label class="font-weight-bold">Location:</label> {{ value.location }}</p>
                    <p class="m-0" v-if="value.city"><label class="font-weight-bold">City:</label> {{ value.city }}</p>
                    <p class="m-0" v-if="value.state_prov"><label class="font-weight-bold">State:</label> {{ value.state_prov }}</p>
                    <p class="m-0" v-if="value.country_name"><label class="font-weight-bold mb-0">Country:</label> {{ value.country_name }}</p>
                </div>

                <div class="text-right">

                    <a href="#"
                       class="btn btn-secondary"
                       @click.prevent="closeDropDown">
                        {{ $t('close') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

export default {
    name: "PunchGeolocationData",
    props: {
        details: {},
        keyData: {
            default: () => 'in_ip_data'
        }
    },

    computed: {
        value() {
            return this.details[this.keyData] ? JSON.parse(this.details[this.keyData]) : {};
        }
    },

    methods: {
        closeDropDown() {
            $(".dropdown-menu").removeClass('show')
        },
    }

}
</script>

<style scoped lang="scss">
.dropdown {
    .dropdown-menu {
        width: 350px;

        .note {
            overflow-y: auto;
            max-height: 200px;
        }
    }
}
</style>