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
							]" type="password" placeholder="Password" >
								<template #prefix><a-icon type="lock" /></template>
							</a-input>
						</a-form-item>
						<a-form-item class="mb-10">
							<a-switch v-model="rememberMe" /> Remember Me
						</a-form-item>
						<a-form-item>
							<a-button type="primary" block html-type="submit" class="login-form-button">
								SIGN IN
							</a-button>
						</a-form-item>
					</a-form>
					<!-- / Sign In Form -->
					
				</a-card>
				<!-- / Sign Up Form -->

			</div>
		</div>
		<!-- / Sign Up Image And Headings -->
	</div>
</template>

<script>
import jwtDecode from 'jwt-decode';
export default ({
	data() {
		return {
			// Binded model property for "Sign In Form" switch button for "Remember Me" .
			rememberMe: false,
		}
	},
	beforeCreate() {
		// Creates the form and adds to it component's "form" property.
		this.form = this.$form.createForm(this, { name: 'normal_login' });
	},
	methods: {
		// Handles input validation after submission.
		handleSubmit(e) {
			e.preventDefault();
			this.form.validateFields((err, values) => {
				if ( !err ) {
					this.$api.post("auth/login", values).then((res) => {
						let response = {...res.data}
						if(!response.error){
							let jwtData = jwtDecode(response.jwt);
							localStorage.setItem("userToken", response.jwt)
							if(jwtData.aud === 'admin'){
								this.$router.push("/dashboard")
							} else {
								this.$router.push("/evaluation")
							}
						} else {
						// show Error
							this.$message.error(response.message)
						}
					})
				}
			});
		},
	},
})
</script>