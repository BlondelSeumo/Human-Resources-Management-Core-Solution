<template>
    <div class="Chart">
        <chart-reader :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartReader from './helpers/ReaderChart';
    import {ChartMixin} from './mixins/ChartMixin.js';
    export default {
        name: "ReaderChart",

        components: {ChartReader},

        mixins: [ChartMixin]
    }
</script>
