<template>

	<!-- Payment Methods Card -->
	<a-card :bordered="false" class="header-solid h-full" :bodyStyle="{paddingTop: 0,}">
		<template #title>
			<a-row type="flex" align="middle">
				<a-col :span="24" :md="12">
					<h6 class="font-semibold m-0">Account Management</h6>
				</a-col>
				<!-- <a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
					<a-button type="primary">
						Change Password
					</a-button>
				</a-col> -->
			</a-row>
		</template>
		<a-row :gutter="[24, 24]">
			<a-col :span="24" :md="12">
				<a-card class="payment-method-card">
					<!-- <img src="images/logos/visa-logo.png" alt=""> -->
					<a-icon type="user" :style="{fontSize: '32px'}" />
					<h6 class="card-number">{{ user.aud }}</h6>
				</a-card>
			</a-col>
			<a-col :span="24" :md="12">
				<a-card class="payment-method-card">
					<!-- <img src="images/logos/visa-logo.png" alt=""> -->
					<a-icon type="lock" :style="{fontSize: '32px'}" />
					<h6 class="card-number">************</h6>
					<a-button @click="changePassModal = true" type="link">
						<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path class="fill-gray-7" d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z"/>
							<path class="fill-gray-7" d="M11.3787 5.79289L3 14.1716V17H5.82842L14.2071 8.62132L11.3787 5.79289Z"/>
						</svg>
					</a-button>
				</a-card>
			</a-col>
			
		</a-row>

		<a-modal
			v-model="changePassModal"
			title="Password Change"
			centered
		>
			<template slot="footer">
				<a-button key="submit" type="primary" :loading="loading" @click="changePassword">
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
				<a-col :span="24" :sm="24">
					
					<a-form-item label="Re-type Password">
						<a-input
							type="password"
							v-model="reTypePassword"
						/>
					</a-form-item>
				</a-col>
			</a-row>
		</a-modal>
	</a-card>
	<!-- Payment Methods Card -->
</template>

<script>
	import { jwtDecode } from 'jwt-decode';
	export default ({
		data() {
			return {
				password: "",
				reTypePassword: "",
				changePassModal: false,
			}
		},
		computed: {
			user: function(){
				let token = localStorage.getItem('userToken')
				return jwtDecode(token);
			},
		},
		methods: {
			changePassword(){
				let vm = this;

				if(this.password.length === 0 || this.reTypePassword.length === 0){
					vm.$message.error('Please fill in all fields')
					return false
				}

				if(this.password === this.reTypePassword){
					this.$confirm({
						title: 'Change Password',
						content: `Are you sure you want to change your password?`,
						onOk() {
							let payload = {
								id: vm.user.userId,
								newPassword: vm.reTypePassword 
							}
							vm.$api.post("users/changePassword", payload).then((res) => {
								const data = {...res.data};
								if(!data.error){
									localStorage.removeItem('userToken')
									vm.$router.push('/')
								} else {
									vm.$message.error(data.message)
								}	
							})
						},
						onCancel() {},
					});
					this.changePassModal = false
				}else{
					vm.$message.error('Passwords do not match')
				}
			}
		}
	})

</script>