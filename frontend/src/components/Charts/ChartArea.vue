<template>
    <div style="position: relative; width: 100%;">
        <div :style="{ height: height + 'px' }">
            <canvas ref="chartCanvas"></canvas>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export default {
    name: 'BaseChart',
    props: {
        chartData: {
            type: Object,
            required: true,
            default: () => ({ labels: [], datasets: [] })
        },
        type: {
            type: String,
            default: 'line' 
        },
        height: {
            type: Number,
            default: 300
        },
        options: {
            type: Object,
            default: () => ({})
        }
    },
    
    data() {
        return {
            chartInstance: null,
        };
    },

    watch: {
        chartData: {
            handler(newVal) {
                if (this.chartInstance) {
                    // Sanitize data before updating to prevent "area" controller error
                    this.chartInstance.data = this.sanitizeData(newVal);
                    this.chartInstance.update();
                }
            },
            deep: true
        },
        type() {
            this.renderChart();
        }
    },

    mounted() {
        this.renderChart();
    },

    beforeDestroy() {
        if (this.chartInstance) {
            this.chartInstance.destroy();
            this.chartInstance = null;
        }
    },

    methods: {
        // Helper to remove invalid 'area' types from datasets
        sanitizeData(data) {
            // Deep clone to avoid mutating the prop directly
            const cleanData = JSON.parse(JSON.stringify(data));
            
            if (cleanData.datasets) {
                cleanData.datasets.forEach(dataset => {
                    // If a dataset explicitly asks for 'area' (mixed charts), convert to line + fill
                    if (dataset.type === 'area') {
                        dataset.type = 'line';
                        dataset.fill = true;
                    }
                    // If the main chart prop is 'area' and dataset type is missing, 
                    // we ensure the dataset gets filled to look like an area chart.
                    if (this.type === 'area' && !dataset.type) {
                        dataset.fill = true;
                    }
                });
            }
            return cleanData;
        },

        renderChart() {
            if (this.chartInstance) {
                this.chartInstance.destroy();
            }

            const ctx = this.$refs.chartCanvas.getContext("2d");
            
            let chartType = this.type;
            if (this.type === 'area') {
                chartType = 'line';
            }

            // Base options
            const baseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'bottom' },
                    tooltip: {
                        enabled: true,
                        mode: 'index',
                        intersect: false,
                    }
                },
                layout: {
                    padding: { top: 10, right: 10, left: 0, bottom: 0 }
                }
            };

            const scaleOptions = {
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: "rgba(0, 0, 0, 0.05)",
                            borderDash: [6],
                            drawBorder: false,
                        },
                        ticks: {
                            color: "#8C8C8C",
                            font: { size: 12, family: "Open Sans" },
                            padding: 10
                        },
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: "#8C8C8C",
                            font: { size: 12, family: "Open Sans" }
                        },
                    },
                }
            };

            let finalOptions = { ...baseOptions };
            
            // Apply scales to line, bar, and our custom 'area' type
            if (['line', 'bar', 'area'].includes(this.type)) {
                finalOptions = { ...finalOptions, ...scaleOptions };
            }

            finalOptions = { ...finalOptions, ...this.options };

            // Sanitize initial data
            const safeData = this.sanitizeData(this.chartData);

            this.chartInstance = new Chart(ctx, {
                type: chartType, // Uses 'line' even if prop is 'area'
                data: safeData,
                options: finalOptions
            });
        }
    }
};
</script>