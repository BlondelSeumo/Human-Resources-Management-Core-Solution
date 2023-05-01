# Developer guide for `<app-confirmation-modal/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - confirmation-modal
                    - Index.Vue

### Register
For `<app-confirmation-modal/>` component, main Vue component is `Index.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file

```js
    Vue.component('app-table', require('./components/confirmation-modal/Index').default);
```

### Props

1. modalId
    - `type` : `String`,
    - `required` : `true`
2. message
    - `type` : `String`,
    - `default`: `This content will be deleted permanently.`
3. firstButtonName
    - `type` : `String`,
    - `default`: `Yes`
4. secondButtonName
    - `type` : `String`,
    - `default`: `No`
5. icon
    - `type` : `String`
6. title
    - `type` : `String`
7. subTitle
    - `type` : `String`
7. modalClass
    - `type` : `String`
    - `default` : `danger`
    - available classes are: `danger`,`warning`,`primary`,`success`,`info`

### $emit
- `confirmed` for confirmed the process.
- `cancelled` for cancelled the process.

### Useges

```
<template>
    <div class="content-wrapper">
        <button type="button" class="btn btn-primary" @click="deleteModal=true">
            Delete Modal
        </button>

        <!--Delete Confirmation Modal -->
        <app-confirmation-modal v-if="deleteModal" :modal-id="'delete-example'" @confirmed="confirmed" @cancelled="cancelled"/>

        <!-- Dynamic icon title subtitle message -->

        <app-confirmation-modal v-if="isShowDeleteModal"
                                        icon="check-circle"
                                        title="Hand's Up"
                                        sub-title="This content will be deleted permanently"
                                        message="Are you sure?"
                                        modal-class="primary"
                                        modal-id="delete-modal"
                                        @confirmed="confirmed"
                                        @cancelled="cancelled"/>
    </div>
</template>

<script>

    export default {
        name: "ExampleModal",

        data() {
            return {
                deleteModal: false
            }
        },
        methods: {
            confirmed() {
                console.log('Clicked Confirmed');
                this.deleteModal = false;
            },
            cancelled() {
                console.log('Clicked Cancel');
                this.deleteModal = false;
            }
        }
    }
</script>


```