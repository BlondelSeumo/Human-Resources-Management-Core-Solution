import {BarChart} from './Base';

export default {
    extends: BarChart,
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
        this.renderBarChart();
    },
    watch: {
        darkMode: {
            handler: 'renderBarChart',
            immediate: false
        }
    },
    methods: {
        renderBarChart() {
            let xAxisColor = '#F0F2F5',
                yAxisColor = '#F0F2F5';
            if (this.darkMode) {
                xAxisColor = '#2f3541'
                yAxisColor = '#2f3541'
            } else {
                xAxisColor = '#F0F2F5'
                yAxisColor = '#F0F2F5'
            }
            this.renderChart({
                    labels: this.data.labels,
                    datasets: this.data.dataSets
                },
                {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        xAxes: [{
                            gridLines: {
                                borderDash: [8, 4],
                                zeroLineBorderDash: [8, 4],
                                color: xAxisColor,
                                drawTicks: false
                            },
                            ticks: {
                                lineHeight: 1.5,
                                fontFamily: '\'Poppins\', sans-serif',
                                fontSize: 11.262,
                                fontColor: '#9397A0',
                                padding: 12,
                                beginAtZero: this.data.beginAtZero,
                                suggestedMin: this.data.minimumRange,
                                suggestedMax: this.data.maximumRange
                            }
                        }],
                        yAxes: [{
                            gridLines: {
                                borderDash: [8, 4],
                                zeroLineBorderDash: [8, 4],
                                color: yAxisColor,
                                drawTicks: false
                            },
                            ticks: {
                                lineHeight: 1.5,
                                fontFamily: '\'Poppins\', sans-serif',
                                fontSize: 11.262,
                                fontColor: '#9397A0',
                                padding: 14,
                                precision: this.data.withoutDecimalPoint ? 0 : 2,
                                beginAtZero: this.data.beginAtZero,
                                suggestedMin: this.data.minimumRange,
                                suggestedMax: this.data.maximumRange
                            }
                        }]
                    },
                    legend: {
                        display: false,
                    }
                }
            )
        }
    }
}
