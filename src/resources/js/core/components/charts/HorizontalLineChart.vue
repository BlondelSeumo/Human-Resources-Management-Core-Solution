<template>
    <div class="Chart">
        <chart-horizontal-line :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartHorizontalLine from './helpers/HorizontalLineChart';
    import {ChartMixin} from "./mixins/ChartMixin.js";

    export default {
        name: "HorizontalChart",

        components: {ChartHorizontalLine},

        mixins: [ChartMixin]
    }
</script>
