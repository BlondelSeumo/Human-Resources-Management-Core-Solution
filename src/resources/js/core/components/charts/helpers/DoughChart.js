import {DoughChart} from './Base';

export default {
    extends: DoughChart,
    props: {
        data: {
            default: null,
            required: true
        },
        darkMode: {
            type: Boolean,
            required: true
        }
    },
    mounted() {
        this.renderDoughChart();
    },
    watch: {
        darkMode: {
            handler: 'renderDoughChart',
            immediate: false
        }
    },
    methods: {
        renderDoughChart() {
            this.renderChart(
                {
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
}
