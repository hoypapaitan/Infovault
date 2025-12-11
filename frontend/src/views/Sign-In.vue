<!-- 
	This is the sign up page, it uses the dashboard layout in: 
	"./layouts/Default.vue" .
 -->

<template>
	<div>

		<!-- Sign Up Image And Headings -->
		<div class="sign-in-header">
			<div class="content">
				<h3 class="mb-5">ASCOT Graduate Records Repository</h3>
				<p class="text-lg text-subtitle">A web-based graduate tracking and records of information</p>

				<!-- Sign Up Form -->
				<a-card :bordered="false" class="card-signin header-solid h-full" :bodyStyle="{paddingTop: 0}">
					<template #title>
						<h5 class="font-semibold text-center">Administrative Login</h5>
					</template>
					<a-form
						id="components-form-demo-normal-login"
						:form="form"
						class="login-form"
						@submit="handleSubmit"
						:hideRequiredMark="true"
					>
						<a-form-item class="mb-5 mt-20" :colon="false">
							<a-input 
								v-decorator="[
								'username',
								{ rules: [{ required: true, message: 'Please input your email!' }] },
								]" 
								placeholder="Username" 
							>
								<template #prefix><a-icon type="user" /></template>
							</a-input>
						</a-form-item>
						<a-form-item class="mb-5" :colon="false">
							<a-input
							v-decorator="[
							'password',
							{ rules: [{ required: true, message: 'Please input your password!' }] },
							]" 
							:type="showPassword ? 'text' : 'password'" placeholder="Password" >
								<template #prefix><a-icon type="lock" /></template>
								<template #suffix>
									<a-icon v-if="!showPassword" type="eye" @click="showPassword = !showPassword" />
									<a-icon v-if="showPassword" type="eye-invisible" @click="showPassword = !showPassword" />
								</template>
							</a-input>
						</a-form-item>
					<a-form-item class="mb-10">
						<a-switch v-model="rememberMe" /> Remember Me
					</a-form-item>
				<a-form-item>
					<a-button type="primary" block html-type="submit" class="login-form-button" :loading="loginLoading">
						SIGN IN
					</a-button>
				</a-form-item>
					<a-form-item>
						<a-button
							type="link"
							block
							:disabled="!usernameFilled"
							@click="showForgotModal = true"
							class="forgot-password-button"
						>
							Forgot Password?
						</a-button>
					</a-form-item>
				</a-form>
				<!-- / Sign In Form -->				</a-card>
				<!-- / Sign Up Form -->

			</div>
		</div>
		<!-- / Sign Up Image And Headings -->

		<!-- Forgot Password Modal -->
		<a-modal
			v-model="showForgotModal"
			title="Forgot Password"
			:footer="null"
			@cancel="handleForgotCancel"
		>
			<div>
				<p class="mb-10">Please enter the email address registered to your username:</p>
				<a-input
					v-model="forgotEmail"
					placeholder="Registered Email Address"
					type="email"
					class="mb-15"
				>
					<template #prefix><a-icon type="mail" /></template>
				</a-input>
				<a-button
					type="primary"
					block
					:disabled="!forgotEmail"
					:loading="resetLoading"
					@click="handleResetPassword"
				>
					Reset Password
				</a-button>
			</div>
		</a-modal>
	</div>
</template>

<script>
export default {
	data() {
		return {
			rememberMe: false,
			showPassword: false,
			showForgotModal: false,
			forgotEmail: '',
			resetLoading: false,
			usernameValue: '',
			loginLoading: false,
		}
	},
	computed: {
		usernameFilled() {
			return !!this.usernameValue && this.usernameValue.trim().length > 0;
		}
	},
	beforeCreate() {
		this.form = this.$form.createForm(this, { 
			name: 'normal_login',
			onFieldsChange: (props, fields) => {
				if (fields.username) {
					this.usernameValue = fields.username.value || '';
				}
			}
		});
	},
	methods: {
		handleSubmit(e) {
			e.preventDefault();
			console.log('Form submitted');
			
			this.form.validateFields((err, values) => {
				if (err) {
					console.log('Validation error:', err);
					return;
				}
				
				console.log('Form validated, calling API with:', values);
				console.log('API Base URL:', this.$api.defaults.baseURL);
				this.loginLoading = true;
				
				this.$api.post("auth/login", values)
					.then((res) => {
						console.log('API response received:', res);
						console.log('Response status:', res.status);
						this.loginLoading = false;
						let response = {...res.data}

						// Show locked message if error 403
						if (res.status === 403 || response.error === 403) {
							this.$message.error(response.message || 'Your account is locked due to too many failed login attempts. Please contact an admin to unlock.');
							return;
						}

						if(!response.error){
							localStorage.setItem("userToken", response.jwt)
							this.$message.success('Login successful!');
							this.$router.push("/dashboard")
						} else {
							this.$message.error(response.message || 'Login failed')
						}
					})
					.catch((error) => {
						console.error('API error:', error);
						console.error('Error response:', error.response);
						console.error('Error request:', error.request);
						this.loginLoading = false;
						
						if (error.response) {
							console.error('Error response data:', error.response.data);
							this.$message.error(error.response.data.message || 'Your account is locked due to too many failed login attempts. Please contact an admin to unlock.');
							return;
						} else if (error.request) {
							this.$message.error('No response from server. Check if backend is running on http://localhost:8080');
						} else {
							this.$message.error('An error occurred during login.');
						}
					});
			});
		},
		handleForgotCancel() {
			this.showForgotModal = false;
			this.forgotEmail = '';
			this.resetLoading = false;
		},
		handleResetPassword() {
			const username = this.form.getFieldValue('username');
			
			if (!username || !username.trim()) {
				this.$message.error('Username is required.');
				return;
			}
			
			if (!this.forgotEmail || !this.forgotEmail.trim()) {
				this.$message.error('Please enter your registered email address.');
				return;
			}
			
			this.resetLoading = true;
			
			this.$api.post('auth/forgot-password', {
				username: username.trim(),
				email: this.forgotEmail.trim()
			}).then(res => {
				this.resetLoading = false;
				if (res.data && !res.data.error) {
					this.$message.success('Password reset instructions have been sent to your email.');
					this.handleForgotCancel();
				} else {
					this.$message.error(res.data.message || 'Failed to process password reset request.');
				}
			}).catch(err => {
				this.resetLoading = false;
				this.$message.error('An error occurred while processing your request.');
			});
		}
	},
}
</script>