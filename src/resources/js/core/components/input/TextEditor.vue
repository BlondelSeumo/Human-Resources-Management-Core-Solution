<template>
    <div class="editor"
         :class="{'disabled':data.disabled}">
        <textarea
            :id="inputFieldId"
            :name="name"
        />
    </div>
</template>

<script>
import {InputMixin} from './mixin/InputMixin.js';

export default {
    name: "TextEditor",
    mixins: [InputMixin],
    mounted() {
        $("#" + this.inputFieldId).on("summernote.change", () => {
            this.input()
        });
        setTimeout(() => {
            this.initComponent(this.value);
        })
    },
    watch: {
        editorValue: function (value) {
            if (!value) this.setInitValue(value);
        }
    },
    computed: {
        editorValue() {
            return this.value;
        }
    },
    methods: {
        initComponent(initialValue) {
            let config = {
                placeholder: this.data.placeholder,
                dialogsInBody: true,
                height: this.data.height,
            };
            if (Object.keys(this.data.textEditorHints).length) {
                config.hint = this.data.textEditorHints
            }
            if (this.data.minimal === true) {
                config.toolbar = [
                    ['style', ['bold', 'italic', 'underline']],
                    ['fontsize', ['fontsize', 'height', 'color']],
                    ['para', ['ul', 'ol']],
                    ['insert', ['picture', 'link', 'video']]
                ]
            }
            $("#" + this.inputFieldId).summernote(config);
            this.setInitValue(initialValue);
        },

        setInitValue(initialValue) {
            $("#" + this.inputFieldId).summernote('code', initialValue);
        },
        input() {
            this.fieldValue = $('#' + this.inputFieldId).summernote('code');
            this.$emit('input', this.fieldValue);
        }
    }
}
</script>
