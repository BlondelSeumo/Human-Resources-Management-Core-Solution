<template>
    <div class="Chart">
        <chart-line :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartLine from './helpers/LineChart';
    import {ChartMixin} from "./mixins/ChartMixin.js";

    export default {
        name: "LineChart",

        components: {ChartLine},

        mixins: [ChartMixin]
    }
</script>
