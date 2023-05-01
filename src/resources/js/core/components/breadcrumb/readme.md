## Developer guide for `<app-breadcrumb/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - breadcrumb
                    - index.Vue

#### Register
For `<app-breadcrumb/>` component, main Vue component is `index.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-breadcrumb', require('./components/breadcrumb/index').default)
```

#### Props

1. Uses Props

	1. pageTitle
        - `type` : `String`
        - `required` : `true`

    4. button
            - `type` : `Object`


#### Usages
`<app-breadcrumb/>` can use any component file into the `html` tag. The page title component will show as `<h4>` element and small ordered list next to it.
directory are the level of your directory and you can pass array of directory also. You can also pass html in directory f you need to render any. Same will work for directory array also  

- button object example
```
    {
        label:'Form',
        url:'/form'
    }

```

###Example usages
```
<app-breadcrumb page-title="Page title" :directory="['Directory','Sub-directory']" :icon="'grid'" :button="{label:'Form',url:'/form'}"/>
```