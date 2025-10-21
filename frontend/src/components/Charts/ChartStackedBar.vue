<template>
	<div>
		<canvas ref="chart" :style="{'height': height + 'px'}"></canvas>
	</div>
</template>

<script>
	import { Chart, registerables } from 'chart.js';
	Chart.register(...registerables);

	export default ({
		props: [
			'data',
			'height',
		],
		data(){
			return {
				chart: null,
			} ;
		},
		watch:{
			data(newVal){
				this.chart.data = newVal
				this.chart.update()
			}
		},
		mounted () { 
    		let ctx = this.$refs.chart.getContext("2d");
			this.chart = new Chart(ctx, {
				type: 'bar',
                data: this.data,
                options: {
                    plugins: {
                        legend: {
							display: false,
						},
                    },
                    tooltips: {
						enabled: true,
						mode: "index",
						intersect: false,
					},
                    responsive: true,
                    scales: {
                        x: {
                            stacked: true,
                        },
                        y: {
                            stacked: true
                        }
                    }
                }
			}) ;
		},
		// Right before the component is destroyed,
		// also destroy the chart.
		beforeDestroy: function () {
			this.chart.destroy() ;
		},
	})

</script>

<style lang="scss" scoped>
	// canvas {
	// 	color: black;
	// 	background-image: linear-gradient(to right, #103783, #2e5291, #090033 ) ;
	// }
</style>