<template>
    <div class="Chart">
        <chart-pie :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartPie from './helpers/PieChart';
    import {ChartMixin} from './mixins/ChartMixin.js';

    export default {
        name: "PieChart",

        components: {ChartPie},

        mixins: [ChartMixin]
    }
</script>
