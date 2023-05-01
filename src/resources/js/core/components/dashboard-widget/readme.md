## Developer guide for `<app-widget/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - dashboard-widget
                    - Index.Vue

#### Register
For `<app-widget/>` component, main Vue component is `Index.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-widget', require('./components/dashboard-widget/Index').default);
```

#### Type Check

All component type checking and $props bind into `resources/js/core/components/dashboard-widget/index.vue` file.

```type

    <app-widget-with-icon v-if="type === 'app-widget-with-icon'" :data="$props"/>
    <app-widget-without-icon v-else-if="type === 'app-widget-without-icon'" :data="$props"/>
    <app-widget-with-circle v-else-if="type === 'app-widget-with-circle'" :data="$props"/>

```

>**Note** :  
>
> - `type` attribute is required  
> - if you are not specified `type`, than can't find the orginal widget component.


#### Components
And we had imports all other components in `resources/js/core/components/dashboard-widget/Index.Vue`

```components

    import AppWidgetWithIcon from "./AppWidgetWithIcon";
    import AppWidgetWithoutIcon from "./AppWidgetWithoutIcon";
    import AppWidgetWithCircle from "./AppWidgetWithCircle";

    
```

#### Props

1. Required Props

	1. type
        - `type` : `String`
        - `required` : `true`

    2. label
        - `type` : `String`
        - `required` : `true`

    3. number
        - `type` : `Number`
        - `required` : `true`

2. Different Props

    1. icon
        - `type` : `String`

#### Usages
`<app-widget/>` can use any component file into the `html` tag.

###Example usages
```
<template>
    <app-widget type="app-widget-with-icon" :label="label" :number="number" :icon="icon" />
    <app-widget type="app-widget-without-icon" :label="label" :number="number" />
    <app-widget type="app-widget-with-circle" :label="label" :number="number" />
</template>

<script>
    export default {
        name: "TestWidgetWithoutIcon",
        data() {
            return {
            	label: 'Total Campaigns',
            	icon: 'sun',
            	number: 5000,
                
        	}
    	}
    }
</script>


```