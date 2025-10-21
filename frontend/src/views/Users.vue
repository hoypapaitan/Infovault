<template>
	<div>
		<!-- Header Background Image -->
		<div class="profile-nav-bg" style="background-image: url('images/coverbg.png')"></div>
		<!-- / Header Background Image -->

		<!-- User Profile Card -->
		<a-card :bordered="false" class="card-profile-head" :bodyStyle="{padding: 0,}">
			<template #title>
				<a-row type="flex" align="middle">
					<a-col :span="24" :md="12" class="col-info">
						<a-avatar 
							shape="square" 
							:size="74" 
							icon="file-done" 
							:style="{ backgroundColor: '#00ff95' }"
						/>
						<div class="avatar-info">
							<h4 class="font-semibold m-0">User Management</h4>
							<p>Manage user list and account details</p>
						</div>
					</a-col>
					<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
						<!-- <a-button type="primary"  @click="addUSerModal = true"> 
							<a-icon type="user-add" />Add New User 
						</a-button> -->
					</a-col>
				</a-row>
			</template>
		</a-card>
		<!-- User Profile Card -->

		<!-- Authors Table -->
		<a-row :gutter="24" type="flex">

			<!-- Authors Table Column -->
			<a-col :span="24" class="mb-24">
				<!-- Authors Table Card -->
				<CardAuthorTable
					:data="users"
					:columns="columns"
					@updateTable="getList"
				></CardAuthorTable>
				<!-- / Authors Table Card -->
			</a-col>
			<!-- / Authors Table Column -->
		</a-row>
		<!-- / Authors Table -->
		
	</div>
</template>

<script>
	// "Authors" table component.
	import CardAuthorTable from '../components/Cards/CardAuthorTable' ;

	export default ({
		components: {
			CardAuthorTable,
		},
		computed:{
			columns(){
				return [
					{
						title: 'ID',
						dataIndex: 'id'
					},
					{
						title: 'Name',
						scopedSlots: { 
							customRender: 'name' 
						},
					},
					{
						title: 'Username',
						dataIndex: 'username',
						scopedSlots: { customRender: 'username' },
					},
					{
						title: 'Position',
						dataIndex: 'userTypeDescription',
						class: 'text-muted',
					},
					{
						title: 'Status',
						dataIndex: 'status',
						scopedSlots: { customRender: 'status' },
					},
					{
						title: '',
						scopedSlots: { customRender: 'editBtn' },
						width: 50,
					},
				];
			}
		},
		data() {
			return {
				// Associating "Authors" table columns with its corresponding property.
				users: [],
			}
		},
		created(){
			this.getList();
		},
		methods:{
			async getList(){
				this.users = []
				this.$api.get("users/getUsersList").then((res) => {
					let response = {...res.data}
					if(!response.error){
					this.users = response.list
					} else {
					// show Error
					console.log('there is some error')
					}
				})
			},
			
		}
	})

</script>

<style lang="scss">
</style>