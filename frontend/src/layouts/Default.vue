<!-- 
	This is the default layout, used in sign-in and sign-up pages.
 -->

<template>
	<div>

		<!-- Default Layout -->
		<a-layout class="layout-default" id="layout-default" :class="[layoutClass]">

			<!-- Layout Header ( Navbar ) -->
			<DefaultHeader></DefaultHeader>
			<!-- / Layout Header ( Navbar ) -->


			<!-- Page Content -->
			<a-layout-content>
				<router-view />
			</a-layout-content>
			<!-- / Page Content -->

		</a-layout>
		<!-- / Default Layout -->

	</div>
</template>

<script>

	import DefaultHeader from '../components/Headers/DefaultHeader' ;
	import DefaultFooter from '../components/Footers/DefaultFooter' ;

	export default ({
		components: {
			DefaultHeader,
			DefaultFooter,
		},
		data() {
			return {
			}
		},
		created(){
			// Debug logging
			console.log('Default.vue created - Route:', this.$route.name);
			console.log('Route meta:', this.$route.meta);
			console.log('noRedirect flag:', this.$route.meta.noRedirect);
			
			// Don't redirect if route hasn't loaded yet
			if (!this.$route.name) {
				console.log('Route not loaded yet, skipping redirect');
				return;
			}
			
			// Don't redirect if route has noRedirect flag
			if (this.$route.meta.noRedirect) {
				console.log('Skipping redirect due to noRedirect flag');
				return;
			}
			
			let token = localStorage.getItem('userToken')
			if(token){
				this.$router.push("/dashboard")
			} else {
				console.log('No token, redirecting to sign-in');
				this.$router.push("/sign-in")
			}
		},
		computed: {
			user: function(){
				let token = localStorage.getItem('userToken')
				token = JSON.parse(token);
				return jwtDecode(token.value);
			},
			// Sets layout's element's class based on route's meta data.
			layoutClass() {
				return this.$route.meta.layoutClass ;
			}
		},
	})

</script>