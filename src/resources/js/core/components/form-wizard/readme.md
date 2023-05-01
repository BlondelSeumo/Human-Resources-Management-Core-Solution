# Developer guide for `<app-form-wizard/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - form-wizard
                    - FormWizard.vue


### Register
For `<app-form-wizard/>` component. We had register this component in ```resources/js/core/components/coreApp.js``` file

```js
    Vue.component('app-form-wizard', require('./components/form-wizard/FormWizard').default);
```
### Props
1. tabs
    - `type` : `Array`
    - `required` : `true`
2. tabInit - Not required
    - `type` : `Number`
    - `default` : `0`
2. enableAll - Not required
    - `type` : `Boolean`
    - `default` : `false`
    
### Props Structure

1. tabs
    - `Array of objects`. contains with single tab objects
    like
    ```
   {
       "name": "delivery",
       "component": "wizard-delivery",
       "props": ['A','B','C']
   }
   ```

### Methods
2. `nextTab` if component emit `next` event as true then it load next component
3. `prevTab` if component emit `back` event as true then it load previous component

### Related Component
- Component which have been loaded after next or back step
- `Can pass 2 event to parent`
- `next` with value `true` if want to go next tab
- `back` with value `true` if want to go back with

### Events call by `<app-form-wizard/>` **

-   `selectedComponentName` for getting which component is now loaded
-   `disabledTab` for if any one click any disabled tab, then this event is called for getting disabled tab objects

### Usages 
 - Example of Form wizard
```
<template>
    <div class="content-wrapper p-4">
        <div class="card card-with-shadow border border-0 mb-primary">
            <div class="card-body">
                <app-form-wizard :tabs="tabs"
                                 @selectedComponentName="selectedComponent"
                                 @disabledTab="currentDisableTab"/>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Example",
        data() {
            return {
                tabs: [
                    {
                        "name": "details",
                        "component": "wizard-details",
                        "props": "Example string"
                    },
                    {
                        "name": "delivery",
                        "component": "wizard-delivery",
                        "props": ['A', 'B', 'C']
                    },
                    {
                        "name": "template",
                        "component": "wizard-template",
                        "props": [
                            {id: 1, value: 'Cricket', disabled: true},
                            {id: 2, value: 'Football'},
                            {id: 3, value: 'Badminton'},
                            {id: 4, value: 'Option 4', disabled: true},
                            {id: 5, value: 'Option 5'},
                            {id: 6, value: 'Option 6'},
                        ]
                    },
                    {
                        "name": "setup",
                        "component": "wizard-setup",
                        "props": "admin/something"
                    },
                    {
                        "name": "audience",
                        "component": "wizard-audience",
                        "props": "/www.google.com"
                    }
                ]
            }
        },
        methods: {
            selectedComponent(value) {
                console.log(value);
            },
            currentDisableTab(disableTab) {
                console.log(disableTab);
            }
        }
    }
</script>

```


 - Example of component which are loaded
 
 
```
<template>
    <div>
        <div style="min-height: 300px">
            I am a setting for testing dynamic tab and wizard - Delivery
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
        name: "WizardDelivery",
        props:{
            props:{
                default: ''
            }
        },
        methods:{
            goBack(){
                this.$emit('back',true);
            },
            goNext(){
                this.$emit('next',true);
            }
        }
    }
</script>

```
