<template>
    <div class="modal fade"
         :style="'cursor: url('+cursorImage()+'), auto !important'"
         :id="modalId" tabindex="-1"
         role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body d-flex flex-column justify-content-center modal-alert">
                    <app-pre-loader v-if="loading"/>
                    <template v-else>
                        <div class="text-center">
                            <app-icon :class="'text-'+(modalClass?modalClass:'danger')" :name="icon?icon:'x-circle'"/>
                        </div>
                        <h5 class="text-center font-weight-bold mt-4">{{ title ? title : $t('are_you_sure') }}</h5>
                        <p v-if="subTitle" class="text-center font-size-90 m-0 p-0">{{ subTitle }}</p>
                        <p class="mb-primary text-center font-size-90 p-0">
                            {{ message ? message : $t('this_content_will_be_deleted_permanently') }}</p>
                        <div class="text-center">
                            <a v-if="!hideSecondButton"
                               href="#"
                               class="btn btn-secondary mr-2"
                               data-dismiss="modal"
                               @click.prevent="cancelled">
                                {{ secondButtonName ? secondButtonName : $t('no') }}
                            </a>
                            <a v-if="!hideFirstButton"
                               href="#"
                               class="btn" :class="'btn-'+(modalClass?modalClass:'danger')"
                               :data-dismiss="selfClose ? 'modal' : ''"
                               @click.prevent="confirmed">
                                {{ firstButtonName ? firstButtonName : $t('yes') }}
                            </a>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import AppFunction from "../../helpers/app/AppFunction";

export default {
    name: "AppConfirmationModal",
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
        },
        title: String,
        subTitle: String,
        icon: String,
        modalClass: String,
        selfClose: {
            type: Boolean,
            default: true
        },
        loading: {
            type: Boolean,
            default: false
        },
        hideFirstButton: {
            type: Boolean,
            default: false
        },
        hideSecondButton: {
            type: Boolean,
            default: false
        }
    },
    mounted() {
        $('#' + this.modalId).on('hidden.bs.modal', (e) => {
            this.closed();
        });
        $('#' + this.modalId).modal('show');
    },
    methods: {
        cursorImage() {
            if (this.$store.state.theme.darkMode) {
                return AppFunction.getAppUrl('images/close-cursor-dark.png');
            } else {
                return AppFunction.getAppUrl('images/close-cursor-light.png');
            }
        },
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
        closed() {
            this.$emit('cancelled', false);
        }
    }
}
</script>

