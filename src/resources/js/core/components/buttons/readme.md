## Developer guide for `<app-button/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - buttons
                    - AppButton.Vue

#### Register
For `<app-button/>` component, main Vue component is `AppButton.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-button', require('./components/buttons/AppButton').default)
```

#### Props

1. Uses Props

	1. className
        - `type` : `String`
        - `required` : `true`

    2. label
        - `type` : `String`
        - `required` : `true`

    3. isDisabled
        - `type` : `Boolean`
        - `required` : `true`
        - `default` : `false`

#### Methods
`submit` function is called to send the data to his parent component.

```
	- it has `$emit` to send data `submit` to its parent component.
	- it accept argument. 

```
#### Usages
`<app-button/>` can use any component file into the `html` tag.



```
************* `End-App-Button` **************
```



## Developer guide for `<app-dropdown-menu/>` component..

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - buttons
                    - DropdownMenu.Vue


#### Register
For `<app-dropdown-menu/>` component, main Vue component is `Badge.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-dropdown-menu', require('./components/buttons/DropdownMenu').default);
```


#### Props

1. Required Props

	1. title
        - `type` : `String`
        - `required` : `true`

    2. btnClass
        - `type` : `String`
        - `dafault` : `btn-success`
        
    2. list
        - `type` : `Array`
        - `required` : `true`


#### Usages
`<app-dropdown-menu/>` can use any component file into the `html` tag.


```
************* `End-Dropdown-Menu` **************
```


## Developer guide for `<app-load-more/>` component..

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - buttons
                    - LoadMore.Vue


#### Register
For `<app-load-more/>` component, main Vue component is `Badge.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-load-more', require('./components/buttons/LoadMore').default);
```


#### Props

1. Uses Props

	1. label
        - `type` : `String`
        - `default` : `load_more`

    2. preloader
        - `type` : `Boolean`
        - `default` : `false`

    3. disabled
        - `type` : `Boolean`
        - `default` : `false`

#### Methods

`submit` function is called to send the data to his parent component.

```
	- it has `$emit` to send data `submit` to its parent component.
	- it accept argument. 

```

#### Usages
`<app-load-more/>` can use any component file into the `html` tag.


```
************* `End-Load-More` **************
```


## Developer guide for `<app-pagination/>` component..

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - buttons
                    - Pagination.Vue


#### Register
For `<app-pagination/>` component, main Vue component is `Badge.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file.

```Resfister
	Vue.component('app-pagination', require('./components/buttons/Pagination').default);
```


#### Props

1. Uses Props

	1. totalRow
        - `type` : `Number`
        - `require` : `true`

    2. rowLimit
        - `type` : `Number`
        - `require` : `true`


#### Data

	`activePage` : `1`
	`addition` : `0`
   
#### computed

	`totalPage` function is used to sperate the total page from the `totalRow` and `rowLimit`.
   
#### Methods

`activated` function is called to `disable` and `active` bootstrap class use corrent component.

```
	- it send data to its current component.
	- it accept argument `page`. 

```

`nextArrow` function is called to go next page of corrent component page.

```
	- it send data to its current component.

```

`prevArrow` function is called to go previous page of corrent component page.

```
	- it send data to its current component.

```

`activatedPage` function is called to send the data to his parent component.

```
	- it has `$emit` to send data `submit` to its parent component.
	- it accept argument. 

```

#### Usages
`<app-pagination/>` can use any component file into the `html` tag.


```
************* `End-Pagination` **************
```
