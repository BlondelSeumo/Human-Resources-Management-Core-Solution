## Developer guide for `<app-chart/>` component

#### Folder Structure

From Laravel Application

- resources
    - js
        - core
            - components
                - charts
                    - Index.Vue
          
                    
#### Register
  For `<app-chart/>` component, main Vue component is `Index.Vue`. We had register this component in ```resources/js/core/components/coreApp.js``` file
                    
```js
    Vue.component('app-chart', require('./components/chart/Index').default);
```
#### Type Check

All component type checking and $props bind into `resources/js/core/components/chart/index.vue` file.

```html
    <bar-chart v-if="type === 'bar-chart'" :data="$props"/>
    <line-chart v-else-if="type === 'line-chart'" :data="$props"/>
    <pie-chart v-else-if="type === 'pie-chart'" :data="$props"/>
    <dough-chart v-else-if="type === 'dough-chart'" :data="$props"/>
    <reader-chart v-else-if="type === 'reader-chart'" :data="$props"/>
    <polar-chart v-else-if="type === 'polar-chart'" :data="$props"/>
    <bubble-chart v-else-if="type === 'bubble-chart'" :data="$props"/>
    <scatter-chart v-else-if="type === 'scatter-chart'" :data="$props"/>
    <horizontal-line-chart v-else-if="type === 'horizontal-line-chart'" :data="$props"/>
    <custom-line-chart v-else-if="type === 'custom-line-chart'" :data="$props"/>   
```

>**Note** :  
>
> - `type` attribute is required  
> - if you are not specified `type`, than can't find the orginal chart component.

#### Components
And we had imports all other components in `resources/js/core/components/chart/Index.Vue`

```js
    import BarChart from "./BarChart";
    import LineChart from "./LineChart";
    import PieChart from "./PieChart";
    import DoughChart from "./DoughChart";
    import ReaderChart from "./ReaderChart";
    import PolarChart from "./PolarChart";
    import BubbleChart from "./BubbleChart";
    import ScatterChart from "./ScatterChart";
    import HorizontalLineChart from "./HorizontalLineChart";
    import CustomLineChart from "./CustomLineChart";
    
```

#### Props
1. Common props

    1. type
        - `type` : `String`
        - `required` : `true`
        
    2. title
        - `type` : `String`
        
    3. height
        - `type` : `Number`
        - `default` : `400`
       
    4. width
        - `type` : `Number`
        - `default` : `600`
        
    5. withoutDecimalPoint //to remove decimal point number from chart bar
        - `type` : `Boolean`
        - `default` : `false`
    
    6. beginAtZero
        - `type` : `Boolean`
        - `default` : `true`
    
    7. minimumRange
        - `type` : `Number`
        
    8. maximumRange
        - `type` : `Number`

2. Different props    

    1. labels
            - `type` : `Array`
        
    2. dataSets
            - `type` : `Array`
            
### Usages of Labels and dataSets.

#### For `BarChart.vue` labels and dataSets will be ..

```
    labels: [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
    ],
    dataSets: [
        {
            label: 'Sales',
            backgroundColor: '#21252D',
            data: [40, 20, 12, 39, 10, 40, 39, 80, 40, 20, 12, 11]
        },
        {
            label: 'Purchase',
            backgroundColor: '#4466F2',
            data: [20, 40, 32, 49, 20, 50, 19, 30, 70, 20, 42, 21]
        }
    ]
                                  
```

#### For `LineChart.vue` labels and dataSets will be ..

```
    labels: [
        'January', 'February', 'March', 'April', 'May', 'June', 'July'
    ],
    dataSets: [
        {
            label: 'Data One',
            backgroundColor: 'rgba(106, 0, 138, 0.5)',
            data: [40, 39, 10, 40, 39, 80, 40]
        },
        {
            label: 'Data Two',
            backgroundColor: 'rgba(68, 102, 242, 0.5)',
            data: [60, 45, 35, 20, 65, 30, 70]
        },
    ]
                                  
```

