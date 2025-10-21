<template>

	<a-card :bordered="false" class="dashboard-bar-line header-solid">
		<template #title>
			<h6>Gender Analytics Overview</h6>			
			<p>for the last 5 years</p>	
		</template>
		<template #extra>
			<a-badge color="primary" class="badge-dot-primary" text="Traffic" />
			<a-badge color="primary" class="badge-dot-secondary" text="Sales" />
		</template>
		
        <div id="chart">
            <!-- <apexchart
                ref="realtimeChart"
                type="bar" 
                height="380"  
                :options.sync="chartOptions"
                :series="series"
            >
            </apexchart> -->
        </div>
	</a-card>

</template>

<script>
	// Bar chart for "Active Users" card.
	import ChartLine from '../../Charts/ChartLine' ;

	export default ({
        props:{
            chartData: {
                type: Array,
                default: []
            },
            groupData: {
                type: Array,
                default: []
            },
            coursesOpt: {
                type: Array,
                default: []
            },
        },
		components: {
			ChartLine,
		},
        watch:{
            chartData(newData){
                if(newData){
                    this.$refs.realtimeChart.updateSeries([{
                        data: newData,
                    }], false, true);
                }
            },
            groupData(newData){
                if(newData){
                    this.$refs.realtimeChart.updateOptions({
                        xaxis: {
                            group: {
                                style: {
                                    fontSize: '10px',
                                    fontWeight: 700
                                },
                                groups: newData
                            }
                        },
                    });
                }
            },
            coursesOpt(newVal){
                if(newVal && newVal.length !== 0){
                    this.selectedValue = newVal[0].value
                    this.emitValue()
                } else {
                    this.$refs.realtimeChart.updateSeries([{
                        data: [
                            {
                                "x": "Male",
                                "fillColor": "#3b82f6",
                                "y": 0
                            },
                            {
                                "x": "Female",
                                "fillColor": "#f43f5e",
                                "y": 0
                            }
                        ],
                    }], false, true);
                    this.$refs.realtimeChart.updateOptions({
                        xaxis: {
                            group: {
                                style: {
                                    fontSize: '10px',
                                    fontWeight: 700
                                },
                                groups: [{
                                    "title": "Year - YYYY-YYYY",
                                    "cols": 2
                                }]
                            }
                        },
                    });
                }
            }
        },
		data(){
            return {
                selectedValue: null,
                series: [{
                    name: "enrollment",
                    data: []
                }],
                chartOptions: {
                    chart: {
                        type: 'bar',
                        height: 280
                    },
                    xaxis: {
                        type: 'category',
                        labels: {
                            formatter: function(val) {
                                return val
                            }
                        },
                        group: {
                            style: {
                                fontSize: '10px',
                                fontWeight: 700
                            },
                            groups: []
                        }
                    },
                },
            }
        },
        methods:{
            emitValue(){
                let selected = [];
                let filterData = this.coursesOpt.filter(el => {
                    return el.name === this.selectedValue
                })
                
                let series = []
                let groups = []

                if(typeof filterData[0].data === "object"){
                    for (const el in filterData[0].data) {
                        selected.push(filterData[0].data[el])
                    }
                } else {
                    selected = filterData[0].data
                }

                selected.forEach(el => {
                    groups.push(el.group)
                    series.push(...el.series)
                });

                // this.series.data = series
                // this.chartOptions.xaxis.group.groups = groups
                
                this.$refs.realtimeChart.updateSeries([{
                    data: series,
                }], false, true);
                this.$refs.realtimeChart.updateOptions({
                    xaxis: {
                        group: {
                            style: {
                                fontSize: '10px',
                                fontWeight: 700
                            },
                            groups: groups
                        }
                    },
                });
            }
        }
	})

</script>