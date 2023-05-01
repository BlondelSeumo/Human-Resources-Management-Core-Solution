## Developer guide for `<app-input/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - inputs
                    - Index.Vue

### Props
##### Note: 
- All field which support list props also support ```listValueField``` props. This field will be the replacement for your value filed in list. Like you can pass value like bellow
```json
[
  {"id":  1, "value":  "Anything"},
  {"id":  2, "value":  "Something"}
]
``` 
But if you want that your value field is something else in your data then you can pass ```listValueField='name|translated_name'```

- All field which support list props also you can disable specific item by sending `disabled : true` in list array. like below
```json
[
  {"id":  1, "value":  "Anything", "disabled": true},
  {"id":  2, "value":  "Something"}
]
``` 
Disabled item can't be checked or selected by default it's false. means all item are enable to check or select.


1. Common props
    1. type
        - `type` : `String`
        - `required`: `true`
    2. disabled
        - `type` : `Boolean`
        - `default` : `false`
    3. id
        - `type` : `String`
        - `deafult` : `""`
    4. placeholder
        - `type` : `String`
        - `default` : `""`
        
2. Read Only Props
   - Only for those types: text, email, number, decimal, currency, password,text-area
   - readOnly
        - `type` : `Boolean`
        - `default` : `false`
        
3. Required props
    1. required
        - `type` : `Boolean`
        - `default` : `false`

4. Style props for `<input>` `<label>`
    1. inputClass
         - `type` : `String`
         - `default` : `""`
    2. label
         - `type` : `String`
         - `default` : `""`
    3. labelClass
         - `type` : `String`
         - `default` : `""`

5. Smart Select
    1. list
        - `type` : `Array`
        - `default` : 
            `
            function () { 
                return [
                    {
                        id: 1,
                        value: "option 1",
                        disabled: true
                    },
                    {
                        id: 2,
                        value: "option 2",
                    },
                    {
                        id: 3,
                        value: "option 3",
                    },
                    {
                        id: 4,
                        value: "option 4",
                        disabled: true
                    }
                ];
            }
            `
    2. selectedTextClass
        - `type` : `String`
        - `default` : `""`
    3. listClass
        - `type` : `String`
        - `default` : `""`
    4. listItemClass
        - `type` : `String`
        - `default` : `""`
    5. listItemInputClass
        - `type` : `String`
        - `default` : `""`

6. Multi Create
    1. storeData
        - `type` : `Function`
    2. multiCreatePreloader
        - `type` : `Boolean`
        - `default` : `False`

7. Date Picker
    1. minDate
        - `type` : `String,Date`
    2. maxDate
        - `type` : `String,Date`
    3. dateMode
        - `type` : `String`
        - `default` : `'date'`
        - available are : `'date'`,`'time'`,`'dateTime'`,`'range'` 
        - if you provide `'range'` it will automatically set `isRange="true"`
    4. dateColor
        - `type` : `String`
        - `default` : `'blue'`   
    5. isRange
        - `type` : `Boolean`
        - `default` : `'false'`    
    6. popoverPosition
        - `type` : `String`
        - `default` : `'bottom-start'`
        - available are : `'bottom-start'`,`'bottom-end'`,`'top-start'`,`'top-end'`

8. Time picker
    1. timeFormat
        - `type` : `Number`
        - `default` : `24`
    2. minTime
        - `type` : `String`
    3. maxTime
         - `type` : `String`
    4. getTimeZone
        - `type` : `Boolean`
        - `default` : `false`

9. Textarea
    1. textAreaCols
        - `type` : `Number`
    2. textAreaRows
        - `type` : `Number`
    3. textAreaWrap
        - `type` : `String`
        - `default` : `"soft"`
    4. textAreaSpellCheck
        - `type` : `Boolean`
        - `default` : `false`
    5. textAreaDir
        - `type` : `String`

10. Currency
    1. symbol
        - `type` : `String`
        - `default` : `'$'`
    2. decimal
        - `type` : `String`
        - `default` : `'.'`
    3. thousandSeparator
        - `type` : `String`
        - `default` : `','`
    4. precision
        - `type` : `Number`
        - `default` : `2`
    5. currencyFormat
        - `type` : `String`
        - `default` : `'%v%s'`

11. Radio & Checkbox
    1. checked
        - `type` : `Boolean`
        - `default` : `false`
    2. radioCheckboxName
        - `type` : `String`
    3. radioCheckboxWrapper
        - `type` : `String`
        
12. Password
    1. sameAs
        - `type` : `String`
        - `default` : `''`
    2. specialValidation
            - `type` : `Boolean`
            - `default` : `false`

