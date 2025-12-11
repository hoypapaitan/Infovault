<!-- 
	This is the billing page, it uses the dashboard layout in: 
	"./layouts/Dashboard.vue" .
 -->

 <template>
	<div>

		<a-row type="flex" :gutter="24">

			<!-- Billing Info Column -->
			<a-col :span="24" :md="16">
				<a-row type="flex" :gutter="24">
					<a-col :span="24" class="mb-24">

						<!-- Payment Methods Card -->
						<CardPaymentMethods></CardPaymentMethods>
						<!-- Payment Methods Card -->

					</a-col>
					<a-col :span="24" :xl="12" class="mb-24">

						<!-- Master Card -->
						<CardCredit></CardCredit>
						<!-- / Master Card -->

					</a-col>
					<a-col :span="12" :xl="6" class="mb-24" v-for="(salary, index) in salaries" :key="index">
                        <a-card :bordered="false" >
                            <a-card-meta :title="salary.title" :description="salary.content">
                                <a-icon 
                                    slot="avatar"
                                    key="man"
                                    :style="{color: salary.color, fontSize: '32px'}"
                                    :type="salary.icon" 
                                />
                            </a-card-meta>
                            <template slot="actions" >
                                <a-button @click="openDBModal(salary.action)" slot="actions" type="link">
                                    {{ salary.action }}
                                </a-button>
                            </template>
                        </a-card>

					</a-col>
					
				</a-row>
			</a-col>
			<!-- / Billing Info Column -->
			
			<!-- Invoices Column -->
			<a-col :span="24" :md="8" class="mb-24">

				<!-- Invoices Card -->
				<CardInvoices
					:data="invoiceData"
				></CardInvoices>
				<!-- / Invoices Card -->

			</a-col>
			<!-- / Invoices Column -->

		</a-row>

		<!-- User Management Section -->
		<a-row type="flex" :gutter="24" style="margin-top: 24px;">
			<a-col :span="24">
				<a-card :bordered="false" class="header-solid h-full">
					<template #title>
						<a-row type="flex" align="middle">
							<a-col :span="24" :md="12">
								<h5 class="font-semibold m-0">User Management</h5>
							</a-col>
						<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
							<a-button v-if="isAdmin" type="primary" @click="addUserModal = true"> 
								<a-icon type="user-add" />Add New User 
							</a-button>
						</a-col>
						</a-row>
					</template>
					<a-table :columns="userColumns" :data-source="users" :pagination="false">
						<template slot="name" slot-scope="name">
							<p>{{ `${name.firstName} ${name.lastName}` }}</p>
						</template>

						<template slot="status" slot-scope="status">
							<a-tag class="tag-status" :class="Number(status) === 1 ? 'ant-tag-green' : 'ant-tag-red'">
								{{ Number(status) === 1 ? "Active" : "Deactive" }}
							</a-tag>
						</template>

					<template slot="editBtn" slot-scope="row">
						<a-button v-if="isAdmin" type="link" :data-id="row.key" @click="editUser(row)" class="btn-edit">
							Edit
						</a-button>
					</template>
					</a-table>
				</a-card>
			</a-col>
		</a-row>

		
		<a-modal
			v-model="backupModal"
			title="Backup Database"
			centered
		>
			<template slot="footer">
				<a-button key="submit" type="primary" :loading="loading" @click="databaseAction">
					Confirm
				</a-button>
			</template>
			<a-row :gutter="24">
				<a-col :span="24" :sm="24">
					<a-form-item label="Password">
						<a-input
							type="password"
							v-model="password"
						/>
					</a-form-item>
				</a-col>
			</a-row>
		</a-modal>
		<a-modal
			v-model="restoreModal"
			title="Restore Database"
			centered
		>
			<template slot="footer">
				<a-button key="submit" type="primary" :loading="loading" @click="restoreDb">
					Confirm
				</a-button>
			</template>
			<a-row :gutter="24">
				<a-col :span="24" :sm="24">
					<a-form-item label="Upload SQL File">
						<a-upload :file-list="fileList" :remove="handleRemove" :before-upload="beforeUpload">
							<a-button> <a-icon type="upload" /> Select File </a-button>
						</a-upload>
					</a-form-item>
					<a-form-item label="Password">
						<a-input
							type="password"
							v-model="password"
						/>
					</a-form-item>
				</a-col>
			</a-row>
		</a-modal>

		<!-- User Management Modal -->
		<a-modal
			v-model="addUserModal"
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
				<a-button key="submit" type="primary" :loading="userFormLoading" @click="onSubmitUser">
					Submit
				</a-button>
			</template>
			<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
				<a-row :gutter="24">
					<a-col :span="24" :sm="12">
						<a-form-item label="Username">
							<a-input
								:disabled="formMode === 'edit'"
								v-model="userForm.username"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="12">
						<a-form-item label="Password">
							<a-input
								:disabled="formMode === 'edit'"
								type="password"
								v-model="userForm.password"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="24">
						<a-form-item label="User Type">
							<a-select
								v-model="userForm.userType"
								placeholder="Select Type of User"
								:options="userTypeOpt"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="8">
						<a-form-item label="First Name and Suffix">
							<a-input
								v-model="userForm.firstName"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="8" >
						<a-form-item label="Middle Name">
							<a-input
								v-model="userForm.middleName"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="8" >
						<a-form-item label="Last Name">
							<a-input
								v-model="userForm.lastName"
							/>
						</a-form-item>
					</a-col>
					<a-col :span="24" :sm="12" >
						<a-form-item label="Gender">
							<a-select
								v-model="userForm.sex"
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
								v-model="userForm.contact"
							/>
						</a-form-item>
					</a-col>
				</a-row>
			</a-form>
		</a-modal>
	</div>
