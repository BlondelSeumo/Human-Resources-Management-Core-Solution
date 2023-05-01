import {PieChart} from './Base';

export default {
    extends: PieChart,
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
            },
            {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false,
                }
            }
        )
    }
}
