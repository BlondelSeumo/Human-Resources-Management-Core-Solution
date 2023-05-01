<template>
    <div>
        <div style="min-height: 100px">
            <app-form-group
                type="textarea"
                page="page"
                :label="$fieldLabel('note', '')"
                :placeholder="$textAreaPlaceHolder('note')"
                v-model="formData.note"
                :required="true"
                cols="5"
                :error-message="$errorMessage(errors, 'note')"
            />
        </div>

        <div class="form-group mt-4">
            <div class="row">
                <div class="col-12">
                    <button type="button" @click.prevent="goBack" class="btn btn-secondary mr-2">{{ this.$t('back') }}</button>
                    <button type="button" @click.prevent="goNext" class="btn btn-primary">{{ this.$t('next') }}</button>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
import moment from "moment";
import ManualPayrunErrorMixin from "../../../Mixins/ManualPayrunErrorMixin";
import {formatDateForServer} from "../../../../../common/Helper/Support/DateTimeHelper";

export default {
    name: "ManualPayrunNoteWizard",
    mixins: [ManualPayrunErrorMixin],
    data() {
        return {
            formData: {

            },
        }
    },
    props: {
        props: {
            default: ''
        }
    },
    mounted() {
        this.formData = this.$store.state.payrun.payrunData
    },
    methods: {
        goBack() {
            this.$emit('back', true);
        },
        goNext() {
            this.$store.dispatch('setPayrunData', {
                note: this.formData.note
            });

            this.$emit('next', true);
        }
    },
    computed: {
        statePayrunData(){
            return this.$store.state.payrun.payrunData
        },
    },

    watch: {
        statePayrunData: {
            handler: function (data){
                this.formData = data;
            },
            immediate: true
        },
    }
}
</script>