13. File
    1. maxFiles
        - `type` : `Number`
        - `default` : `null`
    2. generateFileUrl: 
        - `type` : `Boolean` //generating url for exact folder location
        - `default` : `true`

14. SummerNote
    1. height
        - `type` : `Number`
        - `default` : `300`
    
15. RadioButtons
    1. list
        - `type` : `Array`
        - `default` : 
            `
            function () { 
                return [
                    {
                        id: 1,
                        value: "option 1",
                    },
                    {
                        id: 2,
                        value: "option 2",
                    },
                    {
                        id: 3,
                        value: "option 3",
                    },
                    {
                        id: 4,
                        value: "option 4",
                    }
                ];
            }
            `
            
16. Number and length
    1. maxLength
        - `type` : `Number`
    2. minLength
        - `type` : `Number`
    3. maxNumber
        - `type` : `Number`
    4. minNumber
        - `type` : `Number`
    5. alphanumeric
        - `type` : `Boolean`
        - `default` : `false`

17. Error
    1. errorMessage
        - `type` : `String`


### Usage
1. Input (text)
```html
    <app-input  type="text" v-model="textValue"/>
```

2. Input (email)
```html
    <app-input  type="email" v-model="userEmail"/>
```

3. Time Picker
```html
    <app-input  type="time" v-model="timevalue"/>
```

4. Input (number)
```html
    <app-input  type="number" v-model="numberValue"/>
```

5. Input (decimal)
```html
    <app-input  type="decimal" v-model="userEmail"/>
```

6. Input (password)
```html
    <app-input  type="password" v-model="password"/>
```

7. Date Picker
```html
    <app-input  type="date" v-model="date"/>
```

8. Smart select
```html
    <app-input label="smart-select"  type="smart-select" :list="[{id:1, value: 'option 1'}]" v-model="smartSelect"/>
```

9. Select
```html
    <app-input  type="select" :list="[{id:1, value: 'option 1'}]" v-model="selectValue"/>
```

10. Multi select
```html
    <app-input  type="multi-select" :list="[{id:1, value: 'option 1'}]" v-model="multiSelect"/>
```

11. Multi create
```html
    <app-input  type="multi-create" :list="[{id:1, value: 'option 1'}]" v-model="multiCreate"/>
```

12. Textarea
```html
    <app-input  type="textarea" v-model="textarea"/>
```

13. Currency
```html
    <app-input  type="currency" v-model="currency"/>
```

14. Radio buttons
```html
    <app-input  type="radio-buttons" :list="[{id:1, value: 'option 1'}]" v-model="radioButtons"/>
```

15. Radio
```html
    <app-input  type="radio" :list="[{id:1, value: 'option 1'}]" v-model="radioVlaue"/>
```

16. Checkbox
```html
    <app-input  type="checkbox" :list="[{id:1, value: 'option 1'}]" v-model="checkboxValue"/>
```

17. Text Editor (summer note)
```html
    <app-input  type="text-editor" v-model="textEditor"/>
```

18. File
```html
    <app-input label="select one" type="file" v-model="fileValue"/>
```

19. custom-file-upload
```html
    <app-input label="select one" type="custom-file-upload" v-model="customFile"/>
```

20. Dropzone
```html
    <app-input  type="dropzone" v-model="dropzoneValue"/>
```

21. Tel-Input
```html
<app-input type="tel-input" v-model="testNumber" placeholder="Enter phone number" :required="true"/>
```

22. Switch Input
```html
<app-input type="switch" :label="$t('Switch')" v-model="switchValue"/>
```

### Validation

1. Custom Validation
```html
<template>
    <div class="content-wrapper">
        <div class="card card-with-shadow border-0">
            <div class="card-body">
                <form ref="form" data-url="test-component" enctype="multipart/form-data">
                    <div class="form-group">
                        <app-input 
                            type="text"
                            v-model="test"
                        />
                    </div>
                    <button class="btn btn-primary" type="submit" @click.prevent="submitData">Submit</button>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
    import { FormMixin } from "../../mixins/form/FormMixin.js";

    export default {
        name: "TestValidation",
        mixins: [FormMixin],
        data() {
            return {
               test: '',
            }
        },
        methods: {
            submitData(){
                this.fieldStatus.isSubmit = true; // must do fieldStatus.isSubmit = true
                if(this.test.length > 6){
                    this.save(this.testFields); // method from mixin
                }else{
                    this.fieldStatus['test'] = { // here test is v-model
                        isValid : false,
                        message : "Length should more then 6"
                    }
                }
            }
        }
    }
</script>

```

** Note ** :  
    - If you are not specified `type`, it will render default `text` input
    - Other attributes are optional
