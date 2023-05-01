## Developer guide for `<app-avatars-group/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - avatars-group
                    - AvatarsGroup.Vue
                    

### Register

```js
    Vue.component('app-avatars-group', require('./components/avatars-group/AvatarsGroup').default);
```


### Props 

1. avatarsGroupClass
    - `type` : `String`
    - `default` : `avatars-group-w-50`
2. avatars
    - `type` : `Array`
    - `required` : `true`
3. shadow
   - `type` : `Boolean`
   - `Default` : `false`
4. border
   - `type` : `Boolean`
   - `Default` : `false`

### Usages

1. avatarsGroupClass
    - css class for avatars size - any of these two
    - `avatars-group-w-60` , `avatars-group-w-50`

2. avatars
    - Array of objects 
    - ``{img : '', alt : '', title: '', subTitle: ''}``
    
3. shadow
    - shadow in the outline of avatars is shadow is true.

  
    
    
### Usages example
```

<template>
    <div class="content-wrapper p-4">
        <div class="card p-4 card-with-shadow border-0">
            <div class="row">
                <div class="col-xl-6">
                    <app-avatars-group :avatar-group-class="avatarsGroupClass" :avatars="avatars" :shadow="true"/>
                </div>
                <div class="col-xl-6">
                    <app-avatars-group :avatars="avatars" :shadow="true"/>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "TestAvatarGroup",
        data(){
            return {
                avatarsGroupClass: 'avatars-group-w-60',
                avatars: [
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                    {
                        img: 'https://images.unsplash.com/photo-1569779213435-ba3167dde7cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60',
                        title: 'Happy daughter',
                        subTitle: 'Software engineer',
                        alt: 'Not Found'
                    },
                ]
            }
        }
    }
</script>

```
