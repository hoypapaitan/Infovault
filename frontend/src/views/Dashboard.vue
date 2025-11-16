<!-- 
	Simple Dashboard - Graduate List with Filtering
 -->

<template>
	<div class="dashboard-container">
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
							icon="dashboard" 
							:style="{ backgroundColor: '#1e9ecf' }"
						/>
						<div class="avatar-info">
							<h4 class="font-semibold m-0">Graduate Dashboard</h4>
							<p>List of all graduate students with search and filter capabilities</p>
						</div>
					</a-col>
					<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
						<a-space size="small">
							<a-button type="primary" @click="exportGraduateList" :loading="exporting">
								<a-icon type="download" />Export List
							</a-button>
							<a-button type="default" @click="refreshData" :loading="loading">
								<a-icon type="reload" />Refresh
							</a-button>
						</a-space>
					</a-col>
				</a-row>
			</template>
		</a-card>
		<!-- User Profile Card -->

		<!-- Filters Section -->
		<a-row :gutter="24" class="mb-24">
			<a-col :span="24">
				<a-card title="Search & Filter" :bordered="false">
					<a-row :gutter="16">
						<a-col :span="24" :md="8">
							<a-form-item label="Search by Name">
								<a-input
									v-model="nameSearch"
									placeholder="Enter graduate name..."
									@change="applyFilters"
									allowClear
								>
									<a-icon slot="prefix" type="search" />
								</a-input>
							</a-form-item>
						</a-col>
						<a-col :span="24" :md="8">
							<a-form-item label="Filter by Year">
								<a-select
									v-model="selectedYear"
									placeholder="Select graduation year"
									@change="applyFilters"
									allowClear
									style="width: 100%"
								>
									<a-select-option v-for="year in availableYears" :key="year" :value="year">
										{{ year }}
									</a-select-option>
								</a-select>
							</a-form-item>
						</a-col>
						<a-col :span="24" :md="8">
							<a-form-item label="Filter by Course">
								<a-select
									v-model="selectedCourse"
									placeholder="Select course"
									@change="applyFilters"
									allowClear
									style="width: 100%"
								>
									<a-select-option v-for="course in availableCourses" :key="course" :value="course">
										{{ course }}
									</a-select-option>
								</a-select>
							</a-form-item>
						</a-col>
					</a-row>
					<a-row>
						<a-col :span="24">
							<a-space>
								<a-button @click="clearFilters" type="default">
									<a-icon type="clear" />Clear Filters
								</a-button>
								<a-tag color="blue" v-if="filteredGraduates.length">
									{{ filteredGraduates.length }} of {{ allGraduates.length }} graduates shown
								</a-tag>
							</a-space>
						</a-col>
					</a-row>
				</a-card>
			</a-col>
		</a-row>

		<!-- Graduate List -->
		<a-row :gutter="24">
			<a-col :span="24">
				<a-card title="Graduate List" :bordered="false">
					<template #extra>
						<a-space>
							<a-select v-model="pageSize" @change="onPageSizeChange" style="width: 120px">
								<a-select-option :value="10">10 / page</a-select-option>
								<a-select-option :value="25">25 / page</a-select-option>
								<a-select-option :value="50">50 / page</a-select-option>
								<a-select-option :value="100">100 / page</a-select-option>
							</a-select>
						</a-space>
					</template>

					<a-table
						:dataSource="filteredGraduates"
						:columns="graduateColumns"
						:pagination="{
							current: currentPage,
							pageSize: pageSize,
							total: filteredGraduates.length,
							showSizeChanger: false,
							showQuickJumper: true,
							showTotal: (total, range) => `${range[0]}-${range[1]} of ${total} graduates`
						}"
						:loading="loading"
						:scroll="{ x: 1200 }"
						@change="onTableChange"
						bordered
					>
						<template slot="name" slot-scope="text, record">
							<div>
								<strong>{{ text }}</strong>
								<br>
								<small style="color: #666;">ID: {{ record.id }}</small>
							</div>
						</template>

						<template slot="gender" slot-scope="text">
							<a-tag :color="text === 'Male' ? 'blue' : 'pink'">
								<a-icon :type="text === 'Male' ? 'man' : 'woman'" />
								{{ text }}
							</a-tag>
						</template>

						<template slot="achievement" slot-scope="text">
							<a-tag 
								:color="getAchievementColor(text)"
								v-if="text && text !== 'Regular Graduate'"
							>
								{{ text }}
							</a-tag>
							<span v-else>{{ text || 'Regular Graduate' }}</span>
						</template>

						<template slot="action" slot-scope="text, record">
							<a-button-group size="small">
								<a-button type="link" @click="viewGraduateDetails(record)">
									<a-icon type="eye" />View
								</a-button>
							</a-button-group>
						</template>
					</a-table>
				</a-card>
			</a-col>
		</a-row>

		<!-- Graduate Details Modal -->
		<a-modal
			v-model="detailsModal"
			title="Graduate Details"
			:width="600"
			:footer="null"
		>
			<div v-if="selectedGraduate">
				<a-descriptions :column="2" bordered>
					<a-descriptions-item label="Full Name">
						{{ selectedGraduate.name }}
					</a-descriptions-item>
					<a-descriptions-item label="Gender">
						<a-tag :color="selectedGraduate.gender === 'Male' ? 'blue' : 'pink'">
							<a-icon :type="selectedGraduate.gender === 'Male' ? 'man' : 'woman'" />
							{{ selectedGraduate.gender }}
						</a-tag>
					</a-descriptions-item>
					<a-descriptions-item label="Address">
						{{ selectedGraduate.address || 'N/A' }}
					</a-descriptions-item>
					<a-descriptions-item label="Batch">
						{{ selectedGraduate.batch || 'N/A' }}
					</a-descriptions-item>
					<a-descriptions-item label="Year Graduated">
						{{ selectedGraduate.yearGraduated }}
					</a-descriptions-item>
					<a-descriptions-item label="Section">
						{{ selectedGraduate.section || 'N/A' }}
					</a-descriptions-item>
					<a-descriptions-item label="Course" :span="2">
						{{ selectedGraduate.course }}
					</a-descriptions-item>
					<a-descriptions-item label="Major" :span="2">
						{{ selectedGraduate.major || 'N/A' }}
					</a-descriptions-item>
					<a-descriptions-item label="Achievement" :span="2">
						<a-tag 
							:color="getAchievementColor(selectedGraduate.achievement)"
							v-if="selectedGraduate.achievement && selectedGraduate.achievement !== 'Regular Graduate'"
						>
							{{ selectedGraduate.achievement }}
						</a-tag>
						<span v-else>{{ selectedGraduate.achievement || 'Regular Graduate' }}</span>
					</a-descriptions-item>
					<a-descriptions-item label="Created At" :span="2">
						{{ formatDate(selectedGraduate.created_at) }}
					</a-descriptions-item>
				</a-descriptions>
			</div>
		</a-modal>
	</div>
</template>

<script>
export default {
	name: 'Dashboard',
	data() {
		return {
			loading: false,
			exporting: false,
			
			// Graduate data
			allGraduates: [],
			filteredGraduates: [],
			
			// Filter options
			nameSearch: '',
			selectedYear: null,
			selectedCourse: null,
			availableYears: [],
			availableCourses: [],
			
			// Pagination
			currentPage: 1,
			pageSize: 25,
			
			// Modal
			detailsModal: false,
			selectedGraduate: null,
			
			// Table columns
			graduateColumns: [
				{
					title: 'Name',
					dataIndex: 'name',
					key: 'name',
					scopedSlots: { customRender: 'name' },
					sorter: (a, b) => a.name.localeCompare(b.name),
					width: 200,
				},
				{
					title: 'Gender',
					dataIndex: 'gender',
					key: 'gender',
					scopedSlots: { customRender: 'gender' },
					width: 100,
					filters: [
						{ text: 'Male', value: 'Male' },
						{ text: 'Female', value: 'Female' },
					],
					onFilter: (value, record) => record.gender === value,
				},
				{
					title: 'Year Graduated',
					dataIndex: 'yearGraduated',
					key: 'yearGraduated',
					sorter: (a, b) => parseInt(a.yearGraduated) - parseInt(b.yearGraduated),
					width: 120,
				},
				{
					title: 'Course',
					dataIndex: 'course',
					key: 'course',
					ellipsis: true,
					width: 250,
				},
				{
					title: 'Major',
					dataIndex: 'major',
					key: 'major',
					ellipsis: true,
					width: 200,
				},
				{
					title: 'Achievement',
					dataIndex: 'achievement',
					key: 'achievement',
					scopedSlots: { customRender: 'achievement' },
					width: 150,
				},
				{
					title: 'Action',
					key: 'action',
					scopedSlots: { customRender: 'action' },
					width: 100,
					fixed: 'right',
				},
			]
		}
	},
	created() {
		this.loadGraduateData();
	},
	methods: {
		async loadGraduateData() {
			this.loading = true;
			try {
				const response = await this.$api.get('graduates/get/list');
				if (!response.data.error) {
					this.allGraduates = response.data.list;
					this.filteredGraduates = [...this.allGraduates];
					this.extractFilterOptions();
				} else {
					this.$notification.error({
						message: 'Error',
						description: 'Failed to load graduate data'
					});
				}
			} catch (error) {
				console.error('Error loading graduate data:', error);
				this.$notification.error({
					message: 'Error',
					description: 'Failed to load graduate data'
				});
			} finally {
				this.loading = false;
			}
		},

		extractFilterOptions() {
			// Extract unique years and courses for filter options
			const years = [...new Set(this.allGraduates.map(g => g.yearGraduated))].filter(year => year);
			const courses = [...new Set(this.allGraduates.map(g => g.course))].filter(course => course);
			
			this.availableYears = years.sort((a, b) => parseInt(b) - parseInt(a));
			this.availableCourses = courses.sort();
		},

		applyFilters() {
			let filtered = [...this.allGraduates];

			// Name search filter
			if (this.nameSearch) {
				filtered = filtered.filter(graduate =>
					graduate.name.toLowerCase().includes(this.nameSearch.toLowerCase())
				);
			}

			// Year filter
			if (this.selectedYear) {
				filtered = filtered.filter(graduate => graduate.yearGraduated === this.selectedYear);
			}

			// Course filter
			if (this.selectedCourse) {
				filtered = filtered.filter(graduate => graduate.course === this.selectedCourse);
			}

			this.filteredGraduates = filtered;
			this.currentPage = 1; // Reset to first page when filtering
		},

		clearFilters() {
			this.nameSearch = '';
			this.selectedYear = null;
			this.selectedCourse = null;
			this.filteredGraduates = [...this.allGraduates];
			this.currentPage = 1;
		},

		refreshData() {
			this.loadGraduateData();
		},

		onTableChange(pagination, filters, sorter) {
			this.currentPage = pagination.current;
		},

		onPageSizeChange(size) {
			this.pageSize = size;
			this.currentPage = 1;
		},

		viewGraduateDetails(graduate) {
			this.selectedGraduate = graduate;
			this.detailsModal = true;
		},

		getAchievementColor(achievement) {
			const colorMap = {
				'Summa Cum Laude': 'gold',
				'Magna Cum Laude': 'orange',
				'Cum Laude': 'green',
				'Regular Graduate': 'default'
			};
			return colorMap[achievement] || 'default';
		},

		formatDate(dateString) {
			if (!dateString) return 'N/A';
			try {
				return new Date(dateString).toLocaleDateString();
			} catch (error) {
				return dateString;
			}
		},

		async exportGraduateList() {
			this.exporting = true;
			try {
				// Create CSV content
				const headers = ['Name', 'Gender', 'Year Graduated', 'Course', 'Major', 'Achievement', 'Address', 'Batch', 'Section'];
				const csvContent = [
					headers.join(','),
					...this.filteredGraduates.map(graduate => [
						`"${graduate.name || ''}"`,
						`"${graduate.gender || ''}"`,
						`"${graduate.yearGraduated || ''}"`,
						`"${graduate.course || ''}"`,
						`"${graduate.major || ''}"`,
						`"${graduate.achievement || ''}"`,
						`"${graduate.address || ''}"`,
						`"${graduate.batch || ''}"`,
						`"${graduate.section || ''}"`
					].join(','))
				].join('\n');

				// Create download link
				const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
				const link = document.createElement('a');
				const url = URL.createObjectURL(blob);
				link.setAttribute('href', url);
				link.setAttribute('download', `graduate_list_${new Date().toISOString().split('T')[0]}.csv`);
				link.style.visibility = 'hidden';
				document.body.appendChild(link);
				link.click();
				document.body.removeChild(link);

				this.$notification.success({
					message: 'Success',
					description: 'Graduate list exported successfully'
				});
			} catch (error) {
				console.error('Export error:', error);
				this.$notification.error({
					message: 'Error',
					description: 'Failed to export graduate list'
				});
			} finally {
				this.exporting = false;
			}
		}
	}
}
</script>

<style lang="scss" scoped>
.dashboard-container {
	.mb-24 {
		margin-bottom: 24px;
	}
	
	.profile-nav-bg {
		height: 120px;
		border-radius: 8px;
		margin-bottom: 24px;
		background-size: cover;
		background-position: center;
	}
	
	.card-profile-head {
		margin-bottom: 24px;
		
		.col-info {
			display: flex;
			align-items: center;
			gap: 16px;
			
			.avatar-info {
				h4 {
					margin: 0;
					color: #1e9ecf;
				}
				
				p {
					margin: 0;
					color: #666;
					font-size: 14px;
				}
			}
		}
	}
}
</style>