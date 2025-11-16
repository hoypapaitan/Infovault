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
						<h5 class="font-semibold text-center">User Registration</h5>
					</template>
					<a-form
						id="components-form-demo-register"
						:form="form"
						class="register-form"
						@submit="handleSubmit"
						:hideRequiredMark="true"
						style="max-height: 500px; overflow-y: auto; padding-right: 10px;"
					>
						<!-- Username -->
						<a-form-item class="mb-4" :colon="false">
							<a-input 
								v-decorator="[
								'username',
								{ rules: [
									{ required: true, message: 'Please input username!' },
									{ min: 3, message: 'Username must be at least 3 characters!' }
								]},
								]" 
								placeholder="Username" 
							>
								<template #prefix><a-icon type="user" /></template>
							</a-input>
						</a-form-item>

						<!-- Password -->
						<a-form-item class="mb-4" :colon="false">
							<a-input
							v-decorator="[
							'password',
							{ rules: [
								{ required: true, message: 'Please input password!' },
								{ min: 6, message: 'Password must be at least 6 characters!' },
								{ validator: validatePassword }
							]},
							]" 
							:type="showPassword ? 'text' : 'password'" 
							placeholder="Password" 
							>
								<template #prefix><a-icon type="lock" /></template>
								<template #suffix>
									<a-icon v-if="!showPassword" type="eye" @click="showPassword = !showPassword" />
									<a-icon v-if="showPassword" type="eye-invisible" @click="showPassword = !showPassword" />
								</template>
							</a-input>
						</a-form-item>

						<!-- Confirm Password -->
						<a-form-item class="mb-4" :colon="false">
							<a-input
							v-decorator="[
							'confirmPassword',
							{ rules: [
								{ required: true, message: 'Please confirm your password!' },
								{ validator: checkPasswordConfirmation }
							]},
							]" 
							type="password" 
							placeholder="Confirm Password" 
							>
								<template #prefix><a-icon type="lock" /></template>
							</a-input>
						</a-form-item>

						<!-- First Name -->
						<a-form-item class="mb-4" :colon="false">
							<a-input 
								v-decorator="[
								'firstName',
								{ rules: [{ required: true, message: 'Please input first name!' }] },
								]" 
								placeholder="First Name" 
							>
								<template #prefix><a-icon type="idcard" /></template>
							</a-input>
						</a-form-item>

						<!-- Last Name -->
						<a-form-item class="mb-4" :colon="false">
							<a-input 
								v-decorator="[
								'lastName',
								{ rules: [{ required: true, message: 'Please input last name!' }] },
								]" 
								placeholder="Last Name" 
							>
								<template #prefix><a-icon type="idcard" /></template>
							</a-input>
						</a-form-item>

						<!-- Middle Name -->
						<a-form-item class="mb-4" :colon="false">
							<a-input 
								v-decorator="[
								'middleName',
								]" 
								placeholder="Middle Name (Optional)" 
							>
								<template #prefix><a-icon type="idcard" /></template>
							</a-input>
						</a-form-item>

						<!-- Sex -->
						<a-form-item class="mb-4" :colon="false">
							<a-select 
								v-decorator="[
								'sex',
								{ rules: [{ required: true, message: 'Please select sex!' }] },
								]" 
								placeholder="Select Sex"
							>
								<a-select-option value="Male">Male</a-select-option>
								<a-select-option value="Female">Female</a-select-option>
							</a-select>
						</a-form-item>

						<!-- Email -->
						<a-form-item class="mb-4" :colon="false">
							<a-input 
								v-decorator="[
								'email',
								{ rules: [
									{ required: true, message: 'Please input email!' },
									{ type: 'email', message: 'Please enter a valid email!' }
								]},
								]" 
								placeholder="Email Address" 
							>
								<template #prefix><a-icon type="mail" /></template>
							</a-input>
						</a-form-item>

						<!-- Address -->
						<a-form-item class="mb-4" :colon="false">
							<a-textarea 
								v-decorator="[
								'address',
								{ rules: [{ required: true, message: 'Please input address!' }] },
								]" 
								placeholder="Complete Address"
								:rows="2" 
							/>
						</a-form-item>

						<!-- Contact -->
						<a-form-item class="mb-4" :colon="false">
							<a-input 
								v-decorator="[
								'contact',
								{ rules: [
									{ required: true, message: 'Please input contact number!' },
									{ pattern: /^[0-9+\-\s]+$/, message: 'Please enter a valid contact number!' }
								]},
								]" 
								placeholder="Contact Number" 
							>
								<template #prefix><a-icon type="phone" /></template>
							</a-input>
						</a-form-item>

						<!-- Submit Button -->
						<a-form-item class="mb-4">
							<a-button 
								type="primary" 
								block 
								html-type="submit" 
								class="register-form-button"
								:loading="isSubmitting"
							>
								REGISTER
							</a-button>
						</a-form-item>

						<!-- Sign In Link -->
						<a-form-item class="text-center">
							<span>Already have an account? </span>
							<router-link to="/sign-in" class="text-primary">Sign In</router-link>
						</a-form-item>
					</a-form>
					<!-- / Sign Up Form -->
					
				</a-card>
				<!-- / Sign Up Form -->

			</div>
		</div>
		<!-- / Sign Up Image And Headings -->
	</div>
</template>

<script>
export default ({
	data() {
		return {
			showPassword: false,
			isSubmitting: false,
		}
	},
	beforeCreate() {
		// Creates the form and adds to it component's "form" property.
		this.form = this.$form.createForm(this, { name: 'user_registration' });
	},
	methods: {
		// Custom validator for password confirmation
		checkPasswordConfirmation(rule, value, callback) {
			const { form } = this;
			if (value && value !== form.getFieldValue('password')) {
				callback('Passwords do not match!');
			} else {
				callback();
			}
		},

		// Validate password strength
		validatePassword(rule, value, callback) {
			if (!value) {
				callback();
				return;
			}
			
			// Check for minimum requirements
			const hasLetter = /[a-zA-Z]/.test(value);
			const hasNumber = /\d/.test(value);
			
			if (!hasLetter || !hasNumber) {
				callback('Password must contain at least one letter and one number!');
			} else {
				callback();
			}
		},

		// Handles input validation and form submission
		handleSubmit(e) {
			e.preventDefault();
			this.form.validateFields((err, values) => {
				if (!err) {
					this.registerUser(values);
				}
			});
		},

		// Register user API call
		async registerUser(formData) {
			this.isSubmitting = true;
			
			try {
				// Prepare data for API (exclude confirmPassword and add defaults)
				const registrationData = {
					username: formData.username,
					password: formData.password,
					firstName: formData.firstName,
					lastName: formData.lastName,
					middleName: formData.middleName || '',
					sex: formData.sex,
					email: formData.email,
					address: formData.address,
					contact: formData.contact,
					userType: 1, // Default value
					status: 1    // Default value
				};

				const response = await this.$api.post('users/create', registrationData);
				
				if (response.data && !response.data.error) {
					// Registration successful
					this.$message.success({
						content: response.data.title || 'Registration successful!',
						duration: 3
					});
					
					// Clear form
					this.form.resetFields();
					
					// Redirect to sign-in page after a brief delay
					setTimeout(() => {
						this.$router.push('/sign-in');
					}, 2000);
					
				} else {
					// Registration failed
					this.$message.error({
						content: response.data?.message || 'Registration failed. Please try again.',
						duration: 5
					});
				}
				
			} catch (error) {
				console.error('Registration error:', error);
				
				// Handle different types of errors
				if (error.response) {
					// Server responded with error status
					const errorMessage = error.response.data?.message || 'Registration failed. Please check your information.';
					this.$message.error({
						content: errorMessage,
						duration: 5
					});
				} else if (error.request) {
					// Network error
					this.$message.error({
						content: 'Network error. Please check your connection and try again.',
						duration: 5
					});
				} else {
					// Other error
					this.$message.error({
						content: 'An unexpected error occurred. Please try again.',
						duration: 5
					});
				}
			} finally {
				this.isSubmitting = false;
			}
		},
	},
})
</script>