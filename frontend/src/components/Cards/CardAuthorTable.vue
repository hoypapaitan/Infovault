<template>

	<!-- Authors Table Card -->
	<a-card :bordered="false" class="header-solid h-full" :bodyStyle="{padding: 0,}">
		<template #title>
			<a-row type="flex" align="middle">
				<a-col :span="24" :md="12">
					<h5 class="font-semibold m-0">User's List</h5>
				</a-col>
				<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
					<a-button type="primary"  @click="addUSerModal = true"> 
						<a-icon type="user-add" />Add New User 
					</a-button>
				</a-col>
			</a-row>
		</template>
		<a-table :columns="columns" :data-source="data" :pagination="false">

			<template slot="name" slot-scope="name">
				<p>{{ `${name.firstName} ${name.lastName}` }}</p>
			</template>

			<template slot="status" slot-scope="status">
				<a-tag class="tag-status" :class="Number(status) === 1 ? 'ant-tag-green' : 'ant-tag-red'">
					{{ Number(status) === 1 ? "Active" : "Deactive" }}
				</a-tag>
			</template>

			<template slot="editBtn" slot-scope="row">
				<a-button type="link" :data-id="row.key"  @click="editUser(row)" class="btn-edit">
					Edit
				</a-button>
			</template>

		</a-table>

		<a-modal
			v-model="addUSerModal"
			title="Add User Form"
			centered
		>
			<template slot="footer">
				<a-popconfirm
					title="Are you sure you want to Delete this user?" 
					@confirm="updateUserStatus(3)"
					ok-text="Yes" cancel-text="No"
				>
					<a-button type="link" icon="delete">Delete User</a-button>
				</a-popconfirm>
				<a-popconfirm
					v-if="userStatus === '1'"
					title="Are you sure you want to Deactivate this user?" 
					@confirm="updateUserStatus(0)"
					ok-text="Yes" cancel-text="No"
				>
					<a-button type="link" icon="lock">Deactivate</a-button>
				</a-popconfirm>
				<a-popconfirm
					v-if="userStatus === '0'"
					title="Are you sure you want to Activate this user?" 
					@confirm="updateUserStatus(1)"
					ok-text="Yes" cancel-text="No"
				>
					<a-button type="link" icon="unlock">Activate</a-button>
				</a-popconfirm>
				<a-button key="submit" type="primary" :loading="loading" @click="onSubmit">
					Submit
				</a-button>
				
			</template>
			<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
				<a-row :gutter="24">
					<a-col :span="24" :sm="12">
						<a-form-item label="Username">
							<a-input
								:disabled="formMode === 'edit'"
								v-model="form.username"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="12">
						<a-form-item label="Password">
							<a-input
								:disabled="formMode === 'edit'"
								type="password"
								v-model="form.password"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="24">
						<a-form-item label="User Type">
							<a-select
								v-model="form.userType"
								placeholder="Select Type of User"
								:options="userTypeOpt"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="8">
						<a-form-item label="First Name and Suffix">
							<a-input
								v-model="form.firstName"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="8" >
						<a-form-item label="Middle Name">
							<a-input
								v-model="form.middleName"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="8" >
						<a-form-item label="Last Name">
							<a-input
								v-model="form.lastName"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="12" >
						<a-form-item label="Gender">
							<a-select
								v-model="form.sex"
							>
								<a-select-option value="Male">
									Male
								</a-select-option>
								<a-select-option value="Female">
									Female
								</a-select-option>
							</a-select>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="12" >
						<a-form-item label="Contact">
							<a-input
								v-model="form.contact"
							/>
						</a-form-item>
					</a-col>
				</a-row>
			</a-form>
		</a-modal>
	</a-card>
	<!-- / Authors Table Card -->

</template>

<script>

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
				formMode: "add",
				userId: null,
				userStatus: null,
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
						label: "Super Admin",
						value: 1,
					},
					{
						label: "Coordinator",
						value: 2,
					},
				],
			}
		},
		methods:{
			editUser(row){
				for (const i in this.form) {
					if(i === 'userType'){
						row[i] = Number(row[i])
					} else if(i === 'password'){
						row[i] = '********'
					}
					this.form[i] = row[i]
				}
				console.log(row)
				this.formMode = 'edit'
				this.userId = row.id
				this.userStatus = row.status
				this.addUSerModal = true
			},
			async updateUserStatus(status){
				let payload = {
					status,
					uid: this.userId,
				}
				let api = "users/update/status"


				this.$api.post(api, payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						this.clearForm();
						this.$emit('updateTable')
						this.addUSerModal = false
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			async onSubmit(){
				let payload = {}
				let api = "users/create"

				if(this.formMode === 'edit'){
					payload = {
						...this.form,
						uid: this.userId,
						status: 1
					}
					api = "users/update"
				} else {
					payload = {
						...this.form,
						status: 1
					}
				}

				this.$api.post(api, payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						this.clearForm();
						this.$emit('updateTable')
						this.addUSerModal = false
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			
			},
			clearForm(){
				this.form = {
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
				}
			}
		}
	})

</script>