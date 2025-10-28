<template>

	<!-- Master Card -->
	<a-card class="card-credit header-solid h-full" style="background-image: url('images/info-card-2.jpg')">
		
		<!-- <h5 class="card-number">ASCOT USER ID: {{ user.userId }}</h5> -->
		
		<div class="card-footer">
			<div class="mr-30">
				<p>Account Name</p>
				<h6>{{ user.fullName }}</h6>
			</div>
			<!-- <div class="mr-30">
				<p>User Type</p>
				<h6>{{ user.aud }}</h6>
			</div> -->
			<!-- <div class="card-footer-col col-logo ml-auto">
				<img src="images/ASCT_logo.png">
			</div> -->
		</div>
	</a-card>
	<!-- / Master Card -->

</template>

<script>
	import jwtDecode from 'jwt-decode';

	export default ({
		
		data() {
			return {
			}
		},
		computed: {
			user: function(){
				// Robust token read: support raw token string or JSON-wrapped object { value: '...' }
				let raw = localStorage.getItem('userToken')
				if(!raw) return null
				let tokenString = raw
				try{
					const parsed = JSON.parse(raw)
					if(parsed && parsed.value) tokenString = parsed.value
				} catch(e){
					// not JSON, assume raw token string
				}
				try{
					return jwtDecode(tokenString)
				} catch(e){
					console.error('Failed to decode JWT token', e)
					return null
				}
			},
		},
		created(){
			console.log(this.user)
		}
	})

</script>