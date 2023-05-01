<template>
    <div class="mb-primary">
        <div class="card template-preview-card border-0 mb-2">
            <div class="template-preview-wrapper p-3 rounded" @click="selectedTemplateCard"
                 :style="[previewType==='image'? {'backgroundImage': 'url('+generateBackgroundImage()+')'} : null]">
                <span v-if="previewType==='html'" class="v-html-wrapper"
                      v-html="!card[customContentKey] ? card[defaultContentKey] : card[customContentKey]"/>
                <div v-if="showAction" class="card-overlay-options">
                    <ul class="list-group h-100">
                        <li v-for="(action,index) in actions" :key="index" v-if="checkModifier(action)"
                            class="list-group-item">
                            <a class="d-block" href="#" target="_blank" @click.prevent="callAction(action)">
                                <app-icon :name="action.icon"/>
                                <span class="ml-3">{{ action.title }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <h5 class="text-muted card-subject">{{ card[subjectKey] }}</h5>
    </div>
</template>

<script>
import AppFunction from "../../helpers/app/AppFunction";

export default {
    name: "AppTemplatePreviewCard",
    props: {
        subjectKey: {
            type: String,
            default: 'subject'
        },
        defaultContentKey: {
            type: String,
            default: 'default_content'
        },
        customContentKey: {
            type: String,
            default: 'custom_content'
        },
        actions: {
            type: Array,
            require: true
        },
        showAction: {
            type: Boolean,
            default: true
        },
        card: {
            type: Object,
            require: true
        },
        id: {
            type: String,
            require: true
        },
        previewType: {
            type: String,
            default: 'html'
        },
        previewImageKey: {
            type: Object
        }
    },
    methods: {
        checkModifier(action) {
            if (action?.modifier) {
                if (action.modifier(this.card) == false) {
                    return false
                } else return true;
            } else return true
        },

        selectedTemplateCard() {
            if (this.showAction == false)
                this.$hub.$emit('TemplateCard-' + this.id, this.card);
        },
        callAction(action) {
            this.$hub.$emit('action-' + this.id, this.card, action, true)
        },
        generateBackgroundImage() {
            if (this.card[this.previewImageKey.relation]) {
                if (this.card[this.previewImageKey.relation][this.previewImageKey.key]) {
                    return this.card[this.previewImageKey.relation][this.previewImageKey.key];
                } else return AppFunction.getAppUrl('images/default-card-view-img.png');
            } else return AppFunction.getAppUrl('images/default-card-view-img.png');
        }
    }
}
</script>
