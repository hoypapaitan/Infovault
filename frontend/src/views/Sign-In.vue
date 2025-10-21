<!-- 
	This is the sign in page, it uses the dashboard layout in: 
	"./layouts/Default.vue" .
 -->

<template>
	<div class="sign-in">
		
		<a-row type="flex" :gutter="[24,24]" justify="space-around" align="middle">

			<!-- Sign In Form Column -->
			<a-col :span="24" :md="12" :lg="{span: 12, offset: 0}" :xl="{span: 6, offset: 2}" class="col-form">
				<h1 class="mb-15">Sign In</h1>
				<h5 class="font-regular text-muted">Gender and Development Analytics Dashboard</h5>

				<!-- Sign In Form -->
				<a-form
					id="components-form-demo-normal-login"
					:form="form"
					class="login-form"
					@submit="handleSubmit"
					:hideRequiredMark="true"
				>
					<a-form-item class="mb-10" label="Username" :colon="false">
						<a-input 
							v-decorator="[
							'username',
							{ rules: [{ required: true, message: 'Please input your email!' }] },
							]" 
							placeholder="Username" 
						/>
					</a-form-item>
					<a-form-item class="mb-5" label="Password" :colon="false">
						<a-input
						v-decorator="[
						'password',
						{ rules: [{ required: true, message: 'Please input your password!' }] },
						]" type="password" placeholder="Password" />
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
			</a-col>
			<!-- / Sign In Form Column -->

			<!-- Sign In Image Column -->
			<a-col :span="24" :md="12" :lg="12" :xl="12" class="col-img">
				<img src="images/gadlogo.png" alt="">
			</a-col>
			<!-- Sign In Image Column -->

		</a-row>
		
	</div>
</template>

<script>
	import { jwtDecode } from 'jwt-decode';
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

<style lang="scss">
	body {
		background-color: #ffffff;
	}
</style>