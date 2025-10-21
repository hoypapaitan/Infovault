<!-- 
	This is the user profile page, it uses the dashboard layout in: 
	"./layouts/Dashboard.vue" .
 -->

 <template>
	<div>

		<!-- Header Background Image -->
		<div class="profile-nav-bg" style="background-image: url('images/bg-profile.jpg')"></div>
		<!-- / Header Background Image -->

		<!-- User Profile Card -->
		<a-card :bordered="false" class="card-profile-head" :bodyStyle="{padding: 0,}">
			<template #title>
				<a-row type="flex" align="middle">
					<a-col :span="24" :md="12" class="col-info">
						<a-avatar 
							shape="square" 
							:size="74" 
							icon="calendar" 
							:style="{ backgroundColor: 'orange' }"
						/>
						<div class="avatar-info">
							<h4 class="font-semibold m-0">EVENTS</h4>
							<p>List of Event and Evaluation</p>
						</div>
					</a-col>
					<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
						<a-button @click="addUSerModal = true" type="primary">Add Event</a-button>
					</a-col>
				</a-row>
			</template>
		</a-card>
		<!-- User Profile Card -->

		<a-row type="flex" :gutter="24">

			<!-- Calendar Settings Column -->
			<a-col :span="24" :md="24" class="mb-24">
				<a-card :bordered="false" class="header-solid h-full" :bodyStyle="{paddingTop: 0, paddingBottom: 0 }">
					<!-- <template #title>
						<h6 class="font-semibold m-0">Event Calendar</h6>
					</template> -->
					<a-calendar @select="getEventDetails" @panelChange="onPanelChange">
						<ul slot="dateCellRender" slot-scope="value" class="events">
							<li v-for="item in getListData(value)" :key="item.id">
								{{ item.title }}
							</li>
						</ul>
					</a-calendar>
			
				</a-card>
				
			</a-col>
			

		</a-row>

		<a-modal
			v-model="addUSerModal"
			title="Event Details"
			centered
		>
			<template slot="footer">
				<a-button key="submit" type="primary" :loading="loading" @click="addToEvent">
					Submit
				</a-button>
			</template>

			<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
				<a-row :gutter="24">
					<a-col :span="24" :sm="12">
						<a-form-item label="Title">
							<a-input
								v-model="form.title"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="12">
						<a-form-item label="Code">
							<a-input
								v-model="form.eventCode"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="24">
						<a-form-item label="Decription">
							<a-input
								type="textarea"
								v-model="form.description"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="24">
						<a-form-item label="Event Date">
							<a-range-picker @change="onChange" />
						</a-form-item>
					</a-col>
				</a-row>
			</a-form>
		</a-modal>

		<a-modal
			v-if="eventDetails !== null"
			v-model="eventDetailsModal"
			:title="eventDetails.title"
			centered
			@cancel="handleCancel"
        	@ok="handleCancel"
		>	
			<template slot="footer">
				<a-button
					v-if="editEvent"
					type="danger"
					style="margin-top: 16px"
					@click="editEvent = false"
				>
					Cancel Edit
				</a-button>
				<a-button
					v-if="editEvent"
					type="success"
					style="margin-top: 16px"
					@click="updateDetails"
				>
					Save
				</a-button>
				<a-button
					v-if="!editEvent"
					type="danger"
					style="margin-top: 16px"
					@click="deleteEventSchedule"
				>
					Delete Schedule
				</a-button>
				<a-button
					v-if="!editEvent"
					type="primary"
					style="margin-top: 16px"
					@click="editEventDetails"
				>
					Edit
				</a-button>
				<a-button
					type="primary"
					:disabled="csvData.length === 0"
					:loading="uploading"
					style="margin-top: 16px"
					@click="uploadCSVData(csvData)"
				>
						{{ uploading ? 'Uploading' : 'Start Upload' }}
				</a-button>
				
			</template>
			<a-row v-if="!editEvent" :gutter="24">
				<a-col :span="24" :sm="24">
					<a-form-item label="Description">
						<span>{{ eventDetails.description }}</span>
					</a-form-item>
				</a-col>
				<a-col v-if="eventEvaluated" :span="24" :sm="24">
					<a-alert
                        message="Informational Notice"
                        description="You can now view the responses of the instructors on the event you selected"
                        type="info"
                        show-icon
                    />
					<a href="#" @click="generateQuestions">Click Here to View the Responses</a>
				</a-col>
				<!-- eventEvaluated: false,
				eventUploaded: false, -->
				<a-col v-if="!eventUploaded" :span="24" :sm="24">
					<a-form-item label="Upload Questionaires">
						<a-upload :file-list="fileList" :remove="handleRemove" :before-upload="beforeUpload">
							<a-button> <a-icon type="upload" /> Select File </a-button>
						</a-upload>
					</a-form-item>
				</a-col>
				<a-col v-if="!eventUploaded" :span="24" :sm="24">
					<a href="/docs/evaluation-format.csv" download="evaluation-format.csv" target="_blank">Click Here to Download Template</a>
				</a-col>
			</a-row>
			<a-form v-if="editEvent" :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
				<a-row :gutter="24">
					<a-col :span="24" :sm="24">
						<a-form-item label="Title">
							<a-input
								v-model="form.title"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="24">
						<a-form-item label="Decription">
							<a-input
								type="textarea"
								v-model="form.description"
							/>
						</a-form-item>
					</a-col>
				</a-row>
			</a-form>
		</a-modal>

		<a-drawer
			v-if="eventDetails !== null"
			:title="eventDetails.title"
			placement="left"
			:width="1220"
			:closable="false"
			:visible="visible"
			@close="onClose"
		>
			<a-button
				type="primary"
				style="margin-top: 16px"
				@click="openPrint = true"
			>
				Print Result
			</a-button>
			<a-table :columns="columns" :data-source="questionaireList" :pagination="false">
				<template slot="title">
					<h4>GAD Response Result</h4>
				</template>
				<template slot="footer">
					<a-row type="flex">
						<a-col flex="1 1 200px">
							TOTAL GAD SCORE— PROJECT IDENTIFICATION AND DESIGN
							STAGES (Add the score for each of the 10 elements, or the figures in
							thickly bordered cells.)
						</a-col>
						<a-col class="text-right" flex="0 1 300px">
							<strong>{{ getGADTotal }}</strong>
						</a-col>
					</a-row>
					
				</template>
				<template slot="question" slot-scope="question">
					<p v-if="question.title !== '' && question.question === ''"><strong>
						{{ `${question.order} ${question.title}` }}
					</strong></p>
					<p v-if="question.title !== '' && question.question !== ''">
						<strong>{{ `${question.order} ${question.title}` }}</strong> <br />
						{{ `${question.question}` }}
					</p>
					<p v-else>{{ `${question.order} ${question.question} (${question.scoring})` }}</p>
				</template>
				<template slot="response" slot-scope="response">
					<a-radio-group v-if="response.isCounted === 'yes'" disabled v-model="response.scoreCol">
						<a-radio-button :value="response.noScore">
						No
						</a-radio-button>
						<a-radio-button :value="response.partlyScore">
						Partly Yes
						</a-radio-button>
						<a-radio-button :value="response.yesScore">
						Yes
						</a-radio-button>
					</a-radio-group>
				</template>
				<template slot="score" slot-scope="score">
					{{ score.scoreCol }}
				</template>
				<template slot="result" slot-scope="result">
					{{ result.remarks }}
				</template>

			</a-table>
		</a-drawer>


		<ModalPrintResponse
			:openPrint="openPrint"
			:dataVal="questionaireList"
			@closePrint="openPrint = false"
		></ModalPrintResponse>
	</div>
</template>

<script>
	import { jwtDecode } from 'jwt-decode';
	import * as d3 from "d3"
	import moment from 'moment'
	import ModalPrintResponse from '../components/Modals/ModalPrintResponse.vue';
	

	export default ({
		components: {
			ModalPrintResponse
		},
		data() {
			return {
				openPrint: false,
				questionaireList: [],
				editEvent: false,
				visible: false,
				addUSerModal: false,
				eventDetailsModal: false,
				eventDetails: null,
				eventEvaluated: false,
				eventUploaded: false,
				form:{
					title: "",
					description: "",
					month: "",
					eventDate: "",
					eventCode: ""
				},
				rangeDate: [],
				csvData: [],
				uploading: false,
				eventList: [],
				currMonth: moment().format("M"),
				currYear: moment().format("YYYY"),
				eventTypes: [
					{
						label: "Enrollment",
						value: "enrollment",
					},
					{
						label: "Graduates",
						value: "graduate",
					},
					{
						label: "Employment",
						value: "employee",
					},
				],
			}
		},
		computed:{
			user: function(){
				let token = localStorage.getItem('userToken')
				return jwtDecode(token);
			},
			getGADTotal(){
				let gadTot = this.questionaireList.reduce((a, b) => Number(a) + Number(b.scoreCol), 0)
				return gadTot
			},
			columns(){
				return [
					{
						title: 'Dimension and question',
						scopedSlots: { 
							customRender: 'question' 
						},
					},
					{
						title: 'Response',
						scopedSlots: { customRender: 'response' },
						width: 250,
					},
					{
						title: 'Score for the item/element',
						scopedSlots: { 
							customRender: 'score' 
						},
					},
					{
						title: 'Result or comment',
						scopedSlots: { 
							customRender: 'result' 
						},
						width: 250,
					},
					
				];
			}
		},
		created(){
			this.getList();
		},
		methods:{
			moment,
			handleCancel(){
				this.eventDetailsModal = false
			},
			editEventDetails(){
				this.editEvent = true
				this.form = {
					title: this.eventDetails.title,
					description: this.eventDetails.description,
				}
			},
			updateDetails(){
				this.$api.post("events/edit", {
					dataId: this.eventDetails.id,
					form: {
						title: this.form.title,
						description: this.form.description,
					}
				}).then((res) => {
					let response = {...res.data}
					if(!response.error){
						console.log('data deleted')
						this.getList()
						this.eventDetailsModal = false
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			deleteEventSchedule(){
				this.$api.post("events/delete", {
					dataId: this.eventDetails.id
				}).then((res) => {
					let response = {...res.data}
					if(!response.error){
						console.log('data deleted')
						this.getList()
						this.eventDetailsModal = false
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			onChange(date, dateString) {
				this.rangeDate = dateString
			},
			showDrawer() {
				this.visible = true;
			},
			onClose() {
				this.visible = false;
			},
			async generateQuestions(){
				let payload = {
					eventId: this.eventDetails.eventCode
				}
				this.$api.post("evaluation/get/questions/response", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
                        let row = res.data.map((el, index) => {
							return {
								...el,
								order: Number(el.order)
							}
						})
						this.questionaireList = row.sort((a, b) => +(a.order > b.order) || -(a.order < b.order))
						this.eventDetailsModal = false

						this.showDrawer();
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			async addToEvent(){
				var start = moment(this.rangeDate[0], "YYYY-MM-DD");
				var end = moment(this.rangeDate[1], "YYYY-MM-DD");
				let daysDiff = moment.duration(end.diff(start)).asDays();


				for (let index = 0; index <= daysDiff; index++) {
					let evntMonth = moment(this.rangeDate[0]).add(index, 'd').format('M')
					let evntDay = moment(this.rangeDate[0]).add(index, 'd').format('DD')
					let evntYear = moment(this.rangeDate[0]).add(index, 'd').format('YYYY')
					let evntDate = moment(this.rangeDate[0]).add(index, 'd').format('YYYY-MM-DD')
					
					let payload = {
						...this.form,
						month: evntMonth,
						days: evntDay,
						year: evntYear,
						eventDate: evntDate
					}
					this.$api.post("events/add", payload).then((res) => {
						let response = {...res.data}
						if(!response.error){
							console.log('data uploaded')
						} else {
							// show Error
							console.log('there is some error')
						}
					})
				}

				this.getList()
				this.addUSerModal = false
				this.form = {
					title: "",
					description: "",
					month: "",
					eventDate: "",
					eventCode: ""
				}

			},
			async getList(month = moment().format("M"), year = moment().format("YYYY")){
				let payload = {
					month,
					year
				}
				this.$api.post("events/list", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						this.eventList = res.data
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			getListData(value) {
				let listData;
				let day = value.date();
				if(this.eventList.length > 0){
					listData = this.eventList.filter(el =>
						Number(el.days) === day && 
						Number(el.month) === Number(this.currMonth) &&
						Number(el.year) === Number(this.currYear)
					)
				} else {
					listData = []
				}
				
				return listData || [];
			},
			onPanelChange(value, mode){
				this.currMonth = value.format('M')
				this.currYear = value.format('YYYY')
				this.getList(value.format('M'), value.format('YYYY'))
			},
			getEventDetails(value){
				let day = value.date();
				let data = this.eventList.filter(el =>
				 	Number(el.days) === day && 
					Number(el.month) === Number(this.currMonth) &&
					Number(el.year) === Number(this.currYear)
				)

				this.eventDetails = data.length > 0 ? data[0] : null
				this.eventDetailsModal = true
				this.checkEvelauatedResponse();
				this.checkEventQuestionaires();
			},
			async checkEvelauatedResponse(){
				let payload = {
					eventId: this.eventDetails.eventCode
				}
				this.$api.post("evaluation/response/get", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
                        
						if(res.data.length > 0){
							// hide upload
							this.eventEvaluated = true;
						} else {
							// enable upoload
							this.eventEvaluated = false;
						}
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			async checkEventQuestionaires(){
				let payload = {
					eventId: this.eventDetails.eventCode
				}
				this.$api.post("evaluation/get/questions", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
                        if(res.data.length > 0){
							// hide upload
							this.eventUploaded = true;
						} else {
							// enable upoload
							this.eventUploaded = false;
						}
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			handleRemove(file) {
				this.csvData = [];
			},
			async beforeUpload(file) {
				var reader = new FileReader();
				reader.readAsText(new Blob(
					[file],
					{"type": file.type}
				))
				const fileContent = await new Promise(resolve => {
					reader.onloadend = (event) => {
						resolve(event.target.result)
					}
				})
				let csvData = d3.csvParse(fileContent)
				

				this.csvData = csvData
				return false;
			},
			handleChange(info) {
				console.log(info.file.status)
				if (info.file.status !== 'uploading') {
					console.log(info.file, info.fileList);
				}
				if (info.file.status === 'done') {
					this.$message.success(`${info.file.name} file uploaded successfully`);
				} else if (info.file.status === 'error') {
					this.$message.error(`${info.file.name} file upload failed.`);
				}
			},
			async uploadCSVData(data){
				this.uploading = true
				data = data.map((el) => {
					return {
						...el,
						eventId: this.eventDetails.eventCode,
						createdBy: Number(this.user.userId)
					}
				})

				const dataUploaded = await new Promise((resolve, reject) => {
					let uploaded = 0
					data.forEach(el => {
						this.$api.post("evaluation/create/content", el).then((res) => {
							let response = {...res.data}
							if(!response.error){
							console.log('data uploaded')
							} else {
							// show Error
							console.log('there is some error')
							}
						})
						uploaded += 1
					});

					if(uploaded === data.length){
						this.$message.success(`File uploaded successfully`);
						resolve({
							message: 'Upload complete'
						})
					} else {
					reject()
					}
				})
			
				this.getList();
				this.csvData = []
				this.eventDetails = null
				this.eventDetailsModal = false
				this.uploading = false
			
			},
			
		}
	})

</script>

<style lang="scss">
</style>