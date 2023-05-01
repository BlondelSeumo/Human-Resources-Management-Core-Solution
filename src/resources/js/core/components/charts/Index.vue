<template>
    <div>
        <bar-chart v-if="type === 'bar-chart'" :data="$props"/>
        <line-chart v-else-if="type === 'line-chart'" :data="$props"/>
        <pie-chart v-else-if="type === 'pie-chart'" :data="$props"/>
        <dough-chart v-else-if="type === 'dough-chart'" :data="$props"/>
        <reader-chart v-else-if="type === 'reader-chart'" :data="$props"/>
        <polar-chart v-else-if="type === 'polar-chart'" :data="$props"/>
        <bubble-chart v-else-if="type === 'bubble-chart'" :data="$props"/>
        <scatter-chart v-else-if="type === 'scatter-chart'" :data="$props"/>
        <horizontal-line-chart v-else-if="type === 'horizontal-line-chart'" :data="$props"/>
        <custom-line-chart v-else-if="type === 'custom-line-chart'" :data="$props"/>
    </div>
</template>

<script>
import BarChart from "./BarChart";
import LineChart from "./LineChart";
import PieChart from "./PieChart";
import DoughChart from "./DoughChart";
import ReaderChart from "./ReaderChart";
import PolarChart from "./PolarChart";
import BubbleChart from "./BubbleChart";
import ScatterChart from "./ScatterChart";
import HorizontalLineChart from "./HorizontalLineChart";
import CustomLineChart from "./CustomLineChart";

export default {
    name: "AppChart",

    components: {
        BarChart,
        LineChart,
        PieChart,
        DoughChart,
        ReaderChart,
        PolarChart,
        BubbleChart,
        ScatterChart,
        HorizontalLineChart,
        CustomLineChart
    },
    props: {
        type: {
            required: true,
            type: String
        },
        labels: {
            type: Array
        },
        dataSets: {
            type: Array,
        },
        height: {
            type: Number,
            default: 400
        },
        width: {
            type: Number,
            default: 600
        },
        withoutDecimalPoint: {
            type: Boolean,
            default: false
        },
        beginAtZero: {
            type: Boolean,
            default: true
        },
        minimumRange: {
            type: Number
        },
        maximumRange: {
            type: Number
        }
    }
}
</script>
