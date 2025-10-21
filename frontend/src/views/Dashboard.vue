<!-- 
	This is the dashboard page, it uses the dashboard layout in: 
	"./layouts/Dashboard.vue" .
 -->

<template>
	<div>
		<!-- Counter Widgets -->
		<a-row :gutter="24">
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="Year From">
						<a-select
							v-model="selectedYears.from"
							v-decorator="[
								'schoolYeaer',
								{ rules: [{ required: true, message: 'Please select year' }] },
							]"
							placeholder="Select a Year"
							:options="yearMultipleOpts"
							@change="getListSelection(selectedYears)"
						/>
					</a-form-item>
				</a-form>
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="Year To">
						<a-select
							v-model="selectedYears.to"
							v-decorator="[
								'schoolYeaer',
								{ rules: [{ required: true, message: 'Please select year' }] },
							]"
							placeholder="Select a Year"
							:options="yearMultipleOpts"
							@change="getListSelection(selectedYears)"
						/>
					</a-form-item>
				</a-form>
			</a-col>
			<a-col :span="24" :lg="24"></a-col>
			<a-col :span="24" :lg="12" :xl="6" class="mb-24" v-for="(stat, index) in dashboardCards" :key="index">
				<a-card>
					<a-card-meta :title="stat.value" :description="stat.label">
						<a-icon 
							slot="avatar"
							key="man"
							:style="{color: stat.color, fontSize: '32px'}"
							:type="stat.icon" 
						/>
					</a-card-meta>
					<!-- <span class="font-bold">{{ stat.label }}</span><br/>
					<h5>{{  }}</h5>  -->
					<template v-if="stat.genders !== undefined" slot="actions" >
						<a-icon key="man" type="man" /> {{ stat.genders.male }}
						<a-icon key="woman" type="woman" /> {{ stat.genders.female }}
					</template>
					<template v-else slot="actions" >
						<a @click="goTo('/resources')">View Details</a>
					</template>
				</a-card>
				
			</a-col>
		</a-row>
		<!-- / Counter Widgets -->

		<!-- Charts -->
		<a-row :gutter="24" type="flex" align="stretch">
			<a-col :span="24" :lg="12" class="mb-24">
				<!-- Active Users Card -->
				<CardLineChart
					title="No. of Enrolled Students"
					description="5 years graph representation of the GAD"
					:chartData.sync="seriesDataEnroll"
					:groupData.sync="groupDataEnroll"
				></CardLineChart>
				<!-- Active Users Card -->

			</a-col>
			<a-col :span="24" :lg="12" class="mb-24">
				
				<!-- Sales Overview Card -->
				<CardLineChart
					title="No. of Student Graduates"
					description="5 years graph representation of the GAD"
					:chartData.sync="seriesDataGrad"
          			:groupData.sync="groupDataGrad"
				></CardLineChart>
				<!-- / Sales Overview Card -->

			</a-col>
			<a-col :span="16" :lg="14" class="mb-24">
				<CardBarChart
					title="Enrollment Courses Summary"
					description="Course over all total of the year"
					:chartData.sync="seriesSummaryDataEnroll"
					:groupData.sync="groupSummaryDataEnroll"
					:showTotal="false"
				/>
			</a-col>
			<a-col :span="8" :lg="10" class="mb-24">
				<CardTotalCourseList
					:data="summaryList"
				></CardTotalCourseList>
			</a-col>

			<a-col :span="8" :lg="10" class="mb-24">
				<CardTotalCourseList
					:data="summaryListGraduate"
				></CardTotalCourseList>
			</a-col>
			<a-col :span="16" :lg="14" class="mb-24">
				<CardBarChart
					title="Graduate Courses Summary"
					description="Course over all total of the year"
					:chartData.sync="seriesSummaryDataGraduate"
					:groupData.sync="groupSummaryDataGraduate"
					:showTotal="false"
				/>
			</a-col>
			
			
		</a-row>
		<!-- / Charts -->

	</div>
</template>

<script>
	import moment from "moment";
	// Bar chart for "Active Users" card.
	import CardBarChart from '../components/Cards/Analytics/CardBarChart' ;
	// Line chart for "Sales Overview" card.
	import CardLineChart from '../components/Cards/CardLineChart' ;
	// Counter Widgets
	import WidgetCounter from '../components/Widgets/WidgetCounter' ;
	import CardTotalCourseList from '../components/Cards/CardTotalCourseList';
import { sum } from "pdf-lib";

	const currentYear = moment().format('YYYY')

	export default ({
		components: {
			CardBarChart,
			CardLineChart,
			CardTotalCourseList,
			WidgetCounter
		},
		computed:{
			yearMultipleOpts() {
				let res = [];
				let startDate = 1997;
				let currentYear = new Date().getFullYear();

				for (let year = startDate; year <= currentYear; year++) {
					res.push({
					label: year.toString(),
					value: year.toString()
					});
				}

				return res;
			}
		},
		data() {
			return {
				// Counter Widgets Stats
				chartEnrollmentData: [],
				chartGraduateData: [],
				chartOptions: [],
				chartGradOptions: [],
				selectedYears: {
					from: "2019",
					to: "2024"
				},
				dashboardCards: [{
					label: 'Students',
					value: 0,
					genders: {
						male: 0,
						female: 0,
					},
					caption: 'Total count of the enrolled students',
					color: 'blue',
					icon: 'usergroup-add'
				}, {
					label: 'Faculty & Staff',
					value: 0,
					genders: {
						male: 0,
						female: 0,
					},
					caption: 'Total count of the employee',
					color: 'orange',
					icon: 'idcard'
				}, {
					label: 'Student Graduates',
					value: 0,
					genders: {
						male: 0,
						female: 0,
					},
					caption: 'Total count of Graduates',
					color: 'green',
					icon: 'check-circle'
				}, {
					label: 'Resources',
					value: 0,
					
					caption: 'Total count of the document',
					color: 'red',
					icon: 'file-pdf'
				},],
				seriesDataEnroll: [],
				groupDataEnroll: [],
				seriesDataGrad: [],
				groupDataGrad: [],
				summaryList: [
					{
						title: "Course",
						male: "0",
						female: "0",
					}
				],
				seriesSummaryDataEnroll: [],
				groupSummaryDataEnroll: [],
				summaryListGraduate: [
					{
						title: "Course",
						male: "0",
						female: "0",
					}
				],
				seriesSummaryDataGraduate: [],
				groupSummaryDataGraduate: [],
			}
		},
		created(){
			this.getListSelection(this.selectedYears)
		},
		methods:{
			moment,
			goTo(route){
				this.$router.push(route)
			},
			async getListSelection(dataYear){
				let payload = {
					...dataYear
				}
				this.$api.post("analytics/get/dashboard", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						this.dashboardCards[0].value = response.enrollment || "0"
						this.dashboardCards[0].genders = response.genders.enrollment
						this.dashboardCards[1].value = response.employee || "0"
						this.dashboardCards[1].genders = response.genders.employee
						this.dashboardCards[2].value = response.graduates || "0"
						this.dashboardCards[2].genders = response.genders.graduate
						this.dashboardCards[3].value = response.resource || "0"
						
					} else {
						// show Error
						console.log('there is some error')
					}
				})


				this.getEnrollmentData()
				this.getGraduateData()
			},
			async getEnrollmentData(){
				let payload = {
					...this.selectedYears,
					reportType: 'enrollment',
					page: 'dashboard',
				}
				this.$api.post("analytics/get/graph/dashboard", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						let selected = [];
						let series = {
							male: [],
							female: [],
						}
						let groups = []
						if(typeof res.data.list === "object"){
							for (const el in res.data.list) {
								groups.push(el)
								selected.push(res.data.list[el])
							}
						} else {
							selected = res.data.list
						}
						// selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))
						selected.forEach((el) => {
							let totalMale = 0
							let totalFemale = 0

							if(typeof el === "object"){
								for (const i in el) {
									totalMale += el[i][0].y
									totalFemale += el[i][1].y
								}
							} else {
								el.forEach(sel => {
									totalMale += sel[0].y
									totalFemale += sel[1].y
								})
							}
							series.male.push(totalMale)
							series.female.push(totalFemale)
						});

						this.seriesDataEnroll = series
                		this.groupDataEnroll = groups
						

						// Get summary
						let summary = [];
						let summaryGroup = []
						let summarySeries = {
							male: [],
							female: [],
						}
						
						for(const i in res.data.overall){
							let summaryObj = {
								title: '',
								male: 0,
								female: 0,
							};
							summaryObj.title = i
							summaryGroup.push(i)

							for(const e in res.data.overall[i]){
								summaryObj.male += res.data.overall[i][e].male
								summaryObj.female += res.data.overall[i][e].female
							}
							
							summarySeries.male.push(summaryObj.male)
							summarySeries.female.push(summaryObj.female)
							summary.push({...summaryObj})
						} 
						
						
						this.seriesSummaryDataEnroll = summarySeries
						this.groupSummaryDataEnroll = summaryGroup
						this.summaryList = summary
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			async getGraduateData(){
				let payload = {
					...this.selectedYears,
					reportType: 'graduate',
					page: 'dashboard',
				}
				this.$api.post("analytics/get/graph/dashboard", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						let selected = [];
						let series = {
							male: [],
							female: [],
						}
						let groups = []

						if(typeof res.data.list === "object"){
							for (const el in res.data.list) {
								groups.push(el)
								selected.push(res.data.list[el])
							}
						} else {
							selected = res.data.list
						}
						
						// selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))
						selected.forEach((el) => {
							let totalMale = 0
							let totalFemale = 0
							if(typeof el === "object"){
								for (const i in el) {
									totalMale += el[i].male
									totalFemale += el[i].female
								}
							}

							series.male.push(totalMale)
							series.female.push(totalFemale)
						});

						this.seriesDataGrad = series
                		this.groupDataGrad = groups

						// Get summary
						let summary = [];
						let summaryGroup = []
						let summarySeries = {
							male: [],
							female: [],
						}
						
						for(const i in res.data.overall){
							let summaryObj = {
								title: '',
								male: 0,
								female: 0,
							};
							summaryObj.title = i
							summaryGroup.push(i)

							for(const e in res.data.overall[i]){
								summaryObj.male += res.data.overall[i][e].male
								summaryObj.female += res.data.overall[i][e].female
							}
							
							summarySeries.male.push(summaryObj.male)
							summarySeries.female.push(summaryObj.female)
							summary.push({...summaryObj})
						} 
						
						
						this.seriesSummaryDataGraduate = summarySeries
						this.groupSummaryDataGraduate = summaryGroup
						this.summaryListGraduate = summary
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			}
		}
		// End
	})

</script>

<style lang="scss">
</style>