import {ReaderChart} from './Base';

export default {
    extends: ReaderChart,
    props: {
        data: {
            default: null,
            required: true
        }
    },
    mounted() {
        this.renderChart({
            labels: this.data.labels,
            datasets: this.data.dataSets
        }, {responsive: true, maintainAspectRatio: false})
    }
}
