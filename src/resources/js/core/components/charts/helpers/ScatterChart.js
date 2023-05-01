import {ScatterChart} from './Base';

export default {
    extends: ScatterChart,
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
