<template>
    <div class="dashboard-container">
        
        <!-- Header -->
        <a-card :bordered="false" class="header-solid mb-24" :bodyStyle="{padding: '16px 24px'}">
            <a-row type="flex" justify="space-between" align="middle">
                <a-col>
                    <h4 class="font-semibold m-0" style="font-size: 18px;">Graduate Record List</h4>
                </a-col>
                <a-col>
                    <a-space>
                        <template v-if="selectedYear">
                            <span class="text-muted">Rows per page:</span>
                            <!-- FIX: Bind to pagination.pageSize -->
                            <a-select v-model="pagination.pageSize" @change="onPageSizeChange" style="width: 110px">
                                <a-select-option :value="10">10 / page</a-select-option>
                                <a-select-option :value="25">25 / page</a-select-option>
                                <a-select-option :value="50">50 / page</a-select-option>
                            </a-select>
                        </template>
                        
                        <pdf-export 
                            :graduates="filteredGraduates" 
                            :year="selectedYear"
                            :disabled="loading || filteredGraduates.length === 0"
                        >
                            Export {{ selectedYear ? 'Batch ' + selectedYear : 'All Data' }}
                        </pdf-export>
                    </a-space>
                </a-col>
            </a-row>
        </a-card>

        <!-- Year Cards -->
        <div class="mb-24">
            <a-row :gutter="[24, 24]" style="margin-top: 15px;">
                <a-col 
                    v-for="stat in yearStatistics" 
                    :key="stat.year" 
                    :xs="24" :sm="12" :md="8" :lg="6"
                >
                    <card-dashboard
                        :year="stat.year"
                        :count="stat.count"
                        :active="selectedYear === stat.year"
                        @click="filterByYear"
                    />
                </a-col>
            </a-row>
        </div>

        <!-- Filters -->
        <a-card :bordered="false" class="mb-24" v-if="selectedYear">
            <a-row :gutter="16" align="middle">
                <a-col :span="12" :md="8">
                    <a-input
                        v-model="nameSearch"
                        placeholder="Search by name..."
                        @change="applyFilters"
                        allowClear
                        size="large"
                    >
                        <a-icon slot="prefix" type="search" />
                    </a-input>
                </a-col>
                <a-col :span="12" :md="8">
                    <a-button @click="filterByYear(selectedYear)" icon="reload" block size="large">
                        Reset Search
                    </a-button>
                </a-col>
            </a-row>
        </a-card>

        <!-- Table -->
        <transition name="fade" mode="out-in">
            <a-card :bordered="false" class="header-solid table-card" v-if="selectedYear" key="table">
                <div slot="title" style="display: flex; align-items: center;">
                    <a-avatar shape="square" style="background-color: #f6ffed; color: #52c41a" icon="table" class="mr-2" />
                    <div>
                        <h5 class="font-semibold m-0">Batch {{ selectedYear }} Records</h5>
                        <small class="text-muted">Total: {{ filteredGraduates.length }} students</small>
                    </div>
                </div>
                
                <!-- FIX: Use pagination object and handleTableChange -->
                <a-table
                    :dataSource="filteredGraduates"
                    :columns="graduateColumns"
                    :pagination="pagination"
                    @change="handleTableChange"
                    :loading="loading"
                    :scroll="{ x: 1000 }"
                    rowKey="id"
                    class="ant-table-striped"
                >
                    <template slot="name" slot-scope="text, record">
                        <div class="d-flex align-items-center">
                            <a-avatar :size="40" class="mr-3" :style="{ backgroundColor: getAvatarColor(text), verticalAlign: 'middle' }">
                                {{ text.charAt(0).toUpperCase() }}
                            </a-avatar>
                            <div style="display: inline-block; vertical-align: middle;">
                                <h6 class="m-0 text-dark font-semibold">{{ text }}</h6>
                                <p class="m-0 text-muted" style="font-size: 12px;">ID: {{ record.id }}</p>
                            </div>
                        </div>
                    </template>

                    <template slot="gender" slot-scope="text">
                        <span>
                            <a-icon :type="text === 'Male' ? 'man' : 'woman'" :style="{ color: text === 'Male' ? '#1890ff' : '#eb2f96', marginRight: '5px' }" />
                            {{ text }}
                        </span>
                    </template>

                    <template slot="course" slot-scope="text">
                        <a-tooltip :title="text">
                            <span><a-icon type="book" theme="twoTone" class="mr-2" /> {{ text }}</span>
                        </a-tooltip>
                    </template>

                    <template slot="achievement" slot-scope="text">
                        <div v-if="text">
                             <a-tag :color="getAchievementColor(text)" style="border-radius: 15px; padding: 2px 12px;">
                                <a-icon v-if="text !== 'Regular Graduate'" type="trophy" class="mr-1" /> {{ text }}
                             </a-tag>
                        </div>
                        <span v-else class="text-muted" style="opacity: 0.6;">—</span>
                    </template>

                    <template slot="action" slot-scope="text, record">
                        <div class="action-buttons">
                            <a-tooltip title="View Details">
                                <a-button shape="circle" icon="eye" size="small" @click="viewGraduateDetails(record)" />
                            </a-tooltip>
                            <a-tooltip v-if="isAdmin" title="Edit Record">
                                <a-button type="primary" shape="circle" icon="edit" size="small" class="ml-2" @click="onEdit(record)" />
                            </a-tooltip>
                            <a-popconfirm
                                v-if="isAdmin"
                                title="Are you sure you want to delete this record?"
                                ok-text="Yes"
                                cancel-text="No"
                                @confirm="onDelete(record)"
                            >
                                <a-tooltip title="Delete Record">
                                    <a-button type="danger" shape="circle" icon="delete" size="small" class="ml-2" />
                                </a-tooltip>
                            </a-popconfirm>
                        </div>
                    </template>
                </a-table>
            </a-card>

            <div v-else class="empty-state-container" key="empty">
                <a-empty 
                    image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                    :image-style="{ height: '150px' }"
                >
                    <span slot="description" style="font-size: 16px; color: #8c8c8c;">
                        Select a <strong>Year Folder</strong> above to open the records.
                    </span>
                </a-empty>
            </div>
        </transition>

        <!-- View Modal -->
        <a-modal
            v-model="detailsModal"
            title="Graduate Details"
            :width="600"
            :footer="null"
            centered
        >
            <div v-if="selectedGraduate">
                <a-descriptions :column="1" bordered size="small">
                    <a-descriptions-item label="Name"><strong>{{ selectedGraduate.name }}</strong></a-descriptions-item>
                    <a-descriptions-item label="Student ID">{{ selectedGraduate.studentId }}</a-descriptions-item>
                    <a-descriptions-item label="Year"><a-tag color="green">{{ selectedGraduate.yearGraduated }}</a-tag></a-descriptions-item>
                    <a-descriptions-item label="Course">{{ selectedGraduate.course }}</a-descriptions-item>
                    <a-descriptions-item label="Achievement">{{ selectedGraduate.achievement || 'Regular Graduate' }}</a-descriptions-item>
                </a-descriptions>
            </div>
        </a-modal>

        <!-- Edit Modal -->
        <a-modal
            v-model="editModalVisible"
            title="Edit Graduate Record"
            ok-text="Save Changes"
            @ok="updateGraduate"
            :confirmLoading="loading"
            centered
        >
            <a-form layout="vertical" v-if="editingGraduate">
                <a-form-item label="Full Name">
                    <a-input v-model="editingGraduate.name" />
                </a-form-item>
                <a-row :gutter="16">
                    <a-col :span="12">
                        <a-form-item label="Year Graduated">
                            <a-input v-model="editingGraduate.yearGraduated" />
                        </a-form-item>
                    </a-col>
                    <a-col :span="12">
                         <a-form-item label="Gender">
                            <a-select v-model="editingGraduate.gender">
                                <a-select-option value="Male">Male</a-select-option>
                                <a-select-option value="Female">Female</a-select-option>
                            </a-select>
                        </a-form-item>
                    </a-col>
                </a-row>
                <a-form-item label="Course">
                     <a-input v-model="editingGraduate.course" />
                </a-form-item>
                <a-form-item label="Achievement">
                    <a-select v-model="editingGraduate.achievement" allowClear>
                        <a-select-option value="Summa Cum Laude">Summa Cum Laude</a-select-option>
                        <a-select-option value="Magna Cum Laude">Magna Cum Laude</a-select-option>
                        <a-select-option value="Cum Laude">Cum Laude</a-select-option>
                        <a-select-option value="Regular Graduate">Regular Graduate</a-select-option>
                    </a-select>
                </a-form-item>
            </a-form>
        </a-modal>
    </div>
