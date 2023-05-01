# Developer guide for `FormMixin`

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - mixins
                - form
                    - FormMixin.js

### Props

1. selectedUrl (for edit form).
    - `type`: `String`,


### Methods

1. `getEditData` for edit form.
    - hooks 
        - `beforeGetEditData` Before Calling the backend api
        - `afterSuccessFromGetEditData` after request success return data by this hook.
        - `afterErrorFromGetEditData` after request error return data by this hook.
2. `save` for submit data
    - `beforeSubmit` can change `fields` object by this hook. 
    - `afterSuccess` after request success return data by this hook.
    - `afterError` after request error return data by this hook.
    - `afterFinalResponse` finally .
    - `afterSubmit` after submit can do anything by this hook.
    - `afterJsValidationFail` After js validation fail

3. `isValidForm` check form validation.
4. `changed` `$emit` from `<app-input/>`


### Usege

1. Must have to diclier `ref="form"` and `data-url='submit'` add the parent form.
2. Must have to use `<app-input/>` component. 
3. Must have to assign `:field-props="fieldStatus"` to `<app-input/>` component.
3. Call `save` method at submit button.

```Vue

    <template>
        <form ref="form" data-url='submit' action method>
            <app-input
                name="time"
                type="time"
                :field-props="fieldStatus"
                :time-format="24"
                @changed="changed"/>

            <button type="submit" @click.prevent="save">submit</button>
        </form>
    </template>

    <script>
        import { FormMixin } from "../../mixins/form/FormMixin.js";

        export default {
            name: "AddEditForm",
            mixins: [FormMixin],
            methods: {
                //hooks
                beforeSubmit(fields) {
                return {
                    fields: fields
                };
                },
                afterSubmit() {},

                afterSuccess(res) {
                console.log(res);
                },

                afterError(res) {
                console.log(res);
                },
            }
        };
    </script>


```
