<template>
	
	<!-- Main Sidebar -->
	<a-layout-sider
		collapsible
		class="sider-primary"
		breakpoint="lg"
		collapsed-width="0"
		width="250px"
		:collapsed="sidebarCollapsed"
		@collapse="$emit('toggleSidebar', ! sidebarCollapsed)"
		:trigger="null"
		:class="['ant-layout-sider-' + sidebarColor, 'ant-layout-sider-' + sidebarTheme]"
		theme="light"
		:style="{ backgroundColor: 'transparent',}"
	>
			<div class="brand">
				<!-- <a-icon type="pie-chart" :style="{ fontSize: '24px', color: '#08c' }" /> -->
				<img src="images/ASCT_logo2.png"  alt=""> <br />
				<span>Graduate Records Repository</span>
			</div>
			<hr>
			
			
			<!-- Sidebar Navigation Menu -->
			<a-menu v-if="user.aud === 'admin'" :style="{ marginTop: '28px' }"  theme="light" mode="inline" :selectedKeys="[currentRoute]">
				<a-menu-item key="dashboard">
					<router-link to="/dashboard">
						<div class="icon" style="text-align: center;">
							<a-icon type="dashboard" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Dashboard</span>
					</router-link>
				</a-menu-item>
				<!-- <a-menu-item key="analytics">
					<router-link to="/analytics">
						<div class="icon" style="text-align: center;">
							<a-icon type="bar-chart" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Analytics</span>
					</router-link>
				</a-menu-item> -->
				<!-- <a-menu-item key="userManagement">
					<router-link to="/userManagement">
						<div class="icon" style="text-align: center;">
							<a-icon type="user" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">User Management</span>
					</router-link>
				</a-menu-item> -->
				<a-menu-item key="dataManagement">
					<router-link to="/dataManagement">
						<div class="icon" style="text-align: center;">
							<a-icon type="unordered-list" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Records</span>
					</router-link>
				</a-menu-item>
				<!-- <a-menu-item>
					<router-link to="/resources">
						<div class="icon" style="text-align: center;">
							<a-icon type="file-pdf" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Resources</span>
					</router-link>
				</a-menu-item> -->
				<!-- <a-menu-item>
					<router-link to="/events">
						<div class="icon" style="text-align: center;">
							<a-icon type="calendar" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Events</span>
					</router-link>
				</a-menu-item> -->
				<a-menu-item key="settings">
					<router-link to="/settings">
						<div class="icon" style="text-align: center;">
							<a-icon type="setting" theme="filled" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Settings</span>
				</router-link>
			</a-menu-item>
			
		</a-menu>
		<!-- Normal User -->
			<a-menu v-else theme="light" mode="inline" :selectedKeys="[currentRoute]">
				<a-menu-item key="dashboard">
					<router-link to="/dashboard">
						<div class="icon" style="text-align: center;">
							<a-icon type="dashboard" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
						<span class="label">Dashboard</span>
					</router-link>
				</a-menu-item>
				<a-menu-item key="analytics">
					<router-link to="/analytics">
						<div class="icon" style="text-align: center;">
							<a-icon type="bar-chart" :style="{ fontSize: '18px', color: '#08c', marginLeft: '8px' }" />
						</div>
					<span class="label">Analytics</span>
			</router-link>
		</a-menu-item>
	</a-menu>
	
	<!-- Logout Button -->
	<div style="padding: 16px;">
		<a-button type="danger" block @click="handleLogout" size="large">
			<a-icon type="logout" />
			Logout
		</a-button>
	</div>
	<!-- / Sidebar Navigation Menu -->
	</a-layout-sider>
	<!-- / Main Sidebar -->

</template>

<script>
	import {jwtDecode} from 'jwt-decode';

	export default ({
		props: {
			// Sidebar collapsed status.
			sidebarCollapsed: {
				type: Boolean,
				default: false,
			},
			
			// Main sidebar color.
			sidebarColor: {
				type: String,
				default: "primary",
			},
			
			// Main sidebar theme : light, white, dark.
			sidebarTheme: {
				type: String,
				default: "light",
			},
		},
		computed:{
			user: function(){
				let token = localStorage.getItem('userToken')
				return jwtDecode(token);
			},
			currentRoute() {
				const routeName = this.$route.path.split('/')[1];
				return routeName || 'dashboard';
			}
		},
		created(){
			console.log(this.user)
		},
		data() {
			return {
				// sidebarCollapsedModel: this.sidebarCollapsed,
			}
		},
		methods: {
			handleLogout() {
				this.$confirm({
					title: 'Logout Confirmation',
					content: 'Are you sure you want to logout?',
					okText: 'Yes, Logout',
					okType: 'danger',
					cancelText: 'Cancel',
					onOk: () => {
						localStorage.removeItem('userToken');
						this.$message.success('Logged out successfully');
						this.$router.push('/sign-in');
					},
					onCancel() {
						// Do nothing
					},
				});
			}
		}
	})

</script>
