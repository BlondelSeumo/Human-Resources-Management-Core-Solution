<template>
    <form ref="form"
          @submit.prevent=""
          v-if="loaded"
    >
        <div class="form-group" v-if="!(['invite_user', 'password-reset'].includes(this.notification_event.name))">
            <label>{{ $t('contents') }}</label>
            <app-input
                type="text"
                name="custom_content"
                v-model="template.content"
                :placeholder="$placeholder('template', 'content')"
                :error-message="$errorMessage(errors, 'custom_content')"
                id="database-template-title"
            />

        </div>
        <div class="form-group text-center">
            <button
                type="button"
                class="btn btn-sm btn-primary px-3 py-1 margin-left-2 mt-2"
                data-toggle="tooltip"
                data-placement="top"
                v-for="t in Object.keys(all_tags)"
                @click="insertAtCaret('database-template-title', t)"
                :title="tags[t]"
            >{{ t }}
            </button>
        </div>
    </form>
    <div v-else>
        <app-pre-loader/>
    </div>
</template>

<script>
import TemplateMixins from "./TemplateMixins";
import {mapState} from "vuex";
import {databaseTemplate} from "./Helper";

export default {
    name: "DatabaseTemplate",
    mixins: [TemplateMixins],
    props: {
        props: {}
    },
    data() {
        return {
            loaded: false,
            tags: databaseTemplate(),
        }
    },
    computed: {
        ...mapState({
            notification_event: state => state.notification_event.notification_event,
            loader: state => state.loading
        }),
        all_tags() {
            let tags = {...this.tags};
            if (this.props.alias === 'app') {
                delete tags['{tenant_name}'];
                return tags;
            }
            delete tags['{app_name}'];
            return tags;
        }
    },
    methods: {
        templateSetter(value) {
            this.loaded = false;
            let template = this.collection(this.notification_event.templates)
                .find(value, 'type');

            if (template && Object.keys(template).length) {
                this.template = {...template};
                this.template.content = this.template.custom_content || this.template.default_content;
            } else {
                this.template = {type: 'mail'};
            }
            this.loaded = true
        },
        insertAtCaret(areaId, text) {
            let txtarea = document.getElementById(areaId);
            if (!txtarea) {
                return;
            }

            let scrollPos = txtarea.scrollTop;
            let strPos = 0;
            let br = ((txtarea.selectionStart || txtarea.selectionStart == '0') ?
                "ff" : (document.selection ? "ie" : false));
            if (br == "ie") {
                txtarea.focus();
                let range = document.selection.createRange();
                range.moveStart('character', -txtarea.value.length);
                strPos = range.text.length;
            } else if (br == "ff") {
                strPos = txtarea.selectionStart;
            }

            let front = (txtarea.value).substring(0, strPos);
            let back = (txtarea.value).substring(strPos, txtarea.value.length);
            txtarea.value = front + text + back;
            strPos = strPos + text.length;
            if (br == "ie") {
                txtarea.focus();
                let ieRange = document.selection.createRange();
                ieRange.moveStart('character', -txtarea.value.length);
                ieRange.moveStart('character', strPos);
                ieRange.moveEnd('character', 0);
                ieRange.select();
            } else if (br == "ff") {
                txtarea.selectionStart = strPos;
                txtarea.selectionEnd = strPos;
                txtarea.focus();
            }

            txtarea.scrollTop = scrollPos;
        }
    },
    created() {
        this.template.type = 'database';
    },
    watch: {
        'notification_event.id': {
            handler: function (nEvent) {
                this.templateSetter(this.template.type);
            },
            immediate: true
        },
        'template.type': {
            handler: function (type) {
                if (this.notification_event && this.notification_event.templates) {
                    this.templateSetter(type);
                }
            },
        },
        template: {
            handler: function (template) {
                this.$hub.$emit('setTemplate', template)
            },
            deep: true,
            immediate: true
        }
    },
}
</script>


<style scoped>
.margin-left-2 {
    margin-left: 4px;
}

.margin-left-2:first-child {
    margin-left: 0;
}
</style>
