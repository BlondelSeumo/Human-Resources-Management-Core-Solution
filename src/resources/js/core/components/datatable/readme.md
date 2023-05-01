# Developer guide for `<app-table/>` component

### Components

And we had imports all other components in `resources/js/core/components/datatable/Index.Vue`

```Vue
    import DataTable from "./DataTable";
    import ManageColumns from "./ManageColumns";
```

and also used `<app-filter/>` component. [click here](../filter/readme.md)

### Props

1. id
    - `type` : `String`,
    - `required` : `true`
2. options
    - `type` : `Object`,
    - `required` : `true`
3. cardView
    - `type` : `Boolean`
    - Default value `false`
    - This options is used for show table data with your view

### Reload Datatable form anywhere.

* Can use after

```
    this.$hub.$emit('reload-'+tableId);
```

### Reload Datatable (But if you want to stay same page)

- Pass anything as parameter except `true` like:

```
    this.$hub.$emit('reload-'+tableId, false);
```

### Available options in datatable

1. id
    - `type` : `String`
    - The option `id` is uniquely identified a table.
2. name
    - `type` : `String`
    - Every table has a `name` option.
3. columns
    - `type` : `Array of Object`
    - Title should be unique.
    - key for all column if you want `titleAlignment: 'right'` // available right default (left)
    - This options specifies the columns of a table. Available columns type -
    - ```HTML
      {
          title: 'Invoice',                   // This is a link type column
          type: 'link',
          key: 'invoice',
          isVisible: true,
          modifier: (value, row) => {
            return `test/${row.invoice}/test`;
            }
      },
      {
          title: 'name',                      // This is a text type column
          type: 'text',
          key: 'name'
      },
      {
          title: 'Image',                     // Image type column
          type: 'image',
          key: 'image',
          altText: "No found",
          className: "avatars-group-w-50",
          default: "default image url",
          isVisible: true
      },
      {
          title: 'User',                      // This is a media object type column
          type: 'media-object',
          key: 'profile_picture',
          mediaTitleKey: 'name',        // key for titile
          mediaSubtitleKey: 'email',       // key for sub titile
          default: "default image url",
          isVisible: true,
          modifier:(value, row)=>{
            return value ; // imag url 
         }
      },
      {
          title: 'component',                 // Component type column
          type: 'component',
          key: 'users',
          isVisible: true,
          componentName: 'test-avatar-group',
      },
      {
          title: 'status',                    // custom-class type column
          type: 'custom-class',
          key: 'status',
          isVisible: true,
          modifier: (value) => {
              if (value === 'Active') return "badge badge-pill badge-primary";
              return "badge badge-pill badge-danger";
          }
      },
      {
          title: 'status 2',                  // custom-html type column
          type: 'custom-html',
          key: 'status2',
          isVisible: false,
          modifier: (value) => {
              if (value === 'Active') return `<h1>${value}</h1>`;
              return `<h2>${value}</h2>`;
          }
      },
      {
          title: 'user',                      // object type column
          type: 'object',
          key: 'created_by',
          modifier: (value, row) => {
              return value.first_name+' '+value.last_name
          }
      },
      {
            title: 'Dynamic Content',
            type: 'dynamic-content',
            key: 'name',
            label: 'click me',
            icon: 'check',
            className: 'btn btn-success',
            modifier: (value, row) => {
                return{
                    isValue: false,
                    value: 'dynamic-contnent/'+row.invoice
                    // value: row.invoice
                }
            }
      },
      {
          title: 'Age',               //Button type column
          type: 'button',
          key: 'age',
          sortable: true,
          className: 'btn btn-success',
          // icon: 'check',
          label: 'click me',
          isVisible: true,
          modifier: (value, row) => {
              return 'Manage button'  // whatever you return is consider as button label excepts Boolean `false` 
          }                           // if You return false it will hide the button else all are will be consider as button label
      },
      {
            title: 'Something',
            type: 'expandable-column',
            key: 'expandable_data',
            className: 'success', // Should be warning, success, primary, danger, dark, light, secondary
            isVisible: true, // not required
            componentName: 'expandable-view',
            showTitle: 'Show', // not required. if not given, must send in modifier
            hideTitle: 'Hide', // not required. if not given, must send in modifier
            showIcon: 'eye', // not required
            hideIcon: 'eye-off', // not required
            modifier: (value, row) => {
                let returnObj = {};
                returnObj.prefixData = 12; // not required // default false
                returnObj.title = 'expand'; // Button Title
                returnObj.expandable = true; // Expandable and Component render (default true)
                //returnObj.button = true; // Clickable Button  (Default false and show badges)
                returnObj.visible = true; // Column visibility (Default true)
                return returnObj;
            }
        },
      {
          title: 'Action',                    // Action type column
          type: 'action',
          key: 'invoice',       // If you want
          isVisible: true
      },
      ```
