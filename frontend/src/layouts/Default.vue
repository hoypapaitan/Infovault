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

			<!-- Layout Footer -->
			<!-- <DefaultFooter></DefaultFooter> -->
			<!-- / Layout Footer -->

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
			let token = localStorage.getItem('userToken')
			if(token){
				this.$router.push("/dashboard")
			} else {
				this.$router.push("/")
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