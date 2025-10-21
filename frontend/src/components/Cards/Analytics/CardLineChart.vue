<template>

	<a-card :bordered="false" class="dashboard-bar-line header-solid">
		<chart-area 
			v-if="lineChartData.labels.length > 0" 
			:height="310" 
			:cdata="lineChartData"></chart-area>
		<div class="card-content">
			<p>{{ title }}</p>
		</div>
		<a-row class="card-footer" type="flex" justify="center" align="top">
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

</template>

<script>

	// Bar chart for "Active Users" card.
	import ChartArea from '../../Charts/ChartArea' ;

	export default ({
		props:{
			title: {
				type: String,
				default: "Graph"
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
            groupData: {
                type: Array,
                default: []
            },
        },
		components: {
			ChartArea,
		},
		watch:{
			chartData(newVal){
				this.lineChartData = {
					labels: this.groupData,
					datasets: [
						{
							label: "Male",
							// tension: 0.4,
							// borderWidth: 0,
							// pointRadius: 0,
							borderColor: "#1890FF",
							// borderWidth: 3,
							data: newVal.male,
							// maxBarThickness: 6

						},
						{
							label: "Female",
							// tension: 0.4,
							// borderWidth: 0,
							// pointRadius: 0,
							borderColor: "#B37FEB",
							// borderWidth: 3,
							data: newVal.female,
							// maxBarThickness: 6

						}
					],
				}
				// this.lineChartData.labels = this.groupData
				// this.lineChartData.datasets[0].data = newVal.male
				// this.lineChartData.datasets[1].data = newVal.female
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

				// Data for line chart.
				lineChartData: {
					labels: [],
					datasets: [
						{
							label: "Male",
							// tension: 0.4,
							// borderWidth: 0,
							// pointRadius: 0,
							borderColor: "#1890FF",
							// borderWidth: 3,
							data: [],
							// maxBarThickness: 6

						},
						{
							label: "Female",
							// tension: 0.4,
							// borderWidth: 0,
							// pointRadius: 0,
							borderColor: "#B37FEB",
							// borderWidth: 3,
							data: [],
							// maxBarThickness: 6

						}
					],
				},
			}
		},
	})

</script>