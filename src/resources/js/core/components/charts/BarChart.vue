<template>
    <div class="Chart">
        <chart-bar :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartBar from './helpers/BarChart';
    import {ChartMixin} from "./mixins/ChartMixin.js";

    export default {
        name: "BarChart",

        components: {ChartBar},

        mixins: [ChartMixin]
    }

</script>
