<template>
    <div>
        <app-overlay-loader v-if="preloader || loader"/>
        <div v-else>
            <draggable :forceFallback="true"
                       tag="div"
                       v-model="socialLinks"
                       v-bind="dragOptions"
                       @start="dragstart($event)"
                       @end="dragend">
                <div class="row align-items-center py-half-primary"
                     v-for="(socialLink, index) in socialLinks"
                     :key="'social-link'+index">
                    <div class="col-4 col-lg-4">
                        <div class="d-flex align-items-center text-capitalize">
                            <div class="cursor-grab mr-3">
                                <app-icon name="align-justify" class="text-muted"/>
                            </div>
                            <div>
                                <div class="icon-box mr-2" :key="Object.keys(socialLink)[0]">
                                    <app-icon :name="Object.keys(socialLink)[0]"/>
                                </div>
                            </div>
                            {{ $t(Object.keys(socialLink)[0]) }}
                        </div>
                    </div>
                    <div class="col-8 col-lg-4" v-if="!editable[Object.keys(socialLink)[0]]">
                        <div v-if="Object.values(socialLink)[0]" :key="'with-link-'+index" class="ml-auto ml-lg-0"
                             style="max-width: 310px;">
                            <p class="mb-0 text-truncate">{{ Object.values(socialLink)[0] }}</p>
                        </div>
                        <p class="text-right text-lg-left mb-0" v-else>{{ $t('not_added_yet') }}</p>
                    </div>
                    <div class="col-8 col-lg-7" v-if="editable[Object.keys(socialLink)[0]]">
                        <form method="post" @submit.prevent="update">
                            <div :key="'without-link-'+index">
                                <div :key="'without-link-edit-'+index"
                                     class="d-flex align-items-center justify-content-between">
                                    <div class="flex-grow-1 mr-1">
                                        <app-input :id="'social-link-'+Object.keys(socialLink)[0]"
                                                   class="mr-2"
                                                   type="text"
                                                   v-model="formData.value"/>
                                        <app-message type="error" :message="$errorMessage(errors, 'value')"/>
                                    </div>
                                    <div class="mr-1">
                                        <app-submit-button :label="$t('save')" @click="addSocialLink(index)"/>
                                    </div>
                                    <div class="">
                                        <a href="#" class="text-muted"
                                           v-if="$can('update_employee_social_links')"
                                           @click.prevent="editToggle(Object.keys(socialLink)[0], false)">
                                            <app-icon name="x"/>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-3">
                        <div v-if="!Object.values(socialLink)[0] && !editable[Object.keys(socialLink)[0]]"
                             :key="'without-link-show-'+index" class="text-right">
                            <button type="submit"
                                    class="btn btn-primary"
                                    @click.prevent="editToggle(Object.keys(socialLink)[0])"
                                    v-if="$can('update_employee_social_links')">
                                {{ $t('add') }}
                            </button>
                        </div>
                        <div v-else-if="!editable[Object.keys(socialLink)[0]]" class="text-right mt-3 mt-lg-0">
                            <div class="btn-group btn-group-action" role="group">
                                <button class="btn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        :title="$t('edit')"
                                        @click="editToggle(Object.keys(socialLink)[0])"
                                        v-if="$can('update_employee_social_links')">
                                    <app-icon name="edit"/>
                                </button>
                                <button class="btn"
                                        data-toggle="tooltip"
                                        data-placement="top"
                                        :title="$t('remove_link')"
                                        @click="openDeleteModal(Object.keys(socialLink)[0])"
                                        v-if="$can('update_employee_social_links')">
                                    <app-icon name="trash-2"/>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </draggable>
        </div>

        <app-confirmation-modal
            v-if="confirmationModalActive"
            icon="trash-2"
            modal-id="app-confirmation-modal"
            @confirmed="deleteSocialLink"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
import draggable from 'vuedraggable';
import {axiosPatch} from "../../../../../../common/Helper/AxiosHelper";
import FormHelperMixins from '../../../../../../common/Mixin/Global/FormHelperMixins';
import {mapState} from 'vuex'

export default {
    name: "SocialLinks",
    components: {draggable},
    mixins: [FormHelperMixins],
    props: ['props'],
    data() {
        return {
            editable: {},
            confirmationModalActive: false,
        }
    },
    mounted() {
        // Initialize bootstrap tooltip
        setTimeout(function () {
            $('[data-toggle="tooltip"]').tooltip();
        }, 5000);
    },
    computed: {
        url() {
            return `${this.apiUrl.EMPLOYEES}/${this.props.id}/social-links`;
        },
        dragOptions() {
            return {
                animation: 300
            };
        },
        ...mapState({
            loader: state => state.employees.socialLinkLoader,
            socialLinks: state => state.employees.socialLinks
        })
    },
    methods: {
        deleteSocialLink() {
            this.sendRequest();
        },
        openDeleteModal(key) {
            this.formData.value = null;
            this.formData.key = key;
            this.confirmationModalActive = true;
        },
        cancelled() {
            this.confirmationModalActive = false;
        },
        editToggle(key, value = true) {
            this.editable = {};
            this.$set(this.editable, key, value)
            this.formData = {
                key,
                value: this.socialLinks.find(link => Object.keys(link)[0] === key)[key]
            }
            setTimeout(() => {
                $('#social-link-' + key).focus();
            });
        },
        addSocialLink(index) {
            this.socialLinks[index].edit = false;
            this.update(this.socialLinks[index]);
        },
        update() {
            this.preloader = true;
            this.sendRequest();
        },
        sendRequest() {
            axiosPatch(this.url, this.formData).then(response => {
                this.toastAndReload(response.data.message);
                this.$set(this.editable, this.formData.key, false);
                return this.$store.dispatch('getEmployeeSocialLinks', this.props.id)
            }).catch(error => {
                if (error.response) {
                    this.errors = error.response.data.errors;
                }
            }).finally(() => {
                this.preloader = false;
            })
        },
        dragstart(ev) {
            this.drag = true;
            ev.item.classList.add('catch-item');
            ev.target.classList.add('catch-container');
        },
        dragend(ev) {
            this.drag = false;
            ev.item.classList.remove('catch-item');
            ev.target.classList.remove('catch-container');
        }
    }
}
</script>