4. showHeader
    - `type` : `Boolean`
    - Default value `true`
    - By giving this option `showHeader` value `true/false` you can show or hide the table header.
5. url
    - `type` : `String`
    - Under this option you have to put the url where from.
6. orderBy
    - `type` : `String`
    - Default value `DESC`
    - `orderBy` option used to sort the table in ascending or descending order.
7. showFilter
    - `type` : `Boolean`
    - Default value `true`
    - This option takes a boolean value to show or hide filters from a datatable.
8. datatableType
    - `type` : `String`
    - Default value `default`
    - There are two datatable type available in `datatableType` option `default/cardView`. `default` type shows all the
      filters outside of the table card and `cardView` type shows the filters inside the table card.
9. showSearch
    - `type` : `Boolean`
    - Default value `true`
    - `showSearch` option used to show or hide the search filter.
10. filters
    - in `dropdown-menu-filter`, `radio`, `avatar-filter`, `dropdown-filter` you can send `initValue` or `active` in 
      filter object
    - if you click any filter after boot, create event `getFilteredValues` form child accessed by <app-table>
    - `type` : `Array of Object`
    - This options specifies the filters of a table. Available keys in a 'filters' is
    ### Description related component : `<app-filter/>` [click here](../filter/readme.md)

11. tablePaddingClass
    - `type` : `String`
    - Default value `p-primay`
    - This options used to padding of table container

12. tableShadow
    - `type` : `Boolean`
    - Default value `true`
    - This options used to shadow of table container
    
13. cardViewComponent
    - `type` : `String`
    - if `cardView` is `true`, it will be `required`.
    - The component in which you want to show table data.

14. cardViewEmptyBlock // Only work in cardView true
    - `type` : `Boolean`
    - Default `true`
    - use `false` for hide emptyBlock

15. cardViewPagination // Only work in cardView true
    - `type` : `Boolean`
    - Default `true`
    - use `false` for hide pagination
    
16. cardViewQueryParams // Only work in cardView true
    - `type` : `Object`
    - Default `{}`
    - use if you want to send any queryString with table
    ```
    cardViewQueryParams: 
    {
        'view_type': 'calendar'
    },
    ```

17. enableCookie
    - `type` : `Boolean`
    - `default` : `true`
    - will automatically save column in cookies
    
18. managePagination
    - `type` : `Boolean`
    - `default` : `true`
    - send false if you don't want this
        
19. dataKey
    - `type` : `String`
    - `default` : `data`
    - in your paginate response which key hold the actual data of your table.

20. enableRowSelect
    - `type` : `Boolean`
    - This options used to enable multiple row selection of table. if true.
    - emit `getRows` for the row which are selected.

21. afterRequestSuccess
    - `type` : `Function`
    - if you want you can use this function like
    ```
    afterRequestSuccess: (res) => {
        console.log(res)
    }
    ```
22. afterRequestSuccess
    - `type` : `Function`
    - if you want you can use this function like
    ```
    afterRequestError: (err) => {
        console.log(err)
    }
    ```
23. datatableWrapper
    - `type` : `Boolean`
    - `default` : `true`
    - use `false` for only datatable without internal filter and search
    - if you provide `false` it always expect props `filtededData` & `search`

24. showCount
    - `type` : `Boolean`
    - `default` : `false`
    - It shows you count number of data showing
    
25. showClearFilter
    - `type` : `Boolean`
    - `default` : `false`
    - It accesses you to clear all filter
    
26. paginationOptions
    - `type` : `array`
    - `default` : `[10, 20, 30]`
    - for per page pagination limit
    
26. sortByFilter
    - `type` : `Object`
    - Hold some key value for sort by filter
    ```
    sortByFilter: {
        isVisible: true,
        label: 'Sort By',
        key: 'sort_by',
        options: ['most_recent','most_viewed','top_rated']
    }
    ```

27. paginationBlockClass
    - `type` : `String`
    - `default` : `mt-primary`
    - if wrapper false then `p-primary`

27. showAction
    - `type` : `Boolean`
    - Default value `true`
    - This options used to show/hide the action buttons in a table

28. actionType
    - `type` : `String`
    - Two type of action button available in datatable. One is `default` which gives a group of actions with icon view
      and another one is `dropdown` which give a dropdown action button.

29. actions
    - `type` : `Array`
    - trigger an event name `action` you can receive `row, actionObj, active` in parameter.
    - `action` option takes an array of object as action button
    - `uniqueKey` key if you want  
    - `icon` only work for `default` action
    ```
    [
        {},
        {
            title: 'Active',
            modifier: (row) => {
                if (row.status === 'Inactive') {
                    return true
                }
            }
        },
        {
            title: 'Inactivate',
            modifier: (row) => {
                if (row.status === 'Active') {
                    return true
                }
            }
        },
        {
            title: 'Edit',
            icon: 'edit',
            type: 'none',
            component: 'test-modal',
            url: 'edit-test',
            uniqueKey: 'invoice',
        },
        {
            title: 'Delete',
            icon: 'trash',
            component: 'test-modal',
            type: 'none',
            url: '',
        },
        {
            title: 'view',
            icon: 'trash',
            type: 'none',
            url: '',
        }
    ]
    ```
    

### Usages example
```
<template>
    <div class="content-wrapper">
        <app-breadcrumb :page-title="'Test Table'" :icon="'settings'"/>

        <div class="mb-primary">
            <button class="btn btn-primary" type="button" @click.prevent="testCol">Changed</button>
            <button class="btn btn-secondary" type="button" @click.prevent="testColRev">Changed reverse</button>
        </div>

        <app-table
            :id="'test-table-core'"
            :options="options"
            @action="getAction"
            @getRows="getSelectedRows"
            @getFilteredValues="filteredValues"
        />

        <test-modal v-if="isShowTestModal" @close-modal="closeModal"/>

        <app-confirmation-modal
            v-if="isShowDeleteModal"
            icon="check-circle"
            title="Hand's Up"
            sub-title="This content will be deleted permanently"
            message="Are you sure?"
            modal-class="success"
            modal-id="delete-modal"
            @confirmed="confirmed"
            @cancelled="cancelled"
        />
    </div>
</template>

<script>
import TestModal from "../modal/TestModal";

export default {
    name: "Example",

    components: {TestModal},

    data() {
        return {
            isShowTestModal: false,
            isShowDeleteModal: false,
            defaultColumns: [
                {},
                {
                    title: 'Invoice',
                    type: 'link',
                    key: 'invoice',
                    isVisible: true,
                    // sortable: true,
                    modifier: (value, row) => {
                        return `test/${row.invoice}/test`;
                    }
                },
                {
                    title: 'Name',
                    type: 'custom-html',
                    key: 'name',
                    isVisible: true,
                    // sortable: true,
                    titleAlignment: 'right', // available right default (left)
                    modifier: (value) => {
                        return `<p class="text-right m-0">${value}</p>`
                    }
                },
                {
                    title: 'Something',
                    type: 'expandable-column',
                    key: 'expandable_data',
                    className: 'success', // Should be warning, success, primary, danger, dark, light, secondary
                    isVisible: true, // not required
                    componentName: 'expandable-view',
                    showTitle: 'Show', // not required. if not given, must send in modifier
                    hideTitle: 'Hide', // not required. if not given, must send in modifier
                    showIcon: 'eye', // not required
                    hideIcon: 'eye-off', // not required
                    modifier: (value, row) => {
                        let returnObj = {};
                        returnObj.prefixData = 12; // not required // default false
                        returnObj.title = 'expand'; // Button Title
                        returnObj.expandable = true; // Expandable and Component render (default true)
                        returnObj.button = true; // Clickable Button  (Default false and show badges)
                        returnObj.visible = true; // Column visibility (Default true)
                        if (!value) {
                            returnObj.expandable = false
                        }
                        return returnObj;
                    }
                },
                {
                    title: 'Age',
                    type: 'button',
                    key: 'age',
                    className: 'btn btn-success',
                    // icon: 'check',
                    label: 'click me',
                    isVisible: true,
                    modifier: (value, row) => {
                        if (value > 30) return false;
                        return value
                    }
                },
                {
                    title: 'Dynamic Content',
                    type: 'dynamic-content',
                    key: 'name',
                    label: 'click me',
                    // icon: 'check',
                    className: 'btn btn-success',
                    modifier: (value, row) => {
                        return {
                            isValue: false,
                            value: 'dynamic-contnent/' + row.invoice
                            // value: row.invoice
                        }
                    }
                },
                {
                    title: 'Image',
                    type: 'image',
                    key: 'image',
                    altText: "No found",
                    className: "avatars-group-w-50",
                    isVisible: false,
                    default: "https://images.unsplash.com/photo-1537815749002-de6a533c64db?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1090&q=80"
                },
                {
                    title: 'Media Object',
                    type: 'media-object',
                    key: 'image',
                    mediaTitleKey: 'name',
                    mediaSubtitleKey: 'email',
                    default: "https://images.unsplash.com/photo-1537815749002-de6a533c64db?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1090&q=80",
                    isVisible: false,
                    modifier: (value, row) => {
                        return value; // img url
                    }
                },
                {
                    title: 'component',
                    type: 'component',
                    key: 'users',
                    isVisible: false,
                    componentName: 'test-avatar-group',
                },
                {
                    title: 'Tags',
                    type: 'component',
                    key: 'users',
                    componentName: 'test-tag-manager',
                },
                {
                    title: 'status',
                    type: 'custom-class',
                    key: 'status',
                    isVisible: false,
                    modifier: (value) => {
                        if (value === 'Active') return "badge badge-pill badge-primary";
                        return "badge badge-pill badge-danger";
                    }
                },
                {
                    title: 'status 2',
                    type: 'custom-html',
                    key: 'status2',
                    isVisible: false,
                    modifier: (value) => {
                        if (value === 'Active') return `<h1>${value}</h1>`;
                        return `<h2>${value}</h2>`;
                    }
                },
                {
                    title: 'user',
                    type: 'object',
                    key: 'created_by',
                    isVisible: false,
                    modifier: (value, row) => {
                        return value.first_name + ' ' + value.last_name
                    }
                },
                {
                    title: 'Action',
                    type: 'action',
                    isVisible: true,
                    key: 'invoice'
                },
            ],
            options: {
                afterRequestError: function (error){
                    console.log('Error happened', error)
                },
                afterRequestSuccess: ({data}) => {
                    console.log(data.data)
                },
                name: 'TestTable',
                url: 'test-value',
                dataKey: 'data',
                showHeader: true,
                paginationOptions: [5, 10, 15],
                datatableWrapper: true,
                paginationBlockClass: 'mt-primary',
                showCount: true,
                showClearFilter: true,
                managePagination: true,
                cardViewComponent: 'test-form',
                cardViewEmptyBlock: false,
                cardViewPagination: false,
                cardViewQueryParams: {
                    'view_type': 'calendar'
                },
                sortByFilter: {
                    isVisible: true,
                    label: 'Sort By',
                    key: 'sort_by',
                    options: ['most_recent','most_viewed','top_rated'],
                },
                enableCookie: true,
                columns: [],
                filters: [
                    {
                        "title": "Select an option",
                        "type": "dropdown-menu-filter",
                        "key": "dropdown-menu",
                        "initValue": 3,
                        "option": [
                            {
                                id: 1,
                                name: 'How many deals were won?',
                                icon: 'award'
                            },
                            {
                                id: 2,
                                name: 'How many deals were lost?',
                                icon: 'frown'
                            },
                            {
                                id: 3,
                                name: 'How many deals were average?',
                            }
                        ],
                        listValueField: 'name'
                    },
                    {
                        "title": "Date Range",
                        "type": "range-picker",
                        "key": "date",
                        "option": ["today", "thisMonth", "last7Days", "nextYear"]
                    },
                    {
                        type: 'date',
                        key: 'single-date',
                    },
                    {
                        title: 'Toggle Button',
                        type: 'toggle-filter',
                        key: 'is_active',
                        buttonLabel: {
                            active: 'Active',
                            inactive: 'Inactive'
                        },
                        header: {
                            title: "Want to filter only active",
                            description: "You can filter your data table which are created based on segment"
                        }
                    },
                    {
                        "title": "Sending rate",
                        "type": "range-filter",
                        "key": "sending rate",
                        "maxTitle": "Max usd",
                        "minTitle": "Min usd",
                        "sign": "$",
                        "maxRange": 60,
                        "minRange": 20
                    },
                    {
                        "title": "Status",
                        "type": "checkbox",
                        "key": "status",
                        "option": [
                            {
                                id: 1,
                                name: "active"
                            },
                            {
                                id: 2,
                                name: "invited"
                            },
                            {
                                id: 3,
                                name: "subscribed"
                            },
                            {
                                id: 4,
                                name: "multiple word",
                                disabled: true
                            },
                            {
                                id: 5,
                                name: "Option 1",
                                disabled: true
                            },
                            {
                                id: 6,
                                name: "Option 2"
                            }

                        ],
                        listValueField: 'name'
                    },
                    {
                        "title": "List with segment",
                        "type": "radio",
                        "key": "list-with-segment",
                        // "initValue": 2,
                        "header": {
                            "title": "Want to filter your list?",
                            "description": "You can filter your data table which are created based on segment"
                        },
                        "option": [
                            {
                                id: 1,
                                name: "with segment"
                            },
                            {
                                id: 2,
                                name: "without segment"
                            }
                        ],
                        listValueField: 'name'
                    },
                    {
                        "title": "Search & select",
                        "type": "drop-down-filter",
                        "key": "search-select",
                        active: 3,
                        "option": [
                            {id: 1, name: 'Cricket'},
                            {id: 2, name: 'Football'},
                            {id: 3, name: 'Badminton'},
                            {id: 4, name: 'Option 4', disabled: true},
                            {id: 5, name: 'Option 5'},
                            {id: 6, name: 'Option 6'},
                        ],
                        listValueField: 'name'
                    },
                    {
                        "title": "Multi Select",
                        "type": "multi-select-filter",
                        "key": "multi select",
                        "option": [
                            {id: 1, name: 'Cricket'},
                            {id: 2, name: 'Football'},
                            {id: 3, name: 'Badminton'},
                            {id: 4, name: 'Option 4'},
                            {id: 5, name: 'Option 5'},
                            {id: 6, name: 'Option 6'},
                        ],
                        listValueField: 'name'
                    },
                    {
                        "title": "Avatar Filter",
                        active: 1,
                        "type": "avatar-filter",
                        "key": "user",
                        "option": [
                            {
                                id: 1,
                                name: 'Cricket',
                                status: 'success',
                                profile_picture: {
                                    id: 1,
                                    full_url: "https://images.unsplash.com/photo-1506919258185-6078bba55d2a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60",
                                    type: "profile_picture"
                                }
                            },
                            {
                                id: 2,
                                name: 'Football',
                                status: 'danger',
                                img: ''
                            },
                            {
                                id: 3,
                                name: 'Badminton',
                                status: 'success',
                                profile_picture: {
                                    id: 1,
                                    full_url: "https://images.unsplash.com/photo-1506919258185-6078bba55d2a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60",
                                    type: "profile_picture"
                                }
                            },
                            {
                                id: 4,
                                name: 'Option 4',
                                status: 'success',
                            },
                            {
                                id: 5,
                                name: 'Option 5',
                                status: 'warning',
                                img: 'https://images.unsplash.com/photo-1506919258185-6078bba55d2a?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=500&q=60'
                            },
                            {
                                id: 6,
                                name: 'Option 6',
                                status: 'success',
                            }
                        ],
                        listValueField: 'name',
                        "imgRelationship": 'profile_picture',
                        "imgKey": 'full_url'
                    },
                ],
                enableRowSelect: true,
                // tablePaddingClass: 'p-primary',
                tableShadow: true,
                showSearch: true,
                showFilter: true,
                paginationType: "pagination",
                responsive: true,
                rowLimit: 10,
                showAction: true,
                orderBy: 'desc',
                actionType: "dropdown",
                actions:
                    [
                        {},
                        {
                            title: 'Active',
                            modifier: (row) => {
                                if (row.status === 'Inactive') {
                                    return true
                                }
                            }
                        },
                        {
                            title: 'Inactivate',
                            modifier: (row) => {
                                if (row.status === 'Active') {
                                    return true
                                }
                            }
                        },
                        {
                            title: 'Edit',
                            icon: 'edit',
                            type: 'none',
                            component: 'test-modal',
                            url: 'edit-test',
                            uniqueKey: 'invoice',
                        },
                        {
                            title: 'Delete',
                            icon: 'trash',
                            component: 'test-modal',
                            type: 'none',
                            url: '',
                        },
                        {
                            title: 'view',
                            icon: 'trash',
                            type: 'page',
                            url: '',
                        }
                    ],
            },
            tenantInfo: {
                logo: {
                    value: 'images/core.png',
                },
                name: 'Bkash',
                admin: {
                    full_name: 'demo@mail.com'
                },
                status: {
                    translated_name: 'Invited',
                    name: 'status_invited'
                },
                created_by: {
                    full_name: 'John doe'
                },
                short_name: 'bkash43',
                tenant_group: {
                    name: 'Telecom'
                },
                url: 'sendex.gainhq.com',
            },
            actions: [
                {
                    "title": "Edit",
                },
                {
                    "title": "Delete",
                }
            ]
        }
    },
    created() {
        this.options.columns = this.defaultColumns;
    },
    methods: {
        getSelectedRows(value) {
            // console.log(value);
        },
        filteredValues(value) {
            // console.log(value);
        },
        getAction(row, actionObj, active) {
            // console.log(actionObj.title);
            if (actionObj.title == 'Edit') {
                this.openModal();
            } else if (actionObj.title == 'Delete') {
                this.openDeleteModal();
            }
        },
        openModal() {
            this.isShowTestModal = true;

            setTimeout(function () {
                $("#exampleModal").modal('show');
            });
        },
        openDeleteModal() {
            this.isShowDeleteModal = true;

            setTimeout(function () {
                $("#delete-modal").modal('show');
            });
        },
        confirmed() {
            this.isShowDeleteModal = false;
            console.log('Clicked Confirmed');
        },
        cancelled() {
            this.isShowDeleteModal = false;
            console.log('Clicked Canceled');
        },
        closeModal() {
            this.isShowTestModal = false;
        },
        testCol() {
            this.options.columns = [...this.defaultColumns, {
                'title': 'Test',
                'type': 'text',
                'key': 'invoice',
                'isVisible': true
            }]
            this.options.filters.find(item => item.type === 'radio').initValue = 1
            this.options.filters.find(item => item.type === 'avatar-filter').active = 3
            this.options.filters.find(item => item.type === 'dropdown-menu-filter').initValue = 1
        },
        testColRev() {
            this.options.columns = [...this.defaultColumns]
        }
    }
}
</script>

```

