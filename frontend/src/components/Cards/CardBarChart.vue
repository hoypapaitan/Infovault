<template>

	<!-- Active Users Card -->
	<a-card :bordered="false" class="dashboard-bar-chart">
		<chart-bar :height="220" :data="barChartData"></chart-bar>
		<div class="card-title">
			<h6>Gender Count for the Year of:</h6>
			<p>YYYY to YYYY</p>
		</div>
		<div class="card-content">
			<p>Total accumulated gender equality for 5 years span</p>
		</div>
		<a-row class="card-footer" type="flex" justify="center" align="top">
			<a-col :span="12">
				<h4>3,6K</h4>
				<span>Male</span>
			</a-col>
			<a-col :span="12">
				<h4>2m</h4>
				<span>Female</span>
			</a-col>
		</a-row>
	</a-card>
	<!-- Active Users Card -->

</template>

<script>

	// Bar chart for "Active Users" card.
	import ChartBar from '../Charts/ChartBar' ;

	export default ({
		components: {
			ChartBar,
		},
		data() {
			return {
				// Data for bar chart.
				barChartData: {
					labels: ["2019", "2020", "2021", "2022", "2023", "2024"],
					datasets: [
						{
							label: "Male",
							backgroundColor: '#218bfc',
							borderWidth: 0,
							borderSkipped: false,
							borderRadius: 6,
							data: [850, 366, 500, 620, 900, 500],
							maxBarThickness: 20,
						},
						{
							label: "Female",
							backgroundColor: '#ff3371',
							borderWidth: 0,
							borderSkipped: false,
							borderRadius: 6,
							data: [340, 600, 500, 620, 900, 500],
							maxBarThickness: 20,
						},
					],
				},
			}
		},
	})

</script>