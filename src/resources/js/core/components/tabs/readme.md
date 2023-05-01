# Developer guide for `<app-tab/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - tabs
                    - Tab.vue


### Register
For `<app-tab/>` component. We had register this component in ```resources/js/core/components/coreApp.js``` file

```js
    Vue.component('app-tab', require('./components/tabs/Index').default);
```

### Props
1. tabs
    - `type` : `Array`
    - `required` : `true`
2. icon
    - `icon` : `String`
    - `required` : `true`
2. type
    - `icon` : `String`
    - `default` : `vertical`

### Usage

Available type for `<app-tab/>`

1. tabs
    - `Array of objects`. contains with single tab objects
    like
    ```
   {
       "name": "delivery",
       "title": "delivery setting",
       "component": "delivery-setting",
       "props": "Example string",   
       "permission": ""
    }
   ```
1. tabs - if type is horizontal
    - `Array of objects`. contains with single tab objects
    like
    ```
   {
       "name": "delivery",
       "icon": "phone-call",
       "component": "delivery-setting",
       "props": "Example string",
       "permission": ""
    }
   ```


2. icon - if type is vertical
    - Tabs icon
    
### Usages
In the below given how to use `<app-tab/>` component for vertical.

```
<template>
    <div class="content-wrapper" style="min-height: 200px">
        <app-tab :tabs="tabs" :icon="icon"/>
    </div>
</template>

<script>
    export default {
        name: "TestTab",
        data() {
            return {
                icon : 'user',
                tabs: [
                    {
                        "name": "delivery",
                        "title": "delivery setting",
                        "component": "wizard-delivery",
                        "props": "Example string",
                        "permission": ""
                    },
                    {
                        "name": "details",
                        "title": "details setting",
                        "component": "wizard-details",
                        "props": ['A','B','C'],
                        "permission": ""
                    },
                    {
                        "name": "template",
                        "title": "template setting",
                        "component": "wizard-template",
                        "props": [
                            {id: 1, value: 'Cricket', disabled: true},
                            {id: 2, value: 'Football'},
                            {id: 3, value: 'Badminton'},
                            {id: 4, value: 'Option 4', disabled: true},
                            {id: 5, value: 'Option 5'},
                            {id: 6, value: 'Option 6'},
                        ],
                        "permission": ""
                    },
                    {
                        "name": "setup",
                        "title": "setup setting",
                        "component": "wizard-setup",
                        "props": "admin/something",
                        "permission": ""
                    },
                    {
                        "name": "audience",
                        "title": "audience setting",
                        "component": "wizard-audience",
                        "props": "/www.google.com",
                        "permission": ""
                    }
                ]
            }
        }
    }
</script>

```

### Usages
In the below given how to use `<app-tab/>` component for horizontal.

```

<template>
    <div class="content-wrapper" style="min-height: 200px">
        <app-tab type="horizontal" :tabs="horizontalTabs"/>
    </div>
</template>

<script>
    export default {
        name: "TestTab",
        data() {
            return {
                horizontalTabs: [
                    {
                        "name": "delivery",
                        "icon": "activity",
                        "component": "wizard-delivery",
                        "props": "Example string",
                        "permission": ""
                    },
                    {
                        "name": "details",
                        "icon": "phone-call",
                        "component": "wizard-details",
                        "props": ['A','B','C'],
                        "permission": ""
                    },
                    {
                        "name": "template",
                        "icon": "mail",
                        "component": "wizard-template",
                        "props": [
                            {id: 1, value: 'Cricket', disabled: true},
                            {id: 2, value: 'Football'},
                            {id: 3, value: 'Badminton'},
                            {id: 4, value: 'Option 4', disabled: true},
                            {id: 5, value: 'Option 5'},
                            {id: 6, value: 'Option 6'},
                        ],
                        "permission": ""
                    },
                    {
                        "name": "setup",
                        "icon": "paperclip",
                        "component": "wizard-setup",
                        "props": "admin/something",
                        "permission": ""
                    },
                    {
                        "name": "audience",
                        "icon": "file-text",
                        "component": "wizard-audience",
                        "props": "/www.google.com",
                        "permission": ""
                    }
                ]
            }
        }
    }
</script>


```

### Methods
1. `loadComponent` for load Each single tab component
    - /* in your tab components you can receive a props `id` and if you send props for component can receive that `props` also.


###Update Notifications
If you want to add a header button you just need to send 
```
"headerButton": {
     "label": "Add new template"
},

```

in app tab objects like:
```
{
    "name": "details",
    "title": "details setting",
    "component": "wizard-details",
    "props": ['A', 'B', 'C'],
    "permission": false,
    "headerButton": {
        "label": 'Add New',
        "class": 'btn btn-success'
    }
},
```

Then when button was clicked it send a global event. You can received by
```
this.$hub.$on('headerButtonClicked-' + this.id, (component) => {
    console.log(component);
    this.showModal = true;
    console.log('Event Triggered');
})

```
Example usages:
```
<template>
    <div>
        <div style="min-height: 300px">
            I am a setting for testing dynamic tab and wizard - WizardTemplate
            <app-modal v-if="showModal" modal-id="demo-modal" @close-modal="showModal=false">
                <div class="p-4">
                    This is test modal
                </div>
            </app-modal>
        </div>

        <div class="form-group">
            <div class="row">
                <div class="col-12">
                    <button type="button" @click.prevent="goBack" class="btn btn-secondary mr-2">Back</button>
                    <button type="button" @click.prevent="goNext" class="btn btn-primary">Next</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "WizardTemplate",
        props: {
            props: {
                default: ''
            },
            id: {
                type: String
            }
        },
        data(){
            return{
                showModal: false
            }
        },
        mounted() {
            this.$hub.$on('headerButtonClicked-' + this.id, (component) => {
                console.log(component);
                this.showModal = true;
                console.log('Event Triggered');
            })
        },
        methods: {
            goBack() {
                this.$emit('back', true);
            },
            goNext() {
                this.$emit('next', true);
            }
        },
        beforeDestroy() {
            console.log('Before destroy');
            this.$hub.$off('headerButtonClicked-' + this.id);
        }
    }
</script>

```
