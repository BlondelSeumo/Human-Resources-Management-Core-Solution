import Chart from 'chart.js'
import {generateChart} from './Base'

Chart.defaults.LineWithLine = Chart.defaults.line;
Chart.controllers.LineWithLine = Chart.controllers.line.extend({
    draw: function (ease) {
        Chart.controllers.line.prototype.draw.call(this, ease);

        if (this.chart.tooltip._active && this.chart.tooltip._active.length) {
            let activePoint = this.chart.tooltip._active[0];
            let ctx = this.chart.ctx;
            let x = activePoint.tooltipPosition().x;
            let topY = this.chart.scales['y-axis-0'].top;
            let bottomY = this.chart.scales['y-axis-0'].bottom;

            // draw line
            ctx.save();
            ctx.beginPath();
            ctx.moveTo(x, topY);
            ctx.lineTo(x, bottomY);
            ctx.lineWidth = 2;
            ctx.strokeStyle = '#07C';
            ctx.stroke();
            ctx.restore()
        }
    }
});

const LineWithLine = generateChart('line-with-chart', 'LineWithLine');
export default {
    extends: LineWithLine,
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
        this.renderCustomLineChart();
    },
    watch: {
        darkMode: {
            handler: 'renderCustomLineChart',
            immediate: false
        }
    },
    methods: {
        renderCustomLineChart() {
            let xAxisColor = '#F0F2F5',
                yAxisColor = '#F0F2F5';
            if (this.darkMode) {
                xAxisColor = '#2f3541'
                yAxisColor = '#2f3541'
            } else {
                xAxisColor = '#F0F2F5'
                yAxisColor = '#F0F2F5'
            }
            this.renderChart(
                {
                    labels: this.data.labels,
                    datasets: this.data.dataSets
                },
                {
                    responsive: true,
                    maintainAspectRatio: false,
                    tooltips: {
                        intersect: false
                    },
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
                                padding: 12
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
                                precision: this.data.withoutDecimalPoint ? 0 : 2
                            }
                        }],
                    },
                    legend: {
                        display: false,
                    }
                }
            );
        }
    }
}
