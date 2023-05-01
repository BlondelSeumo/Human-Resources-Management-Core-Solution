## Developer guide for `<app-template-preview-card/>` component
### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - card
                    - AppCard.vue

### Register
```js
    Vue.component('app-template-preview-card', require('./components/template-preview-card/AppTemplatePreviewCard').default);
```

### Props

1. subjectKey
    - `type` : `Number`
    - `default` : `subject`
2. defaultContentKey
    - `type` : `String`
    - `default` : `default_content`
3. customContentKey
    - `type` : `String`
    - `default` : `custom_content`
4. actions
   - `type` : `Array`
   - `required` : `true`
5. card
   - `type` : `Object`
   - `required` : `true`
6. id
   - `type` : `String`
   - `required` : `true`   
7. showAction
   - `type` : `Boolean`
   - `default` : `true`   
8. previewType
   - `type` : `String`
   - `default` : `html`   
9. previewImageKey
   - `type` : `Object`
   

### Usage

Available type for `<app-template-preview-card/>`

###How to use `<app-card/>`
```
<template>
    <div class="content-wrapper" style="min-height: 600px; width: 300px">
        <app-template-preview-card
            :id="id"
            :card="card"
            :default-content-key="'defaultContent'"
            :custom-content-key="'customContent'"
            :subject-key="'subject'"
            :show-action="true"
            :preview-type="'image'"
            :preview-image-key="{ relation: 'thumbnail', key: 'path' }"
            :actions="actions"/>

        <!--Normal Modal -->
        <app-modal v-if="addEditModal" :modal-id="'example'" modal-size="default" @close-moadal="closeAddEditModal">
            <template slot="header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="close outline-none" @click="closeAddEditModal">
               <span>
                   <app-icon :name="'x'"></app-icon>
               </span>
                </button>
            </template>

            <template slot="body">
                <p v-html="card.customContent?card.customContent:card.defaultContent"></p>
            </template>

            <template slot="footer">
                <button type="button" class="btn btn-secondary mr-4" data-dismiss="modal" @click="closeAddEditModal">Close
                </button>
                <button type="button" class="btn btn-primary">Save changes</button>
            </template>
        </app-modal>
        <!--Delete Confirmation Modal -->
        <app-confirmation-modal v-if="deleteModal" :modal-id="'delete-example'" @confirmed="confirmed" @cancelled="cancelled"/>
    </div>
</template>

<script>
    export default {
        name: "TestPreviewCard",
        data() {
            return {
                addEditModal: false,
                deleteModal: false,
                id: 'cardOne',
                card: {
                    id: 1,
                    defaultContent: "<p style=\"text-align: center; \"><span style=\"font-family: &quot;Comic Sans MS&quot;;\">Hey...! Welcome&nbsp;</span></p><p style=\"text-align: center; \"><img style=\"width: 50%;\" src=\"https://d1csarkz8obe9u.cloudfront.net/posterpreviews/floral-thank-you-card-template-a2f501467428c583f51d34a1b404195f_screen.jpg?ts=1561537284\"></p><p style=\"text-align: center; \"><span style=\"font-family: &quot;Comic Sans MS&quot;; font-size: 14px; text-align: justify;\">It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their</span><span style=\"font-family: &quot;Comic Sans MS&quot;;\">\uFEFF</span><br></p>",
                    customContent: null,
                    subject: "Birthday invitation",
                    thumbnail: {
                        path: '/images/card-sample.jpg'
                    }
                },
                actions: [
                    {
                        "title": "Preview",
                        "icon": "eye",
                        "type": "page",
                        "pageUrl": "https://www.google.com/",
                        "modifier": function (card) {
                            return card.id>10;
                        }
                    },
                    {
                        "title": "Edit",
                        "icon": "edit",
                        "type": "modal",
                        "modalId": "edit-modal"
                    },
                    {
                        "title": "Delete",
                        "icon": "trash",
                        "type": "modal",
                        "modalId": "delete-modal"
                    },
                    {
                        "title": "Duplicate",
                        "icon": "copy",
                        "type": "page",
                        "pageUrl": "#"
                    },
                    {
                        "title": "Use Campaign",
                        "icon": "zap",
                        "type": "page",
                        "pageUrl": "#"
                    },
                    {
                        "title": "Download",
                        "icon": "download",
                        "type": "page",
                        "pageUrl": "#"
                    }
                ]
            }
        },
        mounted() {
            this.getAction();
        },
        methods: {
            getAction() {
                this.$hub.$on('action-'+this.id, (card, actionObj, active) => {
                    //Write your code here after action
                    console.log(card);
                    console.log(actionObj.type);
                    console.log(active);
                    if (actionObj.type == 'modal') {
                        if (actionObj.modalId == 'edit-modal')
                            this.addEditModal = true
                        else if (actionObj.modalId == 'delete-modal')
                            this.deleteModal = true
                    }
                });
            },

            confirmed() {

            },
            cancelled() {
                $('#delete-example').modal('hide');
                this.deleteModal = false
            },
            closeAddEditModal(){
                $('#example').modal('hide');
                this.addEditModal = false
            }
        }
    }
</script>

```
