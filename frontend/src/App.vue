<!-- 
	This is the main page of the application, the layout component is used here,
	and the router-view is passed to it.
	Layout component is dynamically declared based on the layout for each route,
	specified in routes list router/index.js .
 -->

<template>
	<div id="app">
		<component v-if="layout" :is="layout">
			<router-view />
		</component>
		<router-view v-else />
	</div>
</template>

<script>

	export default ({
		computed: {
			// Sets components name based on current route's specified layout, defaults to
			// <layout-default></layout-default> component.
			layout() {
				const routeLayout = this.$route.meta.layout;
				// If layout is explicitly set to false, return null (no layout)
				if (routeLayout === false) {
					return null;
				}
				return "layout-" + ( routeLayout || "default" ).toLowerCase() ;
			}
		},
	})
	
</script>

<style lang="scss">
</style>