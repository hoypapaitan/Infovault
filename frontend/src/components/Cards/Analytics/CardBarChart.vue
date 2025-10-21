<template>

	<!-- Active Users Card -->
	<a-card :bordered="false" class="dashboard-bar-chart">
		<chart-bar 
			v-if="barChartData.labels.length > 0" 
			:height="220" 
			:data.sync="barChartData">
		</chart-bar>
		<div class="card-title">
			<h6>{{ title }}</h6>
			<!-- <p>YYYY to YYYY</p> -->
		</div>
		<div class="card-content">
			<p>{{ description }}</p>
		</div>
		<a-row v-if="showTotal" class="card-footer" type="flex" justify="center" align="top">
			<a-col :span="8">
				<h4>{{ totalMaleCount }}</h4>
				<span>Male</span>
			</a-col>
			<a-col :span="8">
				<h4>{{ totalFemaleCount }}</h4>
				<span>Female</span>
			</a-col>
			<a-col :span="8">
				<h4>{{ ovelAllTotal }}</h4>
				<span>Overall Total</span>
			</a-col>
		</a-row>
	</a-card>
	<!-- Active Users Card -->

</template>

<script>

	// Bar chart for "Active Users" card.
	import ChartBar from '../../Charts/ChartEnroll' ;

	export default ({
		props:{
			title: {
				type: String,
				default: 'Chart Title'
			},
			description: {
				type: String,
				default: "Graph representation"
			},
            chartData: {
                type: Object,
                default: {
					male: [],
					female: [],
				}
            },
			showTotal:{
				type: Boolean,
				default: true
			},
            groupData: {
                type: Array,
                default: []
            },
        },
		components: {
			ChartBar,
		},
		watch:{
			chartData(newVal){

				this.barChartData = {
					labels: this.groupData,
					datasets: [
						{
							label: "Male",
							backgroundColor: '#218bfc',
							borderWidth: 0,
							borderSkipped: false,
							borderRadius: 6,
							data: newVal.male, //[0,1,2,3,4,5]
							maxBarThickness: 40,
						},
						{
							label: "Female",
							backgroundColor: '#ff3371',
							borderWidth: 0,
							borderSkipped: false,
							borderRadius: 6,
							data: newVal.female,
							maxBarThickness: 40,
						},
					],
				}
				// this.barChartData.datasets[0].data = newVal.male
				// this.barChartData.datasets[1].data = newVal.female
			}
		},
		computed:{
			totalMaleCount(){
				let maleCount = this.chartData.male.reduce((a, b) => Number(a) + Number(b), 0)
				return maleCount
			},
			totalFemaleCount(){
				let femaleCount = this.chartData.female.reduce((a, b) => Number(a) + Number(b), 0)
				return femaleCount
			},
			ovelAllTotal(){
				return this.totalMaleCount + this.totalFemaleCount
			},
		},
		data() {

			return {
				// Data for bar chart.
				barChartData: {
					labels: [],
					datasets: [
						{
							label: "Male",
							backgroundColor: '#218bfc',
							borderWidth: 0,
							borderSkipped: false,
							borderRadius: 6,
							data: [], //[0,1,2,3,4,5]
							maxBarThickness: 40,
						},
						{
							label: "Female",
							backgroundColor: '#ff3371',
							borderWidth: 0,
							borderSkipped: false,
							borderRadius: 6,
							data: [],
							maxBarThickness: 40,
						},
					],
				},
			}
		},
	})

</script>