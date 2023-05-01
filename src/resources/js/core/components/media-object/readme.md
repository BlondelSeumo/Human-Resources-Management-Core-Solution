## Developer guide for `<app-media-object/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - media-object
                    - MediaObject.Vue
                    

### Register

```js
    Vue.component('app-avatar', require('./components/media-object/MediaObject').default);
```

### Props

1. imgUrl
    - `type` : `String`
    - `required` : `true`
2. mediaTitle
    - `type` : `String`
    - `required` : `true`
3. mediaSubtitle
    - `type` : `String`
    - `required` : `true`
4. altText
    - `type` : `String`

###Usages
- Example of usages `<app-media-object/>`
```
<app-media-object :img-url="imgPath" :media-title="$t('media_title')"
                                          :media-subtitle="$t('media_subtitle')"/>
```