</template>

<script>
import {jwtDecode} from 'jwt-decode';
import PdfExport from '@/components/Export/PdfExport.vue';
import CardDashboard from '@/components/Cards/CardDashboard.vue';

export default {
    name: 'GraduateDashboard',
    components: {
        PdfExport,
        CardDashboard
    },
    data() {
        return {
            loading: false,
            allGraduates: [],
            filteredGraduates: [],
            nameSearch: '',
            selectedYear: null,
            
            // FIX: Centralized pagination object
            pagination: {
                current: 1,
                pageSize: 25,
                showSizeChanger: false, // We control this with the select above
                showQuickJumper: true,
                showTotal: (total, range) => `${range[0]}-${range[1]} of ${total}`
            },
            
            // View Modal
            detailsModal: false,
            selectedGraduate: null,

            // Edit Modal
            editModalVisible: false,
            editingGraduate: null,
            
            graduateColumns: [
                { title: 'STUDENT', dataIndex: 'name', key: 'name', scopedSlots: { customRender: 'name' }, sorter: (a, b) => a.name.localeCompare(b.name), width: 280 },
                { title: 'GENDER', dataIndex: 'gender', key: 'gender', width: 120, scopedSlots: { customRender: 'gender' } },
                { title: 'COURSE', dataIndex: 'course', key: 'course', ellipsis: true, scopedSlots: { customRender: 'course' } },
                { title: 'ACHIEVEMENT', dataIndex: 'achievement', key: 'achievement', scopedSlots: { customRender: 'achievement' }, width: 200 },
                { title: 'ACTION', key: 'action', scopedSlots: { customRender: 'action' }, width: 150, align: 'center', fixed: 'right' },
            ]
        }
    },
    computed: {
        user: function () {
            let raw = localStorage.getItem('userToken')
            if (!raw) return null
            let tokenString = raw
            try {
                const parsed = JSON.parse(raw)
                if (parsed && parsed.value) tokenString = parsed.value
            } catch (e) { }
            try {
                return jwtDecode(tokenString)
            } catch (e) {
                console.error('Failed to decode JWT token', e)
                return null
            }
        },
        isAdmin: function () {
            return this.user && this.user.aud === 'admin'
        },
        yearStatistics() {
            const stats = {};
            this.allGraduates.forEach(g => {
                if (g.yearGraduated) {
                    stats[g.yearGraduated] = (stats[g.yearGraduated] || 0) + 1;
                }
            });
            return Object.entries(stats)
                .map(([year, count]) => ({ year, count }))
                .sort((a, b) => parseInt(b.year) - parseInt(a.year));
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
                }
            } catch (error) {
                console.error("Failed to load data", error);
                this.$message.error("Failed to load records");
            } finally {
                this.loading = false;
            }
        },

        filterByYear(year) {
            this.nameSearch = '';
            if (this.selectedYear === year) {
                this.selectedYear = null;
            } else {
                this.selectedYear = year;
            }
            this.applyFilters();
        },

        applyFilters() {
            let filtered = [...this.allGraduates];
            if (this.selectedYear) {
                filtered = filtered.filter(graduate => graduate.yearGraduated === this.selectedYear);
            }
            if (this.nameSearch) {
                filtered = filtered.filter(graduate =>
                    graduate.name.toLowerCase().includes(this.nameSearch.toLowerCase())
                );
            }
            this.filteredGraduates = filtered;
            
            // FIX: Reset pagination securely
            const pager = { ...this.pagination };
            pager.current = 1;
            this.pagination = pager;
        },

        // FIX: Handle Table Change
        handleTableChange(pagination, filters, sorter) {
            const pager = { ...this.pagination };
            pager.current = pagination.current;
            pager.pageSize = pagination.pageSize;
            this.pagination = pager;
        },

        // FIX: Handle Page Size Change
        onPageSizeChange(size) {
             const pager = { ...this.pagination };
             pager.pageSize = size;
             pager.current = 1;
             this.pagination = pager;
        },

        // View
        viewGraduateDetails(graduate) {
            console.log(graduate);
            this.selectedGraduate = graduate;
            this.detailsModal = true;
        },

        // Edit - Open
        onEdit(record) {
            this.editingGraduate = { ...record };
            this.editModalVisible = true;
        },

        // Edit - Save (Backend Connected)
        async updateGraduate() {
            if (!this.editingGraduate) return;
            this.loading = true;

            try {
                const response = await this.$api.post('graduates/update', this.editingGraduate);
                
                // Safe check for response
                if (response && response.data && !response.data.error) {
                    this.$message.success('Graduate record updated successfully');
                    this.editModalVisible = false;
                    this.loadGraduateData(); 
                } else {
                    const errorMsg = response?.data?.message || 'Failed to update record';
                    this.$message.error(errorMsg);
                }
            } catch (error) {
                console.error("Update error", error);
                const serverMsg = error.response?.data?.message || 'Server error during update';
                this.$message.error(serverMsg);
            } finally {
                this.loading = false;
            }
        },

        // Delete (Backend Connected)
        async onDelete(record) {
            this.loading = true;
            try {
                const response = await this.$api.post('graduates/delete', { id: record.id });

                if (response && response.data && !response.data.error) {
                    this.$message.success('Record deleted successfully');
                    this.loadGraduateData(); 
                } else {
                    const errorMsg = response?.data?.message || 'Failed to delete record';
                    this.$message.error(errorMsg);
                }
            } catch (error) {
                console.error("Delete error", error);
                const serverMsg = error.response?.data?.message || 'Server error during deletion';
                this.$message.error(serverMsg);
            } finally {
                this.loading = false;
            }
        },

        // Helpers
        getAchievementColor(achievement) {
            const colorMap = { 
                'Summa Cum Laude': 'gold', 
                'Magna Cum Laude': 'orange', 
                'Cum Laude': 'green', 
                'Regular Graduate': 'blue'
            };
            return colorMap[achievement] || 'default';
        },
        getAvatarColor(name) {
            const colors = ['#f56a00', '#7265e6', '#ffbf00', '#00a2ae', '#87d068', '#1890ff', '#eb2f96'];
            let hash = 0;
            if(name) {
                for (let i = 0; i < name.length; i++) {
                    hash = name.charCodeAt(i) + ((hash << 5) - hash);
                }
            }
            return colors[Math.abs(hash) % colors.length];
        }
    }
}
</script>

<style lang="scss" scoped>
.dashboard-container {
    .empty-state-container {
        padding: 60px 0;
        text-align: center;
        background: #fff;
        border-radius: 8px;
        border: 2px dashed #f0f0f0;
    }
    .header-solid {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
    }
    .table-card { border-top: 3px solid #52c41a; }
    .mb-24 { margin-bottom: 24px; }
    .mr-2 { margin-right: 8px; }
    .mr-3 { margin-right: 12px; }
    .ml-2 { margin-left: 8px; }
    .text-muted { color: #8c8c8c; }
    .text-dark { color: #262626; }
    
    .action-buttons {
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    ::v-deep .ant-table-thead > tr > th {
        background-color: #fafafa;
        font-weight: 700;
        color: #595959;
        text-transform: uppercase;
        font-size: 11px;
        letter-spacing: 0.5px;
    }
    ::v-deep .ant-table-row:hover {
        background-color: #f6ffed !important;
        cursor: pointer;
    }
    .d-flex { display: flex; align-items: center; }
    .fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
    .fade-enter, .fade-leave-to { opacity: 0; }
}
</style>