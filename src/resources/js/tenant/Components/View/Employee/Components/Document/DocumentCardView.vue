<template>
    <div class="row" v-if="data.length > 0">
        <div class="col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-primary"
             v-for="(document, rowIndex) in data" :key="'row'+rowIndex">
            <div class="card card-with-shadow h-100 employee-preview-card border custom-border-width">
                <div class="card-body position-relative d-flex flex-column justify-content-between">
                    <div v-if="showAction" class="dropdown options-dropdown position-absolute">
                        <button type="button"
                                class="btn-option btn d-flex align-items-center justify-content-center"
                                data-toggle="dropdown">
                            <app-icon name="more-horizontal"/>
                        </button>
                        <div class="dropdown-menu dropdown-menu-right py-2 mt-1">
                            <template
                                v-for="(action,index) in actions">
                                <a class="dropdown-item px-4 py-2"
                                   href="#"
                                   :key="index"
                                   v-if="checkIfVisible(action, document)"
                                   @click.prevent="callAction(action, document)">
                                    {{ action.title }}
                                </a>
                            </template>
                        </div>
                    </div>
                    <div class="pt-5">
                        <h6 class="mb-4">
                            {{ document.name }}
                        </h6>

                        <a class="btn btn-info btn-sm" :href="urlGenerator(document.path)" target="_blank">
                            <app-icon name="external-link" class="size-16"/>
                            {{ $t('view') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>

import {urlGenerator} from "../../../../../../common/Helper/AxiosHelper";
import CoreLibrary from "../../../../../../core/helpers/CoreLibrary";

export default {
    name: "DocumentCardView",
    extends: CoreLibrary,
    props: {
        data: {
            type: Array,
            required: true
        },
        showAction: {
            type: Boolean,
            default: true
        },
        actions: {
            type: Array
        }
    },
    data() {
        return {
            urlGenerator,
        }
    },
    methods: {
        callAction(action, document) {
            this.$emit('getCardAction', document, action, true);
        },
        checkIfVisible(action, employee) {
            if (this.isFunction(action.modifier)) {
                return action.modifier(employee);
            }
            return true;
        },
    }
}
</script>
<style scoped>
.card.custom-border-width {
    border-width: 2px !important;
}
</style>
