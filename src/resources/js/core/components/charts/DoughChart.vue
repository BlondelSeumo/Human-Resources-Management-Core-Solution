<template>
    <div class="Chart">
        <chart-dough :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartDough from './helpers/DoughChart';
    import {ChartMixin} from "./mixins/ChartMixin.js";

    export default {
        name: "DoughChart",

        components: {ChartDough},

        mixins: [ChartMixin]
    }
</script>
