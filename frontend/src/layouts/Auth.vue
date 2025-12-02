<!-- 
	This is the auth layout, used for password reset and similar pages without redirect logic.
 -->

<template>
	<div>
		<!-- Auth Layout -->
		<a-layout class="layout-default" id="layout-auth" :class="[layoutClass]">
			<!-- Page Content -->
			<a-layout-content>
				<router-view />
			</a-layout-content>
			<!-- / Page Content -->
		</a-layout>
		<!-- / Auth Layout -->
	</div>
</template>

<script>
	export default ({
		data() {
			return {
			}
		},
		computed: {
			// Sets layout's element's class based on route's meta data.
			layoutClass() {
				return this.$route.meta.layoutClass || 'layout-sign-in';
			}
		},
	})
</script>
