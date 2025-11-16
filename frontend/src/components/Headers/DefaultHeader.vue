<template>

	<!-- Layout Header ( Navbar ) -->
	<a-layout-header>
		<div class="header-col header-brand">
			<img src="images/ASCT_logo3.png" width="50%" alt="">
		</div>
		<div class="header-col header-nav">

			<!-- Navigation Menu For Large Screens -->
			<a-menu mode="horizontal" class="menu-large">
				<a-menu-item>
					<router-link to="/sign-in" class="nav-link" @click="e => e.preventDefault()">
						<a-icon type="user" :style="{ fontSize: '16px'}" />
						<span>Login</span>
					</router-link>
				</a-menu-item>
				<a-menu-item>
					<router-link to="/sign-up" class="nav-link" @click="e => e.preventDefault()">
						<a-icon type="user" :style="{ fontSize: '16px'}" />
						<span>Register</span>
					</router-link>
				</a-menu-item>
				<!-- <a-menu-item>
					<router-link to="/about-us" class="nav-link" @click="e => e.preventDefault()">
						<a-icon type="question" :style="{ fontSize: '16px'}" />
						<span>About Us</span>
					</router-link>
				</a-menu-item>
				<a-menu-item>
					<router-link to="/contact-us" class="nav-link" @click="e => e.preventDefault()">
						<a-icon type="phone" :style="{ fontSize: '16px'}" />
						<span>Contact Us</span>
					</router-link>
				</a-menu-item> -->
			</a-menu>
			<!-- / Navigation Menu For Large Screens -->

		</div>
	</a-layout-header>
	<!-- / Layout Header ( Navbar ) -->

</template>

<script>

	export default ({
		data() {
			return {
				// Collapse navigation value.
				// Binded model property for "Collapsible Navigation Menu" collapsed status .
				collapseNav: 0,
			}
		},
	})

</script>

<style lang="scss" scoped>

	.nav-link svg {
		margin-right: 5px;
		vertical-align: middle;
	}
	.nav-link span {
		vertical-align: middle;
	}
	.ant-menu-submenu-popup {
		width: 100%;
	}

</style>