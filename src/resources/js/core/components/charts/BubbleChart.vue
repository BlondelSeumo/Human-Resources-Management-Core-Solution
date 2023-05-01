<template>
    <div class="Chart">
        <chart-bubble :data="data" :height="data.height" :width="data.width" :dark-mode="darkMode"/>
    </div>
</template>

<script>
    import ChartBubble from './helpers/BubbleChart';
    import {ChartMixin} from "./mixins/ChartMixin.js";

    export default {
        name: "BubbleChart",

        components: {ChartBubble},

        mixins: [ChartMixin]
    }
</script>
