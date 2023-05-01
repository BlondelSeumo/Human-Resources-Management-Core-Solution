<template>
    <div class="Chart">
        <chart-polar :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartPolar from './helpers/PolarChart';
    import {ChartMixin} from './mixins/ChartMixin.js';

    export default {
        name: "PolarChart",

        components: {ChartPolar},
        mixins: [ChartMixin]
    }
</script>
