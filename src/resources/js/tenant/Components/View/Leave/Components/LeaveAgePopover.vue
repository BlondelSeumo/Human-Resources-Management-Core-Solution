<template>
    <div class="popover-leave-age" v-click-outside="popperHidden">
        <popper
            trigger="clickToToggle"
            :force-show="showPopper"
            @show="popperVisible"
            @hide="popperHidden"
            :options="{
              placement: 'top'
            }">
            <div class="popper">
                <div class="popover-body">
                    <div class="rounded p-primary">
                        <p class="default-font-color mb-0">
                            {{ this.$t(leave.duration_type) }}
                        </p>
                        <p class="text-muted mb-0">
                            <leave-date-and-time :row-data="leave" />
                        </p>
                        <hr class="my-2"/>
                        <p class="text-muted">
                            {{ comment.comment }}
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <div v-for="attachment in leave.attachments">
                                <a :href="urlGenerator(attachment.path)" target="_blank">
                                    <i data-feather="external-link"/>
                                </a>
                            </div>
                            <div>
                                <a href="#" @click.prevent="openResponseLogModal">
                                    <app-icon name="activity"/>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button slot="reference" :class="`leave-age ${getBtnClass(leave.duration_type)} ${getPendingClass()}`"/>
        </popper>
    </div>
</template>

<script>
import Popper from 'vue-popperjs';
import 'vue-popperjs/dist/vue-popper.css';
import {leaveDateAndTimeFormat} from "../Helper/Helper";
import LeaveDateAndTime from "./LeaveDateAndTime";
import CoreLibrary from "../../../../../core/helpers/CoreLibrary";
import {urlGenerator} from '../../../../../common/Helper/AxiosHelper';

export default {
    name: "LeaveAgePopover",
    components: {LeaveDateAndTime, Popper},
    extends: CoreLibrary,
    props: {
        leave: {

        },
        active: {

        },
        range: {

        }
    },
    data () {
        return {
            urlGenerator,
            showPopper: false,
            btnClass: {
                first_half: 'age-length-half',
                last_half: 'age-length-half',
                single_day: 'age-length-full',
                multi_day: 'age-length-full',
                hours: 'age-length-quarter'
            }
        }
    },
    watch: {
        active: {
            handler: function (active) {
                if (active !== `popper-${this.leave.id}-${this.range}`) {
                    this.showPopper = false;
                }
            }
        }
    },
    methods: {
        openResponseLogModal() {
            this.showPopper = false;
            this.$emit('openResponseLogModal', this.leave.id);
            this.$emit('activePopper', 0);
        },
        popperVisible() {
            this.showPopper = true;
            this.$emit('activePopper', `popper-${this.leave.id}-${this.range}`);
        },
        popperHidden() {
            this.showPopper = false
        },
        getBtnClass(duration_type) {
            return this.btnClass[duration_type] || ''
        },
        getPendingClass() {
            return this.leave.status?.name === 'status_pending' ? 'bg-pending' : '';
        },
    },
    computed: {
        leaveDateTime(){
            return leaveDateAndTimeFormat(this.leave.start_at, this.leave.end_at)
        },
        comment() {
            return this.leave.comments && this.leave.comments.length ? this.leave.comments[0] : {};
        }
    }
}
</script>

<style lang="scss">
.popper {
    border: none;
    max-width: 25rem;
    border-radius: 7px;
    text-align: initial;
    box-shadow: var(--default-box-shadow);
    background-color: var(--default-card-bg);

    &[x-placement^="top"],
    &[x-placement^="bottom"] {
        .popper__arrow {
            border-color: var(--default-card-bg);
        }
    }

    .popover-header {
        display: none;
    }

    .popover-body {
        z-index: 2;
        padding: 0;
        border: none;
    }
}
</style>