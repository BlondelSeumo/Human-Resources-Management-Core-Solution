## Developer guide for `<app-badge/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - badge
                    - Badge.Vue

#### Register
For `<app-badge/>` component, main Vue component is `Badge.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-badge', require('./components/badge/Badge').default);
```

#### Props

1. Required Props

	1. className
        - `type` : `String`
        - `required` : `true`

    2. label
        - `type` : `String`
        - `required` : `true`

#### Usages
`<app-badge/>` can use any component file into the `html` tag.

```
************* `End-Badge` **************
```
## Developer guide for `<app-note/>` component..

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - badge
                    - Note.Vue


#### Register
For `<app-note/>` component, main Vue component is `Badge.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-note', require('./components/badge/Note').default);
```

#### Props

1. Required Props

	1. title
        - `type` : `String`
        - `required` : `true`

    2. notes
        - `type` : `[Array, String]`
        - `required` : `true`
        
    3. showTitle
        - `type` : `Boolean`
        - `default` : `true`
    
    4. noteType
            - `type` : `String`
            - `default` : `note-warning`
            
    5. contentType
            - `type` : `String`
            - `default` : `string` //accept html also
           
    6. noteIcon
            - `type` : `String`
            - `default` : `book-open`
           
    7. paddingClass
            - `type` : `String`
            - `default` : `p-4`
       
#### Usages
`<app-note/>` can use any component file into the `html` tag.
