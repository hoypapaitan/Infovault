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
					@delete="handleDelete"
					@view="handleView"
					@edit="handleEdit"
					@upload="handleUpload"
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
			@ok="uploadCSVData(csvData)"
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

		<!-- View Details Modal -->
		<a-modal v-model="viewModal" title="View Graduate Details" footer="" centered>
			<div v-if="viewRecord">
				<p><strong>Name:</strong> {{ viewRecord.name }}</p>
				<p><strong>Address:</strong> {{ viewRecord.address }}</p>
				<p><strong>Batch:</strong> {{ viewRecord.batch }}</p>
				<p><strong>Year Graduated:</strong> {{ viewRecord.yearGraduated }}</p>
				<p><strong>Advisory ID:</strong> {{ viewRecord.advisoryId }}</p>
				<p><strong>Section:</strong> {{ viewRecord.section }}</p>
				<p><strong>Course:</strong> {{ viewRecord.course }}</p>
				<p><strong>Major:</strong> {{ viewRecord.major }}</p>
				<p><strong>Created At:</strong> {{ viewRecord.created_at }}</p>
			</div>
		</a-modal>

		<!-- Edit Details Modal -->
		<a-modal v-model="editModal" title="Edit Graduate Record" @ok="saveEdit" :confirmLoading="editSaving">
			<a-form :label-col="{ span: 6 }" :wrapper-col="{ span: 16 }">
				<a-form-item label="Name">
					<a-input v-model="editRecord.name" />
				</a-form-item>
				<a-form-item label="Address">
					<a-input v-model="editRecord.address" />
				</a-form-item>
				<a-form-item label="Batch">
					<a-input v-model="editRecord.batch" />
				</a-form-item>
				<a-form-item label="Year Graduated">
					<a-input v-model="editRecord.yearGraduated" />
				</a-form-item>
				<a-form-item label="Advisory ID">
					<a-input-number v-model="editRecord.advisoryId" />
				</a-form-item>
				<a-form-item label="Section">
					<a-input v-model="editRecord.section" />
				</a-form-item>
				<a-form-item label="Course">
					<a-input v-model="editRecord.course" />
				</a-form-item>
				<a-form-item label="Major">
					<a-input v-model="editRecord.major" />
				</a-form-item>
			</a-form>
		</a-modal>

		<ModalPrintReport
			:openPrint="openPrint"
			:dataVal="filteredUser"
			@closePrint="openPrint = false"
		></ModalPrintReport>
	</div>
</template>

<script>
import {jwtDecode} from 'jwt-decode';
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
				(this.selectedCourseFilter.length ? this.selectedCourseFilter.includes(el.course) : true) &&
				(this.selectedSchoolYearFilter.length ? this.selectedSchoolYearFilter.includes(el.yearGraduated) : true)
			)
			
			// return this.users
		},
		columns(){
			return [
				{ title: 'ID', dataIndex: 'id' },
				{ title: 'Name', dataIndex: 'name' },
				{ title: 'Address', dataIndex: 'address' },
				{ title: 'Batch', dataIndex: 'batch' },
				{ title: 'Year Graduated', dataIndex: 'yearGraduated' },
				{ title: 'Advisory ID', dataIndex: 'advisoryId' },
				{ title: 'Section', dataIndex: 'section' },
				{ title: 'Course', dataIndex: 'course' },
				{ title: 'Major', dataIndex: 'major' },
				{ title: 'Created At', dataIndex: 'created_at' },
				{ title: 'Action', scopedSlots: { customRender: 'action' } },
			];
		},
		user: function(){
			// Robust token read: support raw token string or JSON-wrapped object { value: '...' }
			let raw = localStorage.getItem('userToken')
			if(!raw) return null
			let tokenString = raw
			try{
				const parsed = JSON.parse(raw)
				if(parsed && parsed.value) tokenString = parsed.value
			} catch(e){
				// not JSON, assume raw token string
			}
			try{
				return jwtDecode(tokenString)
			} catch(e){
				console.error('Failed to decode JWT token', e)
				return null
			}
		},
	},
		data() {
		return {
			viewModal: false,
			viewRecord: {},
			editModal: false,
			editRecord: {},
			editSaving: false,
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
			var reader = new FileReader();
			let filePath = data
			reader.readAsText(new Blob([filePath], { type: filePath.type }))
			const fileContent = await new Promise(resolve => {
				reader.onloadend = (event) => {
					resolve(event.target.result)
				}
			})
			let csvData = d3.csvParse(fileContent)
			// console.log(csvData)
			// return
			// Normalize CSV columns to match tblgraduate schema
			this.csvData = csvData.map((rec) => ({
				name: rec.name || '',
				address: rec.address || '',
				batch: rec.batch || '',
				yearGraduated: rec.yearGraduated || rec.year_graduated || rec['Year Graduated'] || '',
				advisoryId: rec.advisoryId || rec.advisory_id || rec.advisory || null,
				section: rec.section || '',
				course: rec.course || '',
				major: rec.major || '',
				createdBy: Number(this.user.userId)
			}))

			return false
		},
		async uploadCSVData(data){
			try{
				const res = await this.$api.post('graduates/create/multiple', { csv: data })
				const response = res.data
				if(!response.error){
					this.getList()
					this.addUSerModal = false
				} else {
					console.error('Upload error', response)
				}
			} catch(err){
				console.error(err)
			}
		},
		async getList(){
			this.users = []
			this.usersOrig = []
			this.$api.get("graduates/get/list").then((res) => {
				let response = {...res.data}
				if(!response.error){
					let grads = response.list
					// sort by created_at or id
					grads.sort((a, b) => +(a.id < b.id) || -(a.id > b.id))
					this.users = grads
					this.usersOrig = grads

					let courses = []
					let sy = []
					grads.forEach(element => {
						courses.push({ label: element.course, value: element.course })
						sy.push({ label: element.yearGraduated, value: element.yearGraduated })
					});
					this.courseFilter = courses.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
					this.selectedCourseFilter = this.courseFilter.map(el => el.value)
					this.schoolYearFilter = sy.filter((e, i, self) => i === self.findIndex((t) => t.label === e.label));
					this.selectedSchoolYearFilter = this.schoolYearFilter.map(el => el.value)
				} else {
					console.log('there is some error')
				}
			})
		},

		async handleUpload(csvData){
			// csvData comes from child component as parsed array
			await this.uploadCSVData(csvData)
		},

		async handleDelete(rec){
			const payload = { dataId: rec.id }
			this.$api.post('graduates/delete', payload).then((res) => {
				const response = res.data
				if(!response.error){
					this.getList()
				} else {
					console.error('Delete failed', response)
				}
			}).catch(err => console.error(err))
		},

		handleView(rec){
			this.viewRecord = rec
			this.viewModal = true
		},

		handleEdit(rec){
			this.editRecord = Object.assign({}, rec)
			this.editModal = true
		},

		async saveEdit(){
			this.editSaving = true
			const payload = Object.assign({}, this.editRecord)
			this.$api.post('graduates/update', payload).then((res) => {
				const response = res.data
				this.editSaving = false
				if(!response.error){
					this.editModal = false
					this.getList()
				} else {
					console.error('Update failed', response)
				}
			}).catch(err => { this.editSaving = false; console.error(err) })
		},
		
	}
})

</script>

<style lang="scss">
</style>