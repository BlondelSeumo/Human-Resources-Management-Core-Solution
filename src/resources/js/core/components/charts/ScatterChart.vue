<template>
    <div class="Chart">
        <chart-scatter :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartScatter from './helpers/ScatterChart';
    import {ChartMixin} from './mixins/ChartMixin.js';

    export default {
        name: "ScatterChart",
        components: {ChartScatter},
        mixins: [ChartMixin]
    }
</script>
