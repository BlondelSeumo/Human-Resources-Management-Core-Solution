<template>
    <div>
        <app-overlay-loader v-if="preloader"/>
        <template v-else>
            <single-address :address="employeeAddress.permanent_address"
                            type="permanent_address"
                            @add="openModal"
                            @edit="openModal"
                            @delete="getConfirmations"
            />

            <single-address :address="employeeAddress.present_address"
                            type="present_address"
                            @add="openModal"
                            @edit="openModal"
                            @delete="getConfirmations"
            />
        </template>

        <app-employee-address-details-model
            v-if="isModalActive"
            v-model="isModalActive"
            :title="title"
            :address="getAddress(title)"
            @close="isModalActive = false"
            @reload="getAddresses()"
            :url="url"
        />

        <app-confirmation-modal
                v-if="confirmationModalActive"
                icon="trash-2"
                modal-id="app-confirmation-modal"
                @confirmed="deleteModal"
                @cancelled="cancelled"
                @reload="getAddresses()"
        />
    </div>
</template>

<script>
    import {axiosGet, axiosDelete} from "../../../../../../common/Helper/AxiosHelper";
import SingleAddress from "./Components/SingleAddress";

export default {
    name: "AppAddressDetails",
    props: ['props'],
    components: {SingleAddress},
    data() {
        return {
            confirmationModalActive: false,
            addresses: [],
            preloader: true,
            isModalActive: false,
            title: '',
        }
    },
    mounted() {
        this.getAddresses();
    },
    methods: {
        getAddresses() {
            axiosGet(this.url).then(({data}) => {
                this.addresses = data;
            }).finally(() => {
                this.preloader = false;
            });
        },
        openModal(type) {
            this.isModalActive = true;
            this.title = type;
        },
        getAddress(type) {
            return this.addresses.find(address => address.key === type) || {}
        },
        deleteModal() {
            axiosDelete(`${this.url}/${this.title}`).then(({data}) => {
                this.$toastr.s('', data.message);
                this.$emit('reload')
                this.confirmationModalActive = false;
            }).catch((error) => {
                if (error.response)
                    this.toastException(error.response.data)
            });
        },
        getConfirmations(type){
            this.title = type;
            this.confirmationModalActive = true;
        },
        cancelled() {
            this.confirmationModalActive = false;
        },
    },
    computed: {
        employeeAddress() {
            return  {
                present_address: this.getAddress('present_address'),
                permanent_address: this.getAddress('permanent_address'),
            };
        },
        url() {
            return `${this.apiUrl.EMPLOYEES}/${this.props.id}/addresses`;
        }
    }
}
</script>