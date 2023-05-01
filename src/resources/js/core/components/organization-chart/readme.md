## Developer guide for `<app-organization-chart/>` component

### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - organization-chart
                    - OrganizationChart.Vue
                    

### Register

```js
    Vue.component("app-organization-chart", require('./components/organization-chart/OrganizationChart').default);
```

### Props

1. chartData
    - `type` : `Object`
    - `required` : `true`
    ##Example
    ```
    {
        'id': '5', 'name': 'Hei Hei', 'title': 'senior engineer',
        'children': [
            {'id': '6', 'name': 'Pang Pang', 'title': 'engineer'},
            {'id': '7', 'name': 'Xiang Xiang', 'title': 'UE engineer'}
        ]
    }
    ```    

   
2. height
    - `type` : `Number`
    - `default` : `530`
3. druggable
    - `type` : `Boolean`
    - `default` : `false`

###Usages
 - Example of using organization component
 
```
<template>
    <div class="content-wrapper">
        <div class="card card-with-shadow border border-0 custom-scrollbar">
            <app-organization-chart :chart-data="chartData" :druggable="true" @selected-item="selectedItem"></app-organization-chart>
        </div>
    </div>
</template>

<script>
    export default {
        name: "Example",
        data() {
            return {
                chartData: {
                    'id': '1',
                    'name': 'Lao Lao',
                    'title': 'general manager',
                    'children': [
                        {'id': '2', 'name': 'Regfire solutions LTD', 'title': 'department manager'},
                        {
                            'id': '3', 'name': 'Gain solutions LTD', 'title': 'department manager',
                            'children': [
                                {'id': '4', 'name': 'Tie Hua', 'title': 'senior engineer'},
                                {
                                    'id': '5', 'name': 'Hei Hei', 'title': 'senior engineer',
                                    'children': [
                                        {'id': '6', 'name': 'Pang Pang', 'title': 'engineer'},
                                        {
                                            'id': '7', 'name': 'Xiang Xiang', 'title': 'UE engineer',
                                        }
                                    ]
                                }
                            ]
                        },
                        {'id': '8', 'name': 'Hong Miao', 'title': 'department manager'},
                        {'id': '9', 'name': 'Chun Miao', 'title': 'department manager'}
                    ]
                }
            }
        },
        methods: {
            selectedItem(value) {
                console.log(value);
            }
        }
    }
</script>
```