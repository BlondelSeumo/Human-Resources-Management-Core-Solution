## Developer guide for `<app-tag-manager/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - tag
                    - TagManager.Vue
                    

### Register

```js
    Vue.component('app-avatars-group', require('./components/tag/TagManager').default);
```


### Props 

1. tags
    - `type` : `Array`
    - `default` : `function () {
           return [];
       }`
2. list
    - `type` : `Array`
    - `default` : `function () {
               return [
                   {id: 1, name: 'Red', color: '#72C2EE'},
                   {id: 2, name: 'Black', color: '#72f2ee'},
                   {id: 3, name: 'Yellow', color: '#72C268'},
                   {id: 4, name: 'Blue', color: '#F2C268'}
               ];
           }`
3. listValueField
   - `type` : `String`
   - `Default` : `value`
4. tagPreloader
   - `type` : `Boolean`
   - `Default` : `false`
5. placeholder: 
   - `type`: `String`,
   - `default`: ``
6. disabled: 
   - `type`: `Boolean`,
   - `default`: `false`    
    
### Usages example
```
<template>
    <div>
        <app-tag-manager
            :tags="tags"
            :list="tagOptions"
            list-value-field="name"
            :tag-preloader="tagPreloader"
            @storeTag="testStoreData"
            @attachTag="attachTag"
            @detachTag="detachTag"
        />
    </div>
</template>

<script>
import CoreLibrary from "../../helpers/CoreLibrary";

export default {
    name: "TestTagManager",
    extends: CoreLibrary,
    data() {
        return {
            value: {},
            tagPreloader: false,
            tags: [],
            tagOptions: [
                {id: 1, name: 'Red', color: '#72C2EE'},
                {id: 2, name: 'Black', color: '#72f2ee'},
                {id: 3, name: 'Yellow', color: '#72C268'},
                {id: 4, name: 'Blue', color: '#F2C268'}
            ]
        }
    },
    methods: {
        testStoreData(arg) {
            console.log(arg,'store')
            this.tagPreloader = true;
            this.addNewTagCreateOption(arg);
        },
        attachTag(tagId) {
            this.tags.push(tagId)
            console.log(tagId,'attach')
        },
        detachTag(tagId) {
            let index = this.tags.indexOf(tagId);
            this.tags.splice(index, 1);
            console.log(tagId,'detach')
        },
        addNewTagCreateOption(value) {
            this.axiosPost({
                url: 'store-tag-options',
                data: value
            }).then(response => {
                this.tagOptions = response.data.list;
                this.tags.push(response.data.new_id);
            }).finally(() => {
                this.tagPreloader = false;
            });
        },
    }
}
</script>

```
