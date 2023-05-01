<template>
    <div>
        <app-overlay-loader v-if="preloader"/>
        <template v-else>
            <div class="row mb-primary" v-for="emergencyContact in emergencyContacts">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3 mb-lg-0">
                        <div>
                            <div class="icon-box mr-2">
                                <app-icon name="user"/>
                            </div>
                        </div>
                        {{ emergencyContact.value.name }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <template v-if="emergencyContact.value">
                        <contact-details :value="emergencyContact.value" identifier="relationship" icon="users"/>
                        <contact-details :value="emergencyContact.value" identifier="phone_number" icon="phone"/>
                        <contact-details :value="emergencyContact.value" identifier="email" icon="mail"/>
                        <div v-if="emergencyContact.value.area || emergencyContact.value.country || emergencyContact.value.details" class="d-flex">
                            <div class="mr-2">
                                <app-icon
                                    name="map-pin"
                                    class="primary-text-color"
                                    width="16"
                                    height="16"
                                />
                            </div>
                            <div>
                                <p v-if="$optional(emergencyContact, 'value', 'details')" class="mb-0">{{ emergencyContact.value.details }}</p>
                                <p class="mb-0">
                                    {{ emergencyContact.value.area }}
                                    <template v-if="emergencyContact.value.area && emergencyContact.value.country">,</template>
                                    {{ emergencyContact.value.country }}
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="col-lg-3">
                    <div class="text-right mt-3 mt-lg-0">
                        <div class="btn-group btn-group-action" role="group"
                             aria-label="Default action">
                            <button class="btn"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    :title="$t('edit')"
                                    v-if="$can('update_employee_emergency_contacts')"
                                    @click="editModal(emergencyContact.id)">
                                <app-icon name="edit"/>
                            </button>
                            <button class="btn"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    :title="$t('delete')"
                                    v-if="$can('delete_employee_emergency_contacts')"
                                    @click="getConfirmations(emergencyContact.id)">
                                <app-icon name="trash-2"/>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row align-items-center" v-if="$can('create_employee_emergency_contacts')">
                <div class="col-lg-4">
                    <div class="d-flex align-items-center mb-3 mb-lg-0">
                        <div>
                            <div class="icon-box mr-2">
                                <app-icon name="user-plus"/>
                            </div>
                        </div>
                        {{ $t('emergency_contact') }}
                    </div>
                </div>
                <div class="col-lg-4">
                    <p class="text-muted mb-0">
                        {{ $t('you_can_add_multiple_contacts') }}
                    </p>
                </div>
                <div class="col-lg-3">
                    <div class="text-right">
                        <button class="btn btn-primary" @click="addModal" >
                            {{ $t('add') }}
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <app-employee-emergency-contact-model
            v-if="isModalActive"
            v-model="isModalActive"
            @close="isModalActive = false"
            :selected-url="selectedUrl"
            @reload="getEmergencyContacts"
            :url="url"
        />
        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="deleteModal"
            @cancelled="cancelled"
            :reload="getEmergencyContacts()"
        />
    </div>
</template>

<script>
import {axiosGet, axiosDelete} from "../../../../../../common/Helper/AxiosHelper";
import ContactDetails from "./Components/ContactDetails";

export default {
    name: "EmergencyContacts",
    components: {ContactDetails},
    props: ['props'],
    data() {
        return {
            confirmationModalActive: false,
            preloader: true,
            emergencyContacts: [],
            isModalActive: false,
            selectedUrl: '',
            deletedUrl: ''
        }
    },
    mounted() {
        this.getEmergencyContacts();
    },
    methods: {
        getEmergencyContacts() {
            axiosGet(this.url).then(({data}) => {
                this.emergencyContacts = data;
            }).finally(() => {
                this.preloader = false;
            });
        },
        openModal() {
            this.isModalActive = true;
        },
        addModal() {
            this.selectedUrl = '';
            this.openModal();
        },
        editModal(contactId) {
            this.selectedUrl = `${this.url}/${contactId}`;
            this.openModal();
        },
        deleteModal() {
            axiosDelete(this.deletedUrl).then(({data}) => {
                this.$toastr.s('', data.message);
                this.deletedUrl = '';
                this.confirmationModalActive = false;
                this.$emit('reload');
            }).catch((error) => {
                if (error.response)
                    this.toastException(error.response.data)
            });
        },
        getConfirmations(contactId) {
            this.deletedUrl = `${this.url}/${contactId}`;
            this.confirmationModalActive = true;
        },
        cancelled() {
            this.confirmationModalActive = false;
        },
    },
    computed: {
        url() {
            return `${this.apiUrl.EMPLOYEES}/${this.props.id}/emergency-contacts`;
        }
    }
}
</script>