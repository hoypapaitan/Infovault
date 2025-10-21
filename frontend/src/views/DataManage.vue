<template>
	<div>
		<!-- Header Background Image -->
		<div class="profile-nav-bg" style="background-image: url('images/coverbg.png')"></div>
		<!-- / Header Background Image -->

		<!-- User Profile Card -->
		<a-card :bordered="false" class="card-profile-head" :bodyStyle="{padding: 0,}">
			<template #title>
				<a-row type="flex" align="middle">
					<a-col :span="24" :md="12" class="col-info">
						<a-avatar 
							shape="square" 
							:size="74" 
							icon="idcard" 
							:style="{ backgroundColor: '#1e9ecf' }"
						/>
						<div class="avatar-info">
							<h4 class="font-semibold m-0">Graduate Records</h4>
							<p>Manage graduate records and details for the analytical representation</p>
						</div>
					</a-col>
					<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
						<a-space size="small">
							<a-button type="primary"  @click="addUSerModal = true"> 
								<a-icon type="dot-chart" />Add New Data
							</a-button>

							<a-button class="" type="secondary"> 
								<a-icon type="filter" />
							</a-button>
						</a-space>
						


					</a-col>
				</a-row>
			</template>
		</a-card>
		<!-- User Profile Card -->

		<!-- Authors Table -->
		<!-- <a-row :gutter="24">
			<a-col :span="24" :lg="24" class="mb-24">
				Search Filter
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="Course">
						<a-select
							mode="multiple"
							v-model="selectedCourseFilter"
							:style="{
								maxHeight: '42px',
								overflow: 'auto'
							}"
							:options="courseFilter"
						/>
					</a-form-item>
				</a-form>
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="Type of Report">
						<a-select
							mode="multiple"
							v-model="selectedReportTypeFilter"
							:options="reportTypeFilter"
						/>
					</a-form-item>
				</a-form>
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="Year">
						<a-select
							mode="multiple"
							v-model="selectedSchoolYearFilter"
							:options="schoolYearFilter"
						/>
					</a-form-item>
				</a-form>
			</a-col>
			<a-col :span="24" :lg="6" >
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
					<a-form-item label="Actions">
						<a-button-group>
							<a-button @click="exportCSV" type="primary"> <a-icon type="cloud-download" />Export CSV </a-button>
							<a-button @click="generatePrint" type="primary"> <a-icon type="printer" /> Print Report</a-button>
						</a-button-group>
					</a-form-item>
				</a-form>
				
			</a-col>
			
		</a-row> -->
		<a-row :gutter="24" type="flex">

			<!-- Authors Table Column -->
			<a-col :span="24" class="mb-24">
				<!-- Authors Table Card -->
				<CardAuthorTable
					:data="filteredUser"
					:columns="columns"
					@updateTable="getList"
				></CardAuthorTable>
				<!-- / Authors Table Card -->
			</a-col>
			<!-- / Authors Table Column -->
		</a-row>
		<!-- / Authors Table -->


		<a-modal
			v-model="addUSerModal"
			title="Add Data"
			centered
		>
			<a-alert
				message="Notes"
				description="If you still not yet downloaded the csv format to add data please do ensure download the template below and fill."
				type="info"
				show-icon
			/>
			
			<a-upload-dragger
				name="file"
				accept="csv"
				:before-upload="getFile"
			>
				<p class="ant-upload-drag-icon">
					<a-icon type="file-add" />
				</p>
				<p class="ant-upload-text">
					Click or drag file to this area to upload data
				</p>
				<p class="ant-upload-hint">
					This will automatically insert the data and close the form once the upload of data is complete
				</p>
			</a-upload-dragger>
			<a href="/docs/analytics_data-format.csv" download="analytics_data-format.csv" target="_blank">Click Here to Download Template</a>
		</a-modal>

		<ModalPrintReport
			:openPrint="openPrint"
			:dataVal="filteredUser"
			@closePrint="openPrint = false"
		></ModalPrintReport>
	</div>
</template>

<script>
import { jwtDecode } from 'jwt-decode';
import * as d3 from "d3"

// "Authors" table component.
import CardAuthorTable from '../components/Cards/CardDataManageTable' ;
import ModalPrintReport from '../components/Modals/ModalPrintReport' ;

export default ({
	components: {
		CardAuthorTable,
		ModalPrintReport
	},
	computed:{
		
		filteredUser(){
			return this.users.filter(el => 
				this.selectedCourseFilter.includes(el.course) && 
				this.selectedSchoolYearFilter.includes(el.yearFrom) && 
				this.selectedReportTypeFilter.includes(el.reportType)
			)
			
			// return this.users
		},
		columns(){
			return [
				{
					title: 'ID',
					dataIndex: 'id'
				},
				{
					title: 'Course',
					dataIndex: 'course',
				},
				{
					title: 'Year Recorded',
					dataIndex: 'yearFrom',
					key: 'yearFrom',
				},
				{
					title: 'Type of Report',
					dataIndex: 'reportType',
				},
				{
					title: 'Year',
					dataIndex: 'classYear',
				},
				{
					title: 'School Term',
					dataIndex: 'term',
				},
				{
					title: 'Male',
					dataIndex: 'male',
				},
				{
					title: 'Female',
					dataIndex: 'female',
				},
				{
					title: 'Action',
					scopedSlots: { 
						customRender: 'action' 
					},
				},
			];
		},
		user: function(){
			let token = localStorage.getItem('userToken')
			token = JSON.parse(token);
			return jwtDecode(token.value);
		},
	},
	data() {
		return {
			addUSerModal: false,
			// Associating "Authors" table columns with its corresponding property.
			users: [],
			usersOrig: [],
			sortedInfo: null,

			csvData: [],
			isNewUserModalOpen: false,
      		openPrint: false,
			

			courseFilter: [],
			selectedCourseFilter: [],
			schoolYearFilter: [],
			selectedSchoolYearFilter: [],
			reportTypeFilter: [
				{
					label: "Enrollment",
					value: "enrollment"
				},
				{
					label: "Graduate",
					value: "graduate"
				},
				{
					label: "Employee",
					value: "employee"
				},
			],
			selectedReportTypeFilter: [
				"enrollment",
				"graduate",
				"employee"
			],
		}
	},
	created(){
		this.getList();
	},
	methods:{
		generatePrint(){
			this.openPrint = true
		},
		async exportCSV(){
			const content = [this.columns.map(col => this.wrapCsvValue(col.dataIndex))].concat(
				this.filteredUser.map(row => this.columns.map(col => this.wrapCsvValue(
					typeof col.dataIndex === 'function'
					? col.field(row)
					: row[ col.field === void 0 ? col.dataIndex : col.dataIndex ],
					col.format,
					row
				)).join(','))
			).join('\n')

			const anchor = document.createElement('a');
			anchor.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(content);
			anchor.target = '_blank';
			anchor.download = 'DataReport.csv';
			anchor.click();
		},
		wrapCsvValue (val, formatFn, row) {
			let formatted = formatFn !== void 0
				? formatFn(val, row)
				: val

			formatted = formatted === void 0 || formatted === null
				? ''
				: String(formatted)

			formatted = formatted.split('"').join('""')
			/**
			 * Excel accepts \n and \r in strings, but some other CSV parsers do not
			 * Uncomment the next two lines to escape new lines
			 */
			// .split('\n').join('\\n')
			// .split('\r').join('\\r')

			return `"${formatted}"`
		},
		async getFile(data){
			// console.log(file)
			// return
			var reader = new FileReader();
			// let filePath = data.file.originFileObj
			let filePath = data
			reader.readAsText(new Blob(
				[filePath],
				{"type": filePath.type}
			))
			const fileContent = await new Promise(resolve => {
				reader.onloadend = (event) => {
				resolve(event.target.result)
				}
			})
			let csvData = d3.csvParse(fileContent)
			

			csvData = csvData.map((el) => {
				return {
				...el,
				createdBy: Number(this.user.userId)
				}
			})

			this.uploadCSVData(csvData)
			return false
		},
		async uploadCSVData(data){
			const dataUploaded = await new Promise((resolve, reject) => {
				let payload = {
					csv: data
				}
				this.$api.post("analytics/add/new", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						resolve({
							message: 'Upload complete'
						})
					} else {
						// show Error
						console.log('there is some error')
						reject()
					}
				})
			})
		
			this.$emit('updateTable')
			this.addUSerModal = false
		
		},
		async getList(){
			this.users = []
			this.usersOrig = []
			this.$api.get("analytics/get/list").then((res) => {
				let response = {...res.data}
				if(!response.error){
				response.list.sort((a, b) => +(a.yearFrom < b.yearFrom) || -(a.yearFrom > b.yearFrom))
				this.users = response.list
				this.usersOrig = response.list

				let courses = []
				let sy = []
				response.list.forEach(element => {
					courses.push({
						label: element.course,
						value: element.course
					})
					sy.push({
						label: element.yearFrom,
						value: element.yearFrom
					})
				});
				this.courseFilter = courses.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
				this.selectedCourseFilter = this.courseFilter.map(el => el.value)
				this.schoolYearFilter = sy.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
				this.selectedSchoolYearFilter = this.schoolYearFilter.map(el => el.value)
				} else {
				// show Error
				console.log('there is some error')
				}
			})
		},
		
	}
})

</script>

<style lang="scss">
</style>