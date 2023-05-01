<template>
    <div class="custom-image-upload-wrapper" :class="data.wrapperClass">
        <div class="image-area d-flex">
            <img v-if="imageUrl"
                 :src="imageUrl"
                 alt=""
                 class="img-fluid mx-auto my-auto"/>
        </div>
        <div class="input-area">
            <label
                id="upload-label"
                :for="inputFieldId">
                {{ data.label }}
            </label>
            <input
                :id="inputFieldId"
                type="file"
                :disabled="data.disabled"
                v-on="listeners"
                class="form-control d-none"
            />
        </div>
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';
import {FileUploaderMixin} from './mixin/FileUploaderMixin.js';

export default {
    name: "CustomImageUpload",
    mixins: [InputMixin, FileUploaderMixin],
    data() {
        return {
            imageUrl: ''
        }
    },
    watch: {
        value: {
            handler: 'initComponent',
            immediate: true
        }
    },
    methods: {
        getFile(event) {
            let file = event.target.files[0];
            this.fieldValue = file;
            if (file) this.imageUrl = URL.createObjectURL(file);
            this.changed();
        },
        changed() {
            this.$emit('input', this.fieldValue);
        },
        initComponent() {
            if (typeof this.value == 'string') {
                if (this.value) {
                    this.imageUrl = this.value;

                    this.getDataUrl(this.value)
                        .then(data => {
                            this.fieldValue = data;

                            if (this.fieldValue)
                                this.changed();
                        });
                } else {
                    this.imageUrl = "";
                    this.fieldValue = "";
                }
            }
            if (!this.value) {
                this.imageUrl = "";
                this.fieldValue = "";
            }
        }
    }
}
</script>
