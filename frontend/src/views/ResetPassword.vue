<template>
	<div>
		<!-- Reset Password Header -->
		<div class="sign-in-header">
			<div class="content">
				<h3 class="mb-5">ASCOT Graduate Records Repository</h3>
				<p class="text-lg text-subtitle">Reset Your Password</p>

				<!-- Reset Password Form -->
				<a-card :bordered="false" class="card-signin header-solid h-full" :bodyStyle="{paddingTop: 0}">
					<template #title>
						<h5 class="font-semibold text-center">Reset Password</h5>
					</template>

					<a-spin :spinning="loading">
						<div v-if="!tokenValid && !loading" class="error-message">
							<a-result
								status="error"
								title="Invalid or Expired Link"
								sub-title="The password reset link is invalid or has expired."
							>
								<template #extra>
									<a-button type="primary" @click="goToSignIn">
										Back to Sign In
									</a-button>
								</template>
							</a-result>
						</div>

						<a-form
							v-else
							id="reset-password-form"
							:form="form"
							class="login-form"
							@submit="handleSubmit"
							:hideRequiredMark="true"
						>
							<a-alert
								v-if="errorMessage"
								:message="errorMessage"
								type="error"
								show-icon
								closable
								@close="errorMessage = ''"
								class="mb-15"
							/>

							<a-form-item class="mb-15" label="Email Address">
								<a-input
									v-model="email"
									disabled
									placeholder="Email"
								>
									<template #prefix><a-icon type="mail" /></template>
								</a-input>
							</a-form-item>

							<a-form-item class="mb-15" :colon="false">
								<a-input
									v-decorator="[
										'newPassword',
										{ rules: [{ required: true, message: 'Please enter your new password!' }, { min: 6, message: 'Password must be at least 6 characters!' }] },
									]"
									:type="showPassword ? 'text' : 'password'"
									placeholder="New Password"
								>
									<template #prefix><a-icon type="lock" /></template>
									<template #suffix>
										<a-icon v-if="!showPassword" type="eye" @click="showPassword = !showPassword" />
										<a-icon v-if="showPassword" type="eye-invisible" @click="showPassword = !showPassword" />
									</template>
								</a-input>
							</a-form-item>

							<a-form-item class="mb-15" :colon="false">
								<a-input
									v-decorator="[
										'confirmPassword',
										{ rules: [
											{ required: true, message: 'Please confirm your password!' },
											{ validator: validatePasswordMatch }
										] },
									]"
									:type="showConfirmPassword ? 'text' : 'password'"
									placeholder="Confirm New Password"
								>
									<template #prefix><a-icon type="lock" /></template>
									<template #suffix>
										<a-icon v-if="!showConfirmPassword" type="eye" @click="showConfirmPassword = !showConfirmPassword" />
										<a-icon v-if="showConfirmPassword" type="eye-invisible" @click="showConfirmPassword = !showConfirmPassword" />
									</template>
								</a-input>
							</a-form-item>

							<a-form-item>
								<a-button type="primary" block html-type="submit" class="login-form-button" :loading="submitting">
									RESET PASSWORD
								</a-button>
							</a-form-item>

							<a-form-item class="text-center">
								<router-link to="/sign-in">
									<a-icon type="arrow-left" /> Back to Sign In
								</router-link>
							</a-form-item>
						</a-form>
					</a-spin>
				</a-card>
				<!-- / Reset Password Form -->

			</div>
		</div>
		<!-- / Reset Password Header -->
	</div>
</template>

<script>
export default {
	name: 'ResetPassword',
	data() {
		return {
			email: '',
			token: '',
			tokenValid: false,
			loading: true,
			submitting: false,
			errorMessage: '',
			showPassword: false,
			showConfirmPassword: false,
		};
	},
	beforeCreate() {
		this.form = this.$form.createForm(this, { name: 'reset_password' });
	},
	mounted() {
		this.token = this.$route.params.token;
		this.validateToken();
	},
	methods: {
		async validateToken() {
			try {
				const response = await this.$api.post('/auth/validate-reset-token', {
					token: this.token
				});

				if (response.data.valid) {
					this.tokenValid = true;
					this.email = response.data.email;
				} else {
					this.tokenValid = false;
				}
			} catch (error) {
				console.error('Token validation error:', error);
				this.tokenValid = false;
			} finally {
				this.loading = false;
			}
		},
		
		validatePasswordMatch(rule, value, callback) {
			const newPassword = this.form.getFieldValue('newPassword');
			if (value && value !== newPassword) {
				callback('Passwords do not match!');
			} else {
				callback();
			}
		},
		
		handleSubmit(e) {
			e.preventDefault();
			
			this.form.validateFields(async (err, values) => {
				if (err) {
					return;
				}

				this.submitting = true;
				this.errorMessage = '';

				try {
					const response = await this.$api.post('/auth/reset-password', {
						token: this.token,
						newPassword: values.newPassword
					});

					if (response.data.success) {
						this.$message.success({
							content: 'Password reset successful! Redirecting to sign in...',
							duration: 3,
						});

						setTimeout(() => {
							this.$router.push('/sign-in');
						}, 2000);
					}
				} catch (error) {
					console.error('Password reset error:', error);
					
					if (error.response && error.response.data) {
						this.errorMessage = error.response.data.message || 'Failed to reset password';
					} else {
						this.errorMessage = 'An error occurred. Please try again.';
					}
				} finally {
					this.submitting = false;
				}
			});
		},

		goToSignIn() {
			this.$router.push('/sign-in');
		},
	},
};
</script>

<style scoped>
/* Uses the same styles as Sign-In page */
</style>
