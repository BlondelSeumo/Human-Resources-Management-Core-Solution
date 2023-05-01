## Developer guide for `<app-icon/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - icon
                    - Icon.vue

### Register
```js
    Vue.component('app-icon', require('./components/icon/Icon').default);
```

### Props
1. name
    - `type` : `String`
    - `default` : ``
2. iconClass
    - `type` : `String`
    - `default` : ``


### Usage
Available type for `<app-icon/>`
1. name
    - icon name

##Example usages
```
<app-icon :name="icon"/>
```