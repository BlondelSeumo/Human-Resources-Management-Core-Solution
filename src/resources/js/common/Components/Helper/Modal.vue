<template>
    <app-modal v-if="value"
               :modal-id="id"
               :modal-size="size"
               :modal-alignment="alignModal"
               :modalScroll="scrollable"
               :modal-backdrop="modalBackdrop"
               @close-modal="$emit('input', false)">
        <slot v-if="!hideHeader" name="header">
            <div class="modal-header">
                <h4 class="modal-title">{{ title }}</h4>
                <button v-if="closeButton" type="button" class="close outline-none" aria-label="Close"
                        data-dismiss="modal"
                        @click="$emit('input', false)">
                   <span>
                       <app-icon name="x"></app-icon>
                   </span>
                </button>
            </div>
        </slot>
        <div :class="`modal-body custom-scrollbar ${bodyClass}`" v-if="!preloader">
            <slot></slot>
        </div>
        <div v-else class="modal-body custom-scrollbar">
            <app-pre-loader/>
        </div>
        <div v-if="!hideFooter && !preloader" class="modal-footer">
            <slot name="footer">
                <slot name="footer-space"></slot>
                <button type="button"
                        v-if="!hideCancelButton"
                        class="btn btn-secondary mr-2"
                        data-dismiss="modal"
                        @click="$emit('input', false)">
                    {{ cancelBtnLabel }}
                </button>&nbsp;
                <app-submit-button
                    :loading="loading"
                    v-if="!hideSubmitButton"
                    :btn-class="submitBtnClass"
                    :label="btnLabel"
                    @click="$emit('submit')"
                />
            </slot>
        </div>
    </app-modal>
</template>

<script>
export default {
    name: "ModalComponent",
    props: {
        title: {
            required: false,
            type: String
        },
        size: {
            required: false,
            default: function () {
                return 'default';
            }
        },
        alignModal: {
            type: String,
            default: 'top'
        },
        hideFooter: {
            default: function () {
                return false;
            }
        },
        hideHeader: {
            default: function () {
                return false;
            }
        },
        hideCancelButton: {
            default: false
        },
        hideSubmitButton: {
            default: false
        },
        closeButton: {
            default: true
        },
        modalBackdrop: {
            default: true
        },
        value: {
            type: Boolean
        },
        loading: {
            default: function () {
                return false
            }
        },
        id: {
            default: 'app-modal'
        },
        preloader: {
            default: false
        },
        scrollable: {
            type: Boolean,
            default: true
        },
        bodyClass: {},
        btnLabel: {
            type: String,
            default: function () {
                return this.$t('save')
            }
        },
        cancelBtnLabel: {
            type: String,
            default: function () {
                return this.$t('cancel')
            }
        },
        submitBtnClass: {}
    },
}
</script>
