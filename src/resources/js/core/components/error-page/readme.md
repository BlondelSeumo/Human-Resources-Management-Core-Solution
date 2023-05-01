## Developer guide for `<app-error/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - error-page
                    - Error.Vue
                    

### Register

```js
    Vue.component('app-avatar', require('./components/error-page/Error').default);
```
### Props

1. errorType
    - `type` : `String`
    - `required` : `true`
2. errorTitle
    - `type` : `String`
    - `default` : `'Something went wrong!'`
3. errorDescription
    - `type` : `String`
    - `default` : `''`
4. url
    - `type` : `String`
    - `default` : `'/'`

### Usages

Available type for `<app-error/>`

-   Example usages of `<app-error/>`
```
<template>
    <app-error :error-type="'500'"
               :error-title="'Bad request'"
               :error-description="'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type'"/>
</template>

<script>
    export default {
        name: "ErrorExample"
    }
</script>

```