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
							<a-button type="primary"  @click="addSingleModal = true"> 
								<a-icon type="plus" />Add Single Entry
							</a-button>
							<a-button type="default"  @click="addUSerModal = true"> 
								<a-icon type="upload" />Upload CSV Data
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
			title="Upload CSV Data"
			centered
			@ok="uploadCSVData(csvData)"
		>
			<a-alert
				message="Notes"
				description="If you still not yet downloaded the csv format to add data please do ensure download the template below and fill. Duplicate entries (same name, year, and course) will be automatically skipped."
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

		<!-- Add Single Entry Modal -->
		<a-modal
			v-model="addSingleModal"
			title="Add Single Graduate Entry"
			centered
			@ok="saveSingleEntry"
			:confirmLoading="singleSaving"
		>
			<a-form :label-col="{ span: 6 }" :wrapper-col="{ span: 16 }">
				<a-form-item label="Name" required>
					<a-input v-model="singleRecord.name" placeholder="Enter full name" />
				</a-form-item>
				<a-form-item label="Address">
					<a-input v-model="singleRecord.address" placeholder="Enter address" />
				</a-form-item>
				<a-form-item label="Batch">
					<a-input v-model="singleRecord.batch" placeholder="e.g., 2023-2024" />
				</a-form-item>
				<a-form-item label="Year Graduated">
					<a-input v-model="singleRecord.yearGraduated" placeholder="e.g., 2024" />
				</a-form-item>
				<a-form-item label="Section">
					<a-input v-model="singleRecord.section" placeholder="Enter section" />
				</a-form-item>
				<a-form-item label="Course">
					<a-input v-model="singleRecord.course" placeholder="Enter course" />
				</a-form-item>
				<a-form-item label="Major">
					<a-input v-model="singleRecord.major" placeholder="Enter major" />
				</a-form-item>
				<a-form-item label="Achievement">
					<a-input v-model="singleRecord.achievement" placeholder="Enter achievement/honors" />
				</a-form-item>
				<a-form-item label="Gender" required>
					<a-select v-model="singleRecord.gender" placeholder="Select Gender">
						<a-select-option value="Male">Male</a-select-option>
						<a-select-option value="Female">Female</a-select-option>
					</a-select>
				</a-form-item>
			</a-form>
		</a-modal>

		<!-- View Details Modal -->
		<a-modal v-model="viewModal" title="View Graduate Details" footer="" centered>
			<div v-if="viewRecord">
				<p><strong>Name:</strong> {{ viewRecord.name }}</p>
				<p><strong>Address:</strong> {{ viewRecord.address }}</p>
				<p><strong>Batch:</strong> {{ viewRecord.batch }}</p>
				<p><strong>Year Graduated:</strong> {{ viewRecord.yearGraduated }}</p>
				<p><strong>Section:</strong> {{ viewRecord.section }}</p>
				<p><strong>Course:</strong> {{ viewRecord.course }}</p>
				<p><strong>Major:</strong> {{ viewRecord.major }}</p>
				<p><strong>Achievement:</strong> {{ viewRecord.achievement }}</p>
				<p><strong>Gender:</strong> {{ viewRecord.gender }}</p>
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
				<a-form-item label="Section">
					<a-input v-model="editRecord.section" />
				</a-form-item>
				<a-form-item label="Course">
					<a-input v-model="editRecord.course" />
				</a-form-item>
				<a-form-item label="Major">
					<a-input v-model="editRecord.major" />
				</a-form-item>
				<a-form-item label="Achievement">
					<a-input v-model="editRecord.achievement" />
				</a-form-item>
				<a-form-item label="Gender">
					<a-select v-model="editRecord.gender" placeholder="Select Gender">
						<a-select-option value="Male">Male</a-select-option>
						<a-select-option value="Female">Female</a-select-option>
					</a-select>
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
				{ title: 'Section', dataIndex: 'section' },
				{ title: 'Course', dataIndex: 'course' },
				{ title: 'Major', dataIndex: 'major' },
				{ title: 'Achievement', dataIndex: 'achievement' },
				{ title: 'Gender', dataIndex: 'gender' },
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
			addSingleModal: false,
			singleRecord: {},
			singleSaving: false,
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
				achievement: rec.achievement || '',
				gender: rec.gender || '',
				createdBy: Number(this.user.userId)
			}))

			return false
		},
		async uploadCSVData(data){
			try{
				// Check for duplicates and filter out duplicates from CSV data
				const { cleanData, duplicatesFound } = this.filterDuplicatesFromCSV(data);
				
				if (duplicatesFound.length > 0) {
					this.$notification.warning({
						message: 'Duplicates Found',
						description: `${duplicatesFound.length} duplicate entries were skipped. ${cleanData.length} new records will be uploaded.`
					});
				}

				if (cleanData.length === 0) {
					this.$notification.warning({
						message: 'No New Data',
						description: 'All entries in the CSV already exist. No new records to upload.'
					});
					return;
				}

				const res = await this.$api.post('graduates/create/multiple', { csv: cleanData })
				const response = res.data
				if(!response.error){
					this.getList()
					this.addUSerModal = false
					this.$notification.success({
						message: 'Upload Successful',
						description: `${cleanData.length} new graduate records have been uploaded successfully.`
					});
				} else {
					console.error('Upload error', response)
					this.$notification.error({
						message: 'Upload Failed',
						description: response.message || 'Failed to upload CSV data'
					});
				}
			} catch(err){
				console.error(err)
				this.$notification.error({
					message: 'Upload Error',
					description: 'An error occurred while uploading the CSV data'
				});
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

		async saveSingleEntry(){
			if(!this.singleRecord.name){
				this.$notification.error({
					message: 'Validation Error',
					description: 'Name is required'
				});
				return;
			}

			if(!this.singleRecord.gender){
				this.$notification.error({
					message: 'Validation Error',
					description: 'Gender is required'
				});
				return;
			}

			// Check for duplicates locally first
			const duplicate = this.checkForDuplicateLocally(
				this.singleRecord.name,
				this.singleRecord.yearGraduated,
				this.singleRecord.course
			);

			if (duplicate) {
				this.$notification.warning({
					message: 'Duplicate Entry',
					description: 'A graduate with the same name, year, and course already exists in the current data'
				});
				return;
			}

			this.singleSaving = true;
			const payload = {
				name: this.singleRecord.name,
				address: this.singleRecord.address || '',
				batch: this.singleRecord.batch || '',
				yearGraduated: this.singleRecord.yearGraduated || '',
				advisoryId: this.singleRecord.advisoryId || null,
				section: this.singleRecord.section || '',
				course: this.singleRecord.course || '',
				major: this.singleRecord.major || '',
				achievement: this.singleRecord.achievement || '',
				gender: this.singleRecord.gender || '',
				createdBy: Number(this.user.userId)
			};

			try {
				const res = await this.$api.post('graduates/create', payload);
				const response = res.data;
				this.singleSaving = false;
				
				if(!response.error){
					this.addSingleModal = false;
					this.singleRecord = {};
					this.getList();
					this.$notification.success({
						message: 'Success',
						description: 'Graduate record added successfully!'
					});
				} else {
					console.error('Creation failed', response);
					this.$notification.error({
						message: 'Error',
						description: response.message || 'Failed to create record'
					});
				}
			} catch(err) {
				this.singleSaving = false;
				console.error(err);
				
				// Handle duplicate entries (409 status)
				if (err.response && err.response.status === 409) {
					this.$notification.warning({
						message: 'Duplicate Entry',
						description: err.response.data.message || 'A graduate with the same name, year, and course already exists'
					});
				} else {
					this.$notification.error({
						message: 'Error',
						description: 'An error occurred while creating the record'
					});
				}
			}
		},

		checkForDuplicateLocally(name, yearGraduated, course) {
			// Check against current users data for local duplicate detection
			return this.users.some(user => 
				user.name && user.name.toLowerCase().trim() === (name || '').toLowerCase().trim() &&
				user.yearGraduated === yearGraduated &&
				user.course && user.course.toLowerCase().trim() === (course || '').toLowerCase().trim()
			);
		},

		filterDuplicatesFromCSV(csvData) {
			const cleanData = [];
			const duplicatesFound = [];
			const seenEntries = new Set();

			csvData.forEach((entry, index) => {
				const key = `${(entry.name || '').toLowerCase().trim()}_${entry.yearGraduated}_${(entry.course || '').toLowerCase().trim()}`;
				
				// Check against existing data and within CSV itself
				const isDuplicateInExisting = this.checkForDuplicateLocally(entry.name, entry.yearGraduated, entry.course);
				const isDuplicateInCSV = seenEntries.has(key);

				if (isDuplicateInExisting || isDuplicateInCSV) {
					duplicatesFound.push({
						row: index + 1,
						name: entry.name,
						reason: isDuplicateInExisting ? 'Already exists in database' : 'Duplicate within CSV'
					});
				} else {
					seenEntries.add(key);
					cleanData.push(entry);
				}
			});

			return { cleanData, duplicatesFound };
		},
		
	}
})

</script>

<style lang="scss">
</style>