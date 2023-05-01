<template>
    <div :id="'dropdown-'+rowData.id"
         class="dropdown keep-inside-clicks-open dropdown-note-editor d-inline-block">
        <button type="button"
                class="btn p-0 primary-text-color"
                data-toggle="dropdown">
            <app-icon name="file-text" :class="iconClass"/>
        </button>
        <div class="dropdown-menu p-primary mt-1">
            <div>
                <p class="text-secondary">
                    {{ noteTitle }}
                </p>
                <div v-if="!isEditNote" class="note note-warning custom-scrollbar p-4 mb-5">
                    <p class="text-muted">
                        {{ formData.description ? formData.description : noteDescription }}
                    </p>
                </div>
                <div v-else class="mb-5">
                    <form class="position-relative mb-0"
                          :class="{'loading-opacity': loading}"
                          ref="form"
                          :data-url="url">
                        <app-overlay-loader v-if="loading"/>
                        <app-input
                            type="textarea"
                            v-model="formData.description"
                            :text-area-rows="4"
                            :required="true"
                            :error-message="$errorMessage(errors, 'description')"
                            :placeholder="$t('add_note_here')"
                        />
                    </form>
                </div>
                <div class="text-right">
                    <template v-if="isEditNote">
                        <a href="#"
                           class="btn btn-secondary mr-1"
                           :class="{'disabled': loading}"
                           @click.prevent="closeDropDown">
                            {{ $t('cancel') }}
                        </a>
                        <a href="#"
                           class="btn btn-primary mr-1"
                           :class="{'disabled': loading}"
                           @click.prevent="submitData">
                            {{ $t('save') }}
                        </a>
                    </template>
                    <template v-else>
                        <a href="#"
                           v-if="editPermission"
                           class="btn btn-link mr-1"
                           @click.prevent="toggleEditable">
                            {{ $t('edit') }}
                        </a>
                        <a href="#"
                           class="btn btn-secondary"
                           @click.prevent="closeDropDown">
                            {{ $t('close') }}
                        </a>
                    </template>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import FormHelperMixins from "../../../common/Mixin/Global/FormHelperMixins";

export default {
    name: "AppNoteEditor",
    mixins: [FormHelperMixins],
    props: {
        id: {},
        url: {},
        rowData: {},
        noteTitle: {
            default: function () {
                return this.$t('default_note_title');
            }
        },
        noteDescription: {
            default: function () {
                return this.$t('default_note_description');
            }
        },
        method: {
            default: 'PATCH'
        },
        iconClass: {
            default: 'size-18'
        },
        editPermission: {
            default: true
        }
    },
    data() {
        return {
            isEditNote: false,
            formData: {
                _method: this.method
            }
        }
    },
    mounted() {
        $('#dropdown-' + this.rowData.id).on('hidden.bs.dropdown', () => {
            this.isEditNote = false;
        })
    },
    methods: {
        closeDropDown() {
            this.isEditNote = false;
            $(".dropdown-menu").removeClass('show')
        },
        toggleEditable() {
            this.isEditNote = !this.isEditNote
        },
        afterSuccess({data}) {
            this.loading = false;
            this.closeDropDown();
            this.toastAndReload(data.message);
        },
    },
    watch: {
        noteDescription: {
            handler: function (noteDescription) {
                if (!this.formData.description) {
                    this.formData.description = noteDescription;
                }
            },
            immediate: true
        }
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