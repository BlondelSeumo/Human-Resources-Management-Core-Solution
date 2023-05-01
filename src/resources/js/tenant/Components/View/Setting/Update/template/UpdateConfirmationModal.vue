<template>
    <app-modal :modal-id="modalId" modal-alignment="center" modal-size="md">
        <div class="modal-body d-flex flex-column justify-content-center modal-alert">
            <div class="text-center">
                <app-icon class="text-warning" :name="'alert-triangle'"/>
            </div>
            <h5 class="text-warning text-center font-weight-bold mt-4">{{ $t('are_you_sure') }}</h5>
            <p class="mb-4 text-center">{{ message ? message : $t('this_will_update_entire_app')}}</p>
            <div class="text-center">
                <a href="#" class="btn btn-secondary" data-dismiss="modal" @click.prevent="cancelled">
                    {{ secondButtonName ? secondButtonName : $t('no') }}
                </a>
                <a href="#" class="btn btn-warning ml-2" data-dismiss="modal" @click.prevent="confirmed">
                    {{ firstButtonName ? firstButtonName : $t('yes') }}
                </a>
            </div>
        </div>
    </app-modal>
</template>


<script>
    export default {
        name: "UpdateConfirmationModal",
        props: {
            modalId: {
                type: String,
                required: true
            },
            message: {
                type: String
            },
            firstButtonName: {
                type: String
            },
            secondButtonName: {
                type: String
            }
        },
        mounted() {
            $('#' + this.modalId).on('hidden.bs.modal', (e) => {
                this.closed();
            });
            $('#' + this.modalId).modal('show');
        },
        methods: {
            /**
             * $emit for confirmed opration
             */
            confirmed() {
                this.$emit('confirmed', false);
            },
            /**
             * $emit for cancelled opration
             */
            cancelled() {
                this.closed();
            },
            closed(){
                this.$emit('cancelled', false);
            }
        }
    }
</script>