</template>

<script>
	import {jwtDecode} from 'jwt-decode';
	import CardCredit from "../components/Cards/CardCredit"
	import WidgetSalary from "../components/Widgets/WidgetSalary"
	import CardPaymentMethods from "../components/Cards/CardPaymentMethods"
	import CardInvoices from "../components/Cards/CardInvoices"
	import CardBillingInfo from "../components/Cards/CardBillingInfo"
	import CardTransactions from "../components/Cards/CardTransactions"


	// Salary cards data
	const salaries = [
		{
            color: 'red',
			icon: `download`,
			title: "Export",
			content: "Database",
			action: "Download",
		},
		{
            color: 'blue',
			icon: `cloud-upload`,
			title: "Import",
			content: "Database",
            action: "Upload",
		},
	] ;

	// "Invoices" list data.
	const invoiceData = [
		{
			title: "Graduate Data Format",
			code: "Fields required for Analytical Data",
			file: "/docs/analytics_data-format.csv",
			name: "GraduateDataFormat.csv",
			amount: "Data Management",
		},
		// {
		// 	title: "Event Format",
		// 	code: "Fields required for Event Evaluation",
		// 	file: "/docs/evaluation-format.csv",
		// 	name: "EvaluationFormat.csv",
		// 	amount: "Events",
		// },
	] ;

	export default ({
		components: {
			CardCredit,
			WidgetSalary,
			CardPaymentMethods,
			CardInvoices,
			CardBillingInfo,
			CardTransactions,
		},
		data() {
			return {
				// Salary cards data
				salaries,

				// Associating "Invoices" list data with its corresponding property.
				invoiceData,
				password: '',
				backupModal: false,
				restoreModal: false,
				fileracker: null,
				
				// User Management data
				users: [],
				addUserModal: false,
				formMode: 'add',
				userId: null,
				userStatus: null,
				userFormLoading: false,
				userForm: {
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
		computed: {
			user: function(){
				let token = localStorage.getItem('userToken')
				return jwtDecode(token);
			},
			isAdmin: function () {
				return this.user && this.user.aud === 'admin'
			},
			userColumns() {
				return [
					{
						title: 'ID',
						dataIndex: 'id'
					},
					{
						title: 'Name',
						scopedSlots: { 
							customRender: 'name' 
						},
					},
					{
						title: 'Username',
						dataIndex: 'username',
						scopedSlots: { customRender: 'username' },
					},
					{
						title: 'Position',
						dataIndex: 'userTypeDescription',
						class: 'text-muted',
					},
					{
						title: 'Status',
						dataIndex: 'status',
						scopedSlots: { customRender: 'status' },
					},
					{
						title: '',
						scopedSlots: { customRender: 'editBtn' },
						width: 50,
					},
				];
			}
		},
		created() {
			this.getUsersList();
		},
		methods:{
			handleRemove(file) {
				this.fileracker = null;
			},
			async beforeUpload(file) {
				this.fileracker = file;
				return false
			},
			openDBModal(type){
				if(type === 'Download'){
					this.backupModal = true;
				} else {
					this.restoreModal = true;
				}
			},
			async databaseAction(){
				let vm = this;
				this.$confirm({
					title: 'Backup Database',
					content: `Are you sure you want to backup the database?`,
					onOk() {
						let payload = {
							userId: vm.user.userId,
							password: vm.password 
						}
						vm.$api.post("misc/database/backup", payload).then((res) => {
							let response = {...res.data}
							if(!response.error){
								// console.log(res.data)
								const anchor = document.createElement('a');
								anchor.href = 'data:text/plain;charset=utf-8,' + encodeURIComponent(res.data);
								anchor.target = '_blank';
								anchor.download = `database-backup-${new Date().toISOString()}.sql`;
								anchor.click();
								vm.backupModal = false;
							} else {
								// show Error
								console.log('there is some error')
							}
						})
					},
					onCancel() {},
				});
			},
			restoreDb(){
				let vm = this;
				this.$confirm({
					title: 'Restore Database',
					content: `Are you sure you want to restore the database?`,
					onOk() {
						if (!vm.fileracker) {
							console.log('No file selected');
							return;
						}

						const formData = new FormData();
						formData.append('backup_file', vm.fileracker);
						formData.append('userId', vm.user.userId);
						formData.append('password', vm.password);

						vm.$api.post('misc/database/restore', formData, {
							headers: {
							'Content-Type': 'multipart/form-data'
							}
						}).then((res) => {
							let response = { ...res.data };
							if (!response.error) {
								console.log('Database restored successfully');
								vm.restoreModal = false;
							} else {
								console.log('There is some error');
							}
						})
					},
					onCancel() {},
				});
				
			},
			// User Management Methods
			async getUsersList(){
				this.users = []
				this.$api.get("users/getUsersList").then((res) => {
					let response = {...res.data}
					if(!response.error){
					this.users = response.list
					} else {
					// show Error
					console.log('there is some error')
					}
				})
			},
			editUser(row){
				for (const i in this.userForm) {
					if(i === 'userType'){
						row[i] = Number(row[i])
					} else if(i === 'password'){
						row[i] = '********'
					}
					this.userForm[i] = row[i]
				}
				this.formMode = 'edit'
				this.userId = row.id
				this.userStatus = row.status
				this.addUserModal = true
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
						this.clearUserForm();
						this.getUsersList()
						this.addUserModal = false
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			async onSubmitUser(){
				let payload = {}
				let api = "users/create"

				// Password format validation (only for registration, not edit)
				if(this.formMode !== 'edit'){
					const password = this.userForm.password || '';
					const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=[\]{};':"\\|,.<>/?]).{8,}$/;
					if(!passwordRegex.test(password)){
						this.$message.error('Password must be at least 8 characters and include uppercase, lowercase, number, and special character.');
						this.userFormLoading = false;
						return;
					}
				}

				if(this.formMode === 'edit'){
					payload = {
						...this.userForm,
						uid: this.userId,
						status: 1
					}
					api = "users/update"
				} else {
					payload = {
						...this.userForm,
						status: 1
					}
				}

				this.userFormLoading = true;
				this.$api.post(api, payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						this.clearUserForm();
						this.getUsersList()
						this.addUserModal = false
					} else {
						// show Error
						console.log('there is some error')
					}
					this.userFormLoading = false;
				})
			
			},
			clearUserForm(){
				this.userForm = {
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
				this.formMode = 'add'
				this.userId = null
				this.userStatus = null
			}
		}
	})

</script>

<style lang="scss">
</style>