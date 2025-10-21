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
							<h4 class="font-semibold m-0">SELECTED EVENT: </h4>
							<p>List of Event and Evaluation</p>
						</div>
					</a-col>
					<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
						<!-- <a-button @click="addUSerModal = true" type="primary">Add Event</a-button> -->
                        <!-- <a-button type="link">
                                <svg width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M3 17C3 16.4477 3.44772 16 4 16H16C16.5523 16 17 16.4477 17 17C17 17.5523 16.5523 18 16 18H4C3.44772 18 3 17.5523 3 17ZM6.29289 9.29289C6.68342 8.90237 7.31658 8.90237 7.70711 9.29289L9 10.5858L9 3C9 2.44772 9.44771 2 10 2C10.5523 2 11 2.44771 11 3L11 10.5858L12.2929 9.29289C12.6834 8.90237 13.3166 8.90237 13.7071 9.29289C14.0976 9.68342 14.0976 10.3166 13.7071 10.7071L10.7071 13.7071C10.5196 13.8946 10.2652 14 10 14C9.73478 14 9.48043 13.8946 9.29289 13.7071L6.29289 10.7071C5.90237 10.3166 5.90237 9.68342 6.29289 9.29289Z" fill="#111827"/>
                                </svg>
                            Evaluation Questionaire Template
                        </a-button> -->
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
			v-if="eventDetails !== null"
			v-model="eventDetailsModal"
			:title="eventDetails.title"
			centered
			@cancel="handleCancel"
        	@ok="handleCancel"
		>	
			<template v-if="!eventEvaluated" slot="footer">
                <a-button  @click="downloadQuestions" type="link">
                    <svg width="15" height="15" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M3 17C3 16.4477 3.44772 16 4 16H16C16.5523 16 17 16.4477 17 17C17 17.5523 16.5523 18 16 18H4C3.44772 18 3 17.5523 3 17ZM6.29289 9.29289C6.68342 8.90237 7.31658 8.90237 7.70711 9.29289L9 10.5858L9 3C9 2.44772 9.44771 2 10 2C10.5523 2 11 2.44771 11 3L11 10.5858L12.2929 9.29289C12.6834 8.90237 13.3166 8.90237 13.7071 9.29289C14.0976 9.68342 14.0976 10.3166 13.7071 10.7071L10.7071 13.7071C10.5196 13.8946 10.2652 14 10 14C9.73478 14 9.48043 13.8946 9.29289 13.7071L6.29289 10.7071C5.90237 10.3166 5.90237 9.68342 6.29289 9.29289Z" fill="#111827"/>
                    </svg>
                    Download Questionaire
                </a-button>
				<a-button
					type="primary"
					:disabled="csvData.length === 0"
					:loading="uploading"
					style="margin-top: 16px"
					@click="uploadCSVData(csvData)"
				>
						{{ uploading ? 'Uploading' : 'Submit Answer' }}
				</a-button>
			</template>
			<a-row v-if="!eventEvaluated" :gutter="24">
				<a-col :span="24" :sm="24">
					<a-button
						type="primary"
						style="margin-bottom: 16px"
						@click="generateQuestions"
					>
						Evaluate Event
					</a-button>
				</a-col>
				<a-divider>Or</a-divider>
				<a-col :span="24" :sm="24">
					<a-alert
                        message="Informational Notes"
                        description="Download the questionaires and upload it once done to submit your response"
                        type="info"
                        show-icon
                    />
					<strong><i>Instruction:</i></strong> <br>
					<i>- Acceptable value for the responseCol field</i><br>
					<i><strong>Yes/yes</strong></i><br>
					<i><strong>No/no</strong></i><br>
					<i><strong>Partly/partly</strong> (for Partly Yes)</i><br>
				</a-col>
                <a-col :span="24" :sm="24">
					<a-form-item label="Upload Response">
						<a-upload :file-list="fileList" :remove="handleRemove" :before-upload="beforeUpload">
							<a-button> <a-icon type="upload" /> Select File </a-button>
						</a-upload>
						
					</a-form-item>
				</a-col>
			</a-row>
			<a-row v-if="eventEvaluated" :gutter="24">
				<a-col :span="24" :sm="24">
					<a-alert
                        message="Event Already Evaluated"
                        description="Thankyou for your response and to create an amazing result."
                        type="success"
                        show-icon
                    />
				</a-col>
			</a-row>
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
					:loading="uploading"
					style="margin-top: 16px"
					@click="submitResponse"
				>
						{{ uploading ? 'Uploading' : 'Submit Answer' }}
				</a-button>
			<a-table :columns="columns" :data-source="questionaireList" :pagination="false">

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
					<a-radio-group v-if="response.isCounted === 'yes'" v-model="response.scoreCol">
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
					<a-textarea
						v-if="result.isCounted === 'yes'"
						v-model="result.remarks"
						:auto-size="{ minRows: 2, maxRows: 6 }"
					/>
				</template>

			</a-table>
		</a-drawer>
	</div>
</template>

<script>
	import { jwtDecode } from 'jwt-decode';
	import * as d3 from "d3"
	import moment from 'moment'
	export default ({
		data() {
			return {
				addUSerModal: false,
				eventDetailsModal: false,
				eventDetails: null,
				eventEvaluated: false,
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

				questionaireList: [],
				visible: false,
			}
		},
		computed:{
			user: function(){
				let token = localStorage.getItem('userToken')
				return jwtDecode(token);
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
			showDrawer() {
				this.visible = true;
			},
			onClose() {
				this.visible = false;
			},
			onChange(date, dateString) {
				this.rangeDate = dateString
			},
			async addToEvent(){
				var start = moment(this.rangeDate[0], "YYYY-MM-DD");
				var end = moment(this.rangeDate[1], "YYYY-MM-DD");
				let daysDiff = moment.duration(end.diff(start)).asDays();


				for (let index = 0; index <= daysDiff; index++) {
					let evntMonth = moment(this.rangeDate[0]).add(index, 'd').format('M')
					let evntDay = moment(this.rangeDate[0]).add(index, 'd').format('DD')
					let evntDate = moment(this.rangeDate[0]).add(index, 'd').format('YYYY-MM-DD')
					
					let payload = {
						...this.form,
						month: evntMonth,
						days: evntDay,
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
			async submitResponse(){
				let response = this.questionaireList.map((el) => {
					// console.log(el)
					let scoreValues = {
                        "noScore": "no",
                        "partlyScore": "partly",
                        "yesScore": "yes",
                    }
					let scoreKey = ['noScore', 'partlyScore', 'yesScore']
					for(const i in el){
						if(scoreKey.includes(i) && el[i] === el.scoreCol && el.isCounted === 'yes'){
							el.responseCol = scoreValues[i]
						}
					}

					return {
						responseCol: el.responseCol,
						scoreCol: el.scoreCol,
						remarks: el.remarks,
						questionId: el.id,
						eventId: el.eventId,
						eid: this.user.userId
					}
				});

				console.log(response)
				this.uploadCSVData(response);
			},
			async generateQuestions(){
				let payload = {
					eventId: this.eventDetails.eventCode
				}
				this.$api.post("evaluation/get/questions", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
                        let row = res.data.map((el, index) => {
							return {
								...el,
								order: Number(el.order) || ''
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
			async downloadQuestions(){
				let payload = {
					eventId: this.eventDetails.eventCode
				}
				this.$api.post("evaluation/get/questions", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
                        let row = res.data
                        let columns = [
                            'order',
                            'scoring',
                            'scoringDesc',
                            'question',
                            'responseCol',
                            'remarks',
                        ]
                        const content = [columns.map(col => this.wrapCsvValue(col))].concat(
                            row.map(row => columns.map(col => this.wrapCsvValue(
                                row[col],
                                col.format,
                                row
                            )).join(','))
                        ).join('\n')



                        const anchor = document.createElement('a');
                        anchor.href = 'data:text/csv;charset=utf-8,' + encodeURIComponent(content);
                        anchor.target = '_blank';
                        anchor.download = `${this.eventDetails.title}_Questionaire.csv`;
                        anchor.click();
					} else {
						// show Error
						console.log('there is some error')
					}
				})
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
			getListData(value) {
				let listData;
				let day = value.date();
				listData = this.eventList.filter(el =>
				 	Number(el.days) === day && 
					Number(el.month) === Number(this.currMonth) &&
					Number(el.year) === Number(this.currYear)
				)
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
				this.checkEvelauatedResponse()
			},
			async checkEvelauatedResponse(){
				let payload = {
					eventId: this.eventDetails.eventCode,
					userId: this.user.userId
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
				
                
                let payload = {
					eventId: this.eventDetails.eventCode
				}
				this.$api.post("evaluation/get/questions", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
                        let listData = res.data
                        for(const i in csvData){
                            if(listData[i] !== undefined){
                                let scoreKey = {
                                    "no": "noScore",
                                    "No": "noScore",
                                    "partly": "partlyScore",
                                    "Partly": "partlyScore",
                                    "yes": "yesScore",
                                    "Yes": "yesScore",
                                }
                                if(listData[i].isCounted === 'yes'){
                                    csvData[i].scoreCol = Number(listData[i][scoreKey[csvData[i].responseCol]])
                                } else {
									csvData[i].scoreCol = 0
								}

								csvData[i].eid = this.user.userId
								csvData[i].eventId = listData[i].eventId
								csvData[i].questionId = listData[i].id
                            }
                        }
					} else {
						// show Error
						console.log('there is some error')
					}
				})
                console.log(csvData)
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
				const dataUploaded = await new Promise((resolve, reject) => {
					let payload = {
						csv: data
					}
					this.$api.post("evaluation/response/submit", payload).then((res) => {
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
			
				this.getList();
				this.csvData = []
				this.eventDetails = null
				this.onClose()
				this.eventDetailsModal = false
				this.uploading = false
			
			},
			
		}
	})

</script>

<style lang="scss">
</style>