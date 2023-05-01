## Developer guide for `<app-pre-loader/>` component

### Folder Structure
From Laravel Application

- resources
    - js
        - core
            - components
                - preloders
                    - Preloader.vue
                    

### Register
```js
    Vue.component('app-pre-loader', require('./components/preloders/Preloader').default);
```


### Props
1. spinnerClass
    - `type` : `String`
    - `default` : ``

### Usage
`<app-pre-loader/>` can use inside any button or any other html tag.    

            
## Developer guide for `<app-overlay-loader/>` component

### Folder Structure
From Laravel Application

- resources
    - js
        - core
            - components
                - preloders
                    - OverlayLoader.vue
                    

### Register
```js
    Vue.component('app-overlay-loader', require('./components/preloders/OverlayLoader').default);
```


### Props   
No props for overlay loader


### Usage
`<app-overlay-loader/>` can use on any content for a full div overlay effect   
`***This preloader is an absolute position element. So, remember to keep the position relative of parent component***`