#### For `HorizontalLineChart.vue` labels and dataSets will be ..
```
    labels: [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
    ],
    dataSets: [
        {
            label: 'Data One',
            backgroundColor: '#4466F2',
            data: [40, 20, 52, 39, 30, 60, 39, 80, 45, 30, 22, 15]
        }
    ]
                                  
```

#### For `CustomLineChart.vue` labels and dataSets will be ..

```
    labels: [
        'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
    ],
    dataSets: [
        {
            label: 'Data One',
            backgroundColor: 'rgba(68, 102, 242, 0.5)',
            data: [70, 39, 20, 50, 29, 60, 10]
        }
    ]
                                  
```

#### For `DoughnutChart.vue` labels and dataSets will be ..

```
    labels: [
        'VueJs', 'EmberJs', 'ReactJs', 'AngularJs'
    ],
    dataSets: [
        {
            backgroundColor: [
                '#f96868',
                '#4466F2',
                '#2e383e',
                '#6a008a'
            ],
            data: [20, 25, 40, 15]
        }
    ]
                                  
```

#### For `PieChart.vue` labels and dataSets will be ..

```
    labels: ['VueJs', 'EmberJs', 'ReactJs', 'AngularJs'],
    dataSets: [
        {
            backgroundColor: [
                '#f96868',
                '#4466F2',
                '#2e383e',
                '#6a008a'
            ],
            data: [20, 25, 40, 15]
        }
    ]                                
```


#### For `PolarChart.vue` labels and dataSets will be ..

```
    labels: [
        'Eating', 'Drinking', 'Sleeping', 'Designing', 'Coding', 'Cycling', 'Running'
    ],
    dataSets: [
        {
            label: 'My First dataset',
            backgroundColor: '#4466F2',
            pointBackgroundColor: 'rgba(179,181,198,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(179,181,198,1)',
            data: [65, 59, 76, 70, 56, 55, 40]
        },
        {
            label: 'My Second dataset',
            backgroundColor: '#6a008a',
            pointBackgroundColor: 'rgba(255,99,132,1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(255,99,132,1)',
            data: [28, 80, 40, 19, 96, 27, 100]
        }
    ]
                                  
```

#### For `ReaderChart.vue` labels and dataSets will be ..
```
    labels: [
        'Eating', 'Drinking', 'Sleeping', 'Designing', 'Coding', 'Cycling', 'Running'
    ],
    dataSets: [
        {
            label: 'My First dataset',
            backgroundColor: 'rgba(68, 102, 242, 0.5)',
            borderColor: 'rgba(179,181,198,.5)',
            pointBackgroundColor: 'rgba(70, 195, 95, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(179,181,198,1)',
            data: [65, 59, 90, 81, 56, 55, 40]
        },
        {
            label: 'My Second dataset',
            backgroundColor: 'rgba(106, 0, 138, 0.5)',
            borderColor: 'rgba(0, 143, 214, .5)',
            pointBackgroundColor: 'rgba(70, 195, 95, 1)',
            pointBorderColor: '#fff',
            pointHoverBackgroundColor: '#fff',
            pointHoverBorderColor: 'rgba(255,99,132,1)',
            data: [28, 48, 40, 19, 96, 27, 100]
        }
    ]
                                  
```

#### For `ScatterChart.vue` labels and dataSets will be ..

```
    dataSets: [
        {
            label: 'Scatter Dataset 1',
            fill: false,
            borderColor: '#46c35f',
            backgroundColor: '#46c35f',
            data: [
                    {
                    x: -2,
                    y: 4
                }, {
                    x: -1,
                    y: 1
                }, {
                    x: 0,
                    y: 0
                }, {
                    x: 1,
                    y: 1
                }, {
                    x: 2,
                    y: 4
                }
            ]
        },
        {
            label: 'Scatter Dataset 2',
            fill: false,
            borderColor: '#4466F2',
            backgroundColor: '#4466F2',
            data: [
                    {
                    x: -2,
                    y: -4
                }, {
                    x: -1,
                    y: -1
                }, {
                    x: 0,
                    y: 1
                }, {
                    x: 1,
                    y: -1
                }, {
                    x: 2,
                    y: -4
                }
            ]
        }
    ]
                                  
```
