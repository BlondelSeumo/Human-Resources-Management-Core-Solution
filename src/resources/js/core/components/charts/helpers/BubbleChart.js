import {BubbleChart} from './Base';

export default {
    extends: BubbleChart,
    props: {
        data: {
            default: null,
            required: true
        }
    },
    mounted() {
        this.renderChart({
            datasets: this.data.dataSets
        }, {responsive: true, maintainAspectRatio: false})
    }
}
