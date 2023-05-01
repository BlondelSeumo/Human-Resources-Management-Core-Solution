## Developer guide for `<app-avatar/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - avatars
                    - Avatar.Vue
                    

### Register

```js
    Vue.component('app-avatar', require('./components/avatars/Avatar').default);
```


### Props

1. avatarClass
    - `type` : `String`
    - `required` : `true`
2. img
    - `type` : `String`
    - `required` : `true`
3. altText
   - `type` : `String`
   - `default` : `Not found`
4. shadow
    - `type` : `Boolean`
    - `default` : `false`
5. border
    - `type` : `Boolean`
    - `default` : `false`
6. title
    - `type` : `String`
    - `default` : `Avatar`
7. status
    - `type` : `String`
    - `default` : null



### Usage

Available type for `<app-avatar/>`

1. avatarClass
    - `avatars-w-100, avatars-w-90, avatars-w-80, avatars-w-70, avatars-w-60, avatars-w-50, avatars-w-40, avatars-w-30, avatars-w-20, avatars-w-10`.

2. img 
    - image path

3. altText
    - any text for img
4. shadow
    - shadow enable or disable
5. Border
    - border enable or disable
6. title
    - String if image not available
7. status
    - Indicator of status
    - A string like `success` , `info` , `danger` , `warning`, `primary`,`secondary`

###Usages
 - Example of using avatar component

```

<template>
    <div class="content-wrapper" style="height: 100vh">
        <app-breadcrumb
            page-title="Page title"
            :directory="['Directory','Sub-directory']"
            :icon="'grid'"
            :button="{label:'Form',url:'/form'}"
        />
        <div class="row">
            <app-avatar
                title="avatar"
                :avatar-class="avatarClass"
                status="success" :shadow="true"
                :border="true"
                :img="img"
                :alt-text="altText"
            />
        </div>
    </div>
</template>

<script>
export default {
    name: "TestAvatar",
    data() {
        return {
            avatarClass: "avatars-w-100",
            img: "https://images.unsplash.com/photo-1506919258185-6078bba55d2a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60",
            altText: "Not Found"
        }
    }
}
</script>

```
