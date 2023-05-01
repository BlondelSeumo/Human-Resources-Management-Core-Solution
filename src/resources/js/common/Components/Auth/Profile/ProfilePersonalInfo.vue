<template>
    <form ref="form" data-url='admin/auth/users/change-settings' v-if="!preloader">
        <app-form-group
            :disabled="!editField"
            page="page"
            :label="$t('first_name')"
            type="text"
            id="input-text-first-name"
            :placeholder="$placeholder('first_name', '')"
            v-model="userProfileInfo.first_name"
            :error-message="$errorMessage(errors, 'first_name')"
        />

        <app-form-group
            :disabled="!editField"
            page="page"
            :label="$t('last_name')"
            type="text"
            id="input-text-last-name"
            :placeholder="$placeholder('last_name')"
            v-model="userProfileInfo.last_name"
            :error-message="$errorMessage(errors, 'last_name')"
        />

        <app-form-group
            :disabled="!editField"
            page="page"
            :label="$t('email')"
            type="email"
            id="input-text-email"
            :placeholder="$placeholder('email', '')"
            v-model="userProfileInfo.email"
            :error-message="$errorMessage(errors, 'email')"
        />

        <app-form-group
            v-if="userProfileInfo.profile"
            :disabled="!editField"
            page="page"
            :label="$t('gender')"
            type="radio"
            :list="[
                {id:'male',value: $t('male')},
                {id:'female', value:  $t('female')}
            ]"
            v-model="userProfileInfo.profile.gender"
            :error-message="$errorMessage(errors, 'gender')"

        />

        <app-form-group
            v-if="userProfileInfo.profile"
            :disabled="!editField"
            page="page"
            :label="$fieldTitle('contact', 'number')"
            type="tel-input"
            id="input-text-contact"
            :placeholder="userProfileInfo.profile.contact ? $placeholder('contact', 'number') : this.$t('not_added_yet')"
            v-model="userProfileInfo.profile.contact"
            :error-message="$errorMessage(errors, 'contact')"
        />

        <app-form-group
            v-if="userProfileInfo.profile"
            :disabled="!editField"
            page="page"
            :label="$t('address')"
            type="text"
            id="input-text-address"
            :placeholder="userProfileInfo.profile.address ? $placeholder('address') : this.$t('not_added_yet')"
            v-model="userProfileInfo.profile.address"
            :error-message="$errorMessage(errors, 'address')"
        />

        <app-form-group
            v-if="userProfileInfo.profile"
            :disabled="!editField"
            page="page"
            :label="$t('date_of_birth')"
            type="date"
            v-model="userProfileInfo.profile.date_of_birth"
            :placeholder="userProfileInfo.profile.date_of_birth ? $placeholder('date_of_birth') : this.$t('not_added_yet')"
            :error-message="$errorMessage(errors, 'date_of_birth')"
        />

        <app-form-group
            v-if="userProfileInfo.profile"
            page="page"
            :label="$t('about_me')"
            :disabled="!editField"
            type="textarea"
            v-model="userProfileInfo.profile.about_me"
            :placeholder="$textAreaPlaceHolder('about_me','')"
            :error-message="$errorMessage(errors, 'about_me')"
        />

        <div class="form-group mt-5 mb-0" v-if="editField">
            <app-submit-button @click="submitData" :title="$t('save')" :loading="loading"/>
            <button class="btn btn-secondary ml-2" @click.prevent="refreshData">{{ this.$t('cancel') }}</button>
        </div>
    </form>
    <app-pre-loader class="p-primary" v-else />
</template>

<script>
    import moment from 'moment'
    import FormHelperMixins from "../../../Mixin/Global/FormHelperMixins";

    export default {
        name: "ProfilePersonalInfo",
        mixins: [FormHelperMixins],
        props: {
            id: {
                type: String
            }
        },
        data() {
            return {
                userProfileInfo: {},
                editField: false,
            }
        },
        methods: {
            submitData() {
                let profile = this.userProfileInfo;
                this.loading = true;
                profile.gender = this.userProfileInfo.profile.gender;
                profile.contact = this.userProfileInfo.profile.contact;
                profile.address = this.userProfileInfo.profile.address;
                profile.about_me = this.userProfileInfo.profile.about_me;
                profile.date_of_birth = this.userProfileInfo.profile.date_of_birth ? moment(this.userProfileInfo.profile.date_of_birth).format('YYYY-MM-DD') : '';
                this.save(profile);
            },
            afterSuccess(response) {
                this.loading = false;
                this.$toastr.s('', response.data.message);
                this.scrollToTop(false)
                setTimeout(() => location.reload())
            },

            cancelUser() {
                location.reload();
            },
            refreshData(){
                this.$store.dispatch('getUserInformation');
                this.preloader = true;
                this.editField = false;
            }
        },
        computed: {
            userInfo() {
                return this.$store.getters.getUserInformation
            }
        },
        mounted() {
            this.$store.dispatch('getUserInformation');
            this.preloader = true;

            this.$hub.$on('headerButtonClicked-' + this.id, (component) => {
                this.editField = true;
            })
        },

        watch: {
            userInfo: {
                handler: function (user) {
                    this.userProfileInfo = {
                        ...user,
                        profile: {
                            ...user.profile,
                            date_of_birth: user.profile && user.profile.date_of_birth ? new Date(user.profile.date_of_birth) : ''
                        }
                    };
                    if (user) this.preloader = false
                },
                deep: true
            }
        }

    }
</script>


