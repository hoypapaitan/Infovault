<template>
	<div>

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