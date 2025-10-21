<template>

	<!-- Authors Table Card -->
	<a-card :bordered="false" class="header-solid h-full" :bodyStyle="{padding: 0,}">
		<template #title>
			<a-row type="flex" align="middle">
				<a-col :span="24" :md="12">
					<h5 class="font-semibold m-0">Data Collection</h5>
				</a-col>
				<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
					<a-button type="primary"  @click="addUSerModal = true"> 
						<a-icon type="dot-chart" />Add New Data
					</a-button>
				</a-col>
			</a-row>
		</template>
		<a-table :columns="columns" :data-source="data" :pagination="true">

			<template slot="name" slot-scope="name">
				<p>{{ `${name.firstName} ${name.lastName}` }}</p>
			</template>

			<template slot="status" slot-scope="status">
				<a-tag class="tag-status" :class="Number(status) === 1 ? 'ant-tag-green' : 'ant-tag-red'">
					{{ Number(status) === 1 ? "Active" : "Deactive" }}
				</a-tag>
			</template>

			<template slot="action" slot-scope="action">
				<a-popconfirm 
					title="Are you sure you want to delete this data？" 
					@confirm="deleteDataAnalytics(action)"
					ok-text="Yes" cancel-text="No"
				>
					<a-button type="danger" icon="delete" />
				</a-popconfirm>
			</template>


		</a-table>

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
	</a-card>
	<!-- / Authors Table Card -->

</template>

<script>
import { jwtDecode } from 'jwt-decode';
import * as d3 from "d3"
	export default ({
		props: {
			data: {
				type: Array,
				default: () => [],
			},
			columns: {
				type: Array,
				default: () => [],
			},
		},
		data() {
			return {
				// Active button for the "Authors" table's card header radio button group.
				authorsHeaderBtns: 'all',
				addUSerModal: false,
				form: {
					username: '',
					password: '',
					firstName: '',
					lastName: '',
					middleName: '',
					suffix: '',
					sex: '',
					email: '',
					contact: '',
					userType: null
				},
				userTypeOpt: [
					{
						label: "Admin",
						value: 1,
					},
					{
						label: "Employee",
						value: 2,
					},
				],
			}
		},
		computed:{
			user: function(){
				let token = localStorage.getItem('userToken')
				return jwtDecode(token);
			},
		},
		methods:{
			deleteDataAnalytics(val){
				console.log(val)
				let payload = {
					dataId: val.id
				}
				this.$api.post("analytics/delete/data", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						this.$emit('updateTable')
					} else {
						// show Error
						console.log('there is some error')
					}
				})
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
		}
	})

</script>