<template>
    <div class="Chart">
        <chart-custom-line :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartCustomLine from './helpers/CustomLineChart';
    import {ChartMixin} from "./mixins/ChartMixin.js";

    export default {
        name: "CustomLineChart",

        components: {ChartCustomLine},

        mixins:[ChartMixin]
    }
</script>
