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
        <!-- School/Course/Major/Year Navigation with Breadcrumbs and Back Button in Card Header -->
        <a-card class="mb-24" :bordered="false" :bodyStyle="{padding: '18px 24px 10px 24px'}">
            <div style="display: flex; align-items: center; margin-bottom: 10px;">
                <a-button v-if="selectedSchool || selectedCourse || selectedMajor || selectedYear" type="dashed" icon="arrow-left" @click="goBackNav">
                    Back
                </a-button>
                <a-breadcrumb style="margin-left: 20px;">
                    <a-breadcrumb-item v-if="selectedSchool" @click.native="goBackTo('school')" style="cursor:pointer;">
                        <img v-if="selectedSchoolLogo" :src="selectedSchoolLogo" alt="logo" style="height:20px;vertical-align:middle;margin-right:6px;" />
                        {{ selectedSchool }}
                    </a-breadcrumb-item>
                    <a-breadcrumb-item v-if="selectedCourse" @click.native="goBackTo('course')" style="cursor:pointer;">
                        {{ selectedCourse }}
                    </a-breadcrumb-item>
                    <a-breadcrumb-item v-if="selectedMajor" @click.native="goBackTo('major')" style="cursor:pointer;">
                        {{ selectedMajor }}
                    </a-breadcrumb-item>
                    <a-breadcrumb-item v-if="selectedYear">
                        {{ selectedYear }}
                    </a-breadcrumb-item>
                </a-breadcrumb>
            </div>
            <a-row :gutter="[16, 16]" style="margin-top: 10px;">
                <!-- Step 1: School Cards -->
                <template v-if="!selectedSchool">
                    <a-col v-for="school in schoolList" :key="school.name" :xs="12" :sm="8" :md="6" :lg="4">
                        <div class="folder-card small-folder" @click="selectSchool(school)">
                            <img v-if="school.logo" :src="school.logo" alt="logo" style="height:32px;display:block;margin:0 auto 6px auto;" />
                            <div class="folder-label">{{ school.name }}</div>
                        </div>
                    </a-col>
                </template>
                <!-- Step 2: Course Cards -->
                <template v-else-if="selectedSchool && !selectedCourse">
                    <a-col v-for="course in courseList[selectedSchool]" :key="course" :xs="12" :sm="8" :md="6" :lg="4">
                        <div class="folder-card small-folder" @click="selectCourse(course)">
                            <a-icon type="folder" style="font-size:28px;color:#1890ff;" />
                            <div class="folder-label">{{ course }}</div>
                        </div>
                    </a-col>
                </template>
            </a-row>
        </a-card>

        <!-- School Display Card below analytics cards -->
        <a-row class="mb-24">
            <a-col :xs="24">
                <a-card class="school-display-card" :bordered="false" v-if="selectedSchool">
                    <div style="display:flex;align-items:center;gap:16px;">
                        <img v-if="selectedSchoolLogo" :src="selectedSchoolLogo" alt="logo" style="height:40px;" />
                        <div>
                            <h3 style="margin:0;">{{ selectedSchool }}</h3>
                            <div class="text-muted">School Display Card (details/description can go here)</div>
                        </div>
                    </div>
                </a-card>
            </a-col>
        </a-row>
    
        <!-- Filters -->
        <dashboard-filters
            v-if="selectedCourse"
            :course-options="courseList[selectedSchool] || []"
            :class-year-options="yearOptions"
            :loading="loading"
            @filters-changed="onDashboardFiltersChanged"
        />
    

        <!-- Table -->
        <transition name="fade" mode="out-in">
            <a-card :bordered="false" class="header-solid table-card mb-24 mt-24 " v-if="selectedCourse || selectedSchool" key="table">
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

                    <template slot="course" slot-scope="text, record">
                        <a-tooltip :title="getCourseName(text)">
                            <span><a-icon type="book" theme="twoTone" class="mr-2" /> {{ getCourseName(text) }}</span>
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

            <div v-if="!loading && (selectedCourse || selectedSchool) && filteredGraduates.length === 0" class="empty-state-container" key="empty">
                <a-empty 
                    image="https://gw.alipayobjects.com/zos/antfincdn/ZHrcdLPrvN/empty.svg"
                    :image-style="{ height: '150px' }"
                >
                    <span slot="description" style="font-size: 16px; color: #8c8c8c;">
                        No graduate records found for this year.
                    </span>
                </a-empty>
            </div>
        </transition>

        <!-- Analytics Summary Section (below folder navigation) -->
        <a-row :gutter="[24, 24]" class="mb-24 mt-24">
            <a-col :xs="24" :sm="12" :md="8" v-for="stat in summaryStats" :key="stat.key">
                <a-card class="stat-card" :bordered="false">
                    <a-statistic
                        :title="stat.title"
                        :value="stat.value"
                        :precision="stat.precision || 0"
                        :suffix="stat.suffix"
                        :value-style="{ color: stat.color, fontWeight: '600', fontSize: '28px' }"
                    >
                        <template #prefix>
                            <div class="stat-icon-wrapper" :style="{ backgroundColor: stat.color + '15' }">
                                <a-icon :type="stat.icon" :style="{ color: stat.color, fontSize: '24px' }" />
                            </div>
                        </template>
                    </a-statistic>
                    <div class="stat-trend" v-if="stat.trend !== undefined">
                        <a-tag :color="stat.trend > 0 ? 'green' : 'red'" class="trend-tag">
                            <a-icon :type="stat.trend > 0 ? 'arrow-up' : 'arrow-down'" />
                            {{ Math.abs(stat.trend) }}% vs last period
                        </a-tag>
                    </div>
                </a-card>
            </a-col>
        </a-row>

        
        <a-row :gutter="[24, 24]" class="mb-24">
            <a-col :xs="24" :lg="14">
                <a-card class="chart-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="cluster" /> Courses & Departments Overview</span>
                    </template>
                    <template #extra>
                        <a-radio-group v-model="departmentsView" @change="updateDepartmentsView" size="small">
                            <a-radio-button value="overview">Overview</a-radio-button>
                            <a-radio-button value="trends">Trends</a-radio-button>
                            <a-radio-button value="comparison">Comparison</a-radio-button>
                        </a-radio-group>
                    </template>
                    <div v-if="departmentsView === 'overview'" class="chart-container">
                        <canvas id="departmentsChart" ref="departmentsChart"></canvas>
                    </div>
                    <div v-else-if="departmentsView === 'trends'" class="chart-container">
                        <canvas id="departmentTrendsChart" ref="departmentTrendsChart"></canvas>
                    </div>
                    <div v-else>
                        <div class="comparison-grid">
                            <div v-for="dept in departmentComparison" :key="dept.name" class="dept-comparison-item">
                                <div class="dept-name">{{ dept.name }}</div>
                                <div class="dept-stats">
                                    <a-statistic 
                                        title="Total Graduates"
                                        :value="dept.totalGraduates"
                                        :value-style="{ fontSize: '14px' }"
                                    />
                                    <a-statistic 
                                        title="Avg per Year"
                                        :value="dept.avgPerYear"
                                        :precision="1"
                                        :value-style="{ fontSize: '14px' }"
                                    />
                                    <a-statistic 
                                        title="Growth Rate"
                                        :value="dept.growthRate"
                                        :precision="1"
                                        suffix="%"
                                        :value-style="{ 
                                            fontSize: '14px',
                                            color: dept.growthRate > 0 ? '#52c41a' : '#ff4d4f'
                                        }"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </a-card>
            </a-col>
            <a-col :xs="24" :lg="10">
                <a-card class="chart-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="pie-chart" /> Gender Distribution Per Year</span>
                    </template>
                    <template #extra>
                        <a-select v-model="selectedGenderYear" @change="updateGenderChart" size="small" style="width: 120px;">
                            <a-select-option v-for="year in Object.keys(analyticsData.yearlyGender).sort((a,b)=>b-a)" :key="year" :value="year">{{ year }}</a-select-option>
                        </a-select>
                    </template>
                    <div class="chart-container">
                        <canvas id="genderChart" ref="genderChart"></canvas>
                    </div>
                </a-card>
            </a-col>
            
        </a-row>
        <a-row :gutter="[24, 24]" class="mb-24">
            <a-col :xs="24" :lg="24">
                <a-card class="chart-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="line-chart" /> Graduates per Year</span>
                    </template>
                    <template #extra>
                        <a-radio-group v-model="graduatesChartType" @change="updateGraduatesChart" size="small">
                            <a-radio-button value="line">Line</a-radio-button>
                            <a-radio-button value="bar">Bar</a-radio-button>
                        </a-radio-group>
                    </template>
                    <div class="chart-container">
                        <canvas id="graduatesChart" ref="graduatesChart"></canvas>
                    </div>
                </a-card>

                <a-card class="chart-card" :style="{marginTop: '20px'}" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="bar-chart" /> Course Graduation Metrics</span>
                    </template>
                    <template #extra>
                        <a-select v-model="courseMetricsView" @change="updateCourseMetrics" style="width: 150px" size="small">
                            <a-select-option value="chart">Chart View</a-select-option>
                            <a-select-option value="table">Table View</a-select-option>
                        </a-select>
                    </template>
                    <div v-if="courseMetricsView === 'chart'" class="chart-container">
                        <canvas id="courseChart" ref="courseChart"></canvas>
                    </div>
                    <div v-else>
                        <a-table 
                            :columns="courseMetricsColumns"
                            :data-source="courseMetricsData"
                            :pagination="{ pageSize: 5 }"
                            size="small"
                        >
                            <template slot="completion_rate" slot-scope="text">
                                <a-progress 
                                    :percent="text" 
                                    :stroke-color="text >= 90 ? '#52c41a' : text >= 70 ? '#faad14' : '#ff4d4f'"
                                    size="small"
                                />
                            </template>
                        </a-table>
                    </div>
                </a-card>
            </a-col>
        </a-row>

        <a-row :gutter="[24, 24]">
            <a-col :xs="24" :lg="14">
                <a-card class="yoy-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="calendar" /> Year-over-Year Analysis</span>
                    </template>
                    <div class="yoy-analysis">
                        <a-row :gutter="24">
                            <a-col :xs="24" :md="8" v-for="year in yearAnalysis" :key="year.year">
                                <div class="yoy-item">
                                    <div class="yoy-header">
                                        <h4>{{ year.year }}</h4>
                                        <a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">
                                            <a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />
                                            {{ year.changePercent }}%
                                        </a-tag>
                                    </div>
                                    <div class="yoy-metrics">
                                        <div class="metric">
                                            <span class="metric-label">Total Graduates</span>
                                            <span class="metric-value">{{ year.graduates }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">Courses Active</span>
                                            <span class="metric-value">{{ year.activeCourses }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">Male/Female</span>
                                            <span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>
                                        </div>
                                        <div class="metric">
                                            <span class="metric-label">Top Course</span>
                                            <span class="metric-value" style=" max-width: 270px; display: inline-block;">{{ year.topCourse }}</span>
                                        </div>
                                    </div>
                                </div>
                            </a-col>
                        </a-row>
                    </div>
                </a-card>
            </a-col>
            <a-col :xs="24" :lg="10">
                <a-card class="achievement-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="trophy" /> Achievements Distribution Per Year</span>
                    </template>
                    <template #extra>
                        <a-select v-model="selectedAchievementYear" size="small" style="width: 120px;">
                            <a-select-option v-for="year in Object.keys(analyticsData.yearlyGender).sort((a,b)=>b-a)" :key="year" :value="year">{{ year }}</a-select-option>
                        </a-select>
                    </template>
                    <div class="achievements-list">
                        <div v-for="achievement in achievementsData" :key="achievement.type" class="achievement-item">
                            <div class="achievement-header">
                                <span class="achievement-title">
                                    <a-icon type="star" :style="{ color: achievement.color }" />
                                    {{ achievement.title }}
                                </span>
                                <a-badge :count="achievement.count" :number-style="{ backgroundColor: achievement.color }" />
                            </div>
                            <a-progress 
                                :percent="achievement.percentage" 
                                :stroke-color="achievement.color"
                                :stroke-width="8"
                                :show-info="true"
                            />
                        </div>
                    </div>
                </a-card>
            </a-col>
        </a-row>
        
        

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
import DashboardFilters from '@/components/Filters/DashboardFilters.vue';
import Chart from 'chart.js/auto';

export default {
    name: 'GraduateDashboard',
    components: {
        PdfExport,
        CardDashboard,
        DashboardFilters
    },
    data() {
        return {
            // Color mapping for each school (should match logos and branding)
            schoolColors: {
                'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES': '#228B22', // dark green
                'SCHOOL OF ARTS AND SCIENCES': '#ff69b4', // pink
                'SCHOOL OF EDUCATION': '#0033cc', // blue
                'SCHOOL OF ENGINEERING': '#800000', // maroon
                'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES': '#39FF14', // yellow green
                'SCHOOL OF INDUSTRIAL TECHNOLOGY': '#000000', // black
                'SCHOOL OF INFORMATION TECHNOLOGY': '#ff6600', // orange
                'COLLEGE OF LAW': '#800080', // violet
                'SCHOOL OF ACCOUNTANCY AND BUSINESS MANAGEMENT': '#ffe600', // yellow
                'SCHOOL OF GRADUATE STUDIES': '#00e6ff' // sky blue
            },
            dashboardFilters: {
                course: null,
                classYear: null,
                term: null
            },
            selectedAchievementYear: null, // for year selection in achievements
            loading: false,
            allGraduates: [],
            filteredGraduates: [],
            nameSearch: '',
            selectedSchool: null,
            selectedSchoolLogo: null,
            selectedCourse: null,
            selectedMajor: null,
            selectedYear: null,
            exporting: false,
            graduatesChartType: 'line',
            genderChartType: 'doughnut',
            selectedGenderYear: null, // for year selection in gender chart
            courseMetricsView: 'chart',
            departmentsView: 'overview',
            graduatesChartInstance: null,
            genderChartInstance: null,
            courseChartInstance: null,
            departmentsChartInstance: null,
            departmentTrendsChartInstance: null,
            summaryStats: [],
            analyticsData: {
                graduatesPerYear: {},
                yearlyGender: {}, // Added to store calculated year-specific gender data
                genderDistribution: {},
                courseMetrics: [],
                achievements: [],
                departmentOverview: []
            },
            courseMetricsColumns: [
                { title: 'Course', dataIndex: 'course', key: 'course', ellipsis: true },
                { title: 'Total Graduates', dataIndex: 'totalGraduates', key: 'totalGraduates', align: 'center' },
                { title: 'Growth Rate', dataIndex: 'growthRate', key: 'growthRate', align: 'center' }
            ],
            // School list with logo (updated paths)
            schoolList: [
                { name: 'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES', logo: '/images/schools/ASCOT School of Agricultural Science.jpg' },
                { name: 'SCHOOL OF ARTS AND SCIENCES', logo: '/images/schools/ASCOT School of Arts and Sciences.jpg' },
                { name: 'SCHOOL OF EDUCATION', logo: '/images/schools/ASCOT School of Education.jpg' },
                { name: 'SCHOOL OF ENGINEERING', logo: '/images/schools/ASCOT School of Engineering.jpg' },
                { name: 'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES', logo: '/images/schools/ASCOT School of Forestry and Environment Sciences.jpg' },
                { name: 'SCHOOL OF INDUSTRIAL TECHNOLOGY', logo: '/images/schools/ASCOT School of Industrial Technology.jpg' },
                { name: 'SCHOOL OF INFORMATION TECHNOLOGY', logo: '/images/schools/ASCOT School of Information Technology.jpg' },
                { name: 'COLLEGE OF LAW', logo: '/images/schools/ASCOT College of Law.jpg' },
                { name: 'SCHOOL OF ACCOUNTANCY AND BUSINESS MANAGEMENT', logo: '/images/schools/ASCOT School of Accountancy and Business Management.jpg' },
                { name: 'SCHOOL OF GRADUATE STUDIES', logo: '/images/schools/ASCOT School of Graduate Studies.jpg' }
            ],
            courseList: {
                'GRADUATE': [
                    'MASTER OF ARTS IN EDUCATION',
                    'MASTER IN MANAGEMENT',
                    'MASTER OF SCIENCE IN AGRICULTURE',
                    'MASTER OF SCIENCE IN ENVIRONMENTAL MANAGEMENT'
                ],
                'UNDERGRADUATE': [
                    'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES',
                    'SCHOOL OF EDUCATION',
                    'SCHOOL OF ARTS AND SCIENCES',
                    'SCHOOL OF ENGINEERING',
                    'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES',
                    'SCHOOL OF INDUSTRIAL TECHNOLOGY',
                    'SCHOOL OF INFORMATION TECHNOLOGY',
                ],
                'DIPLOMA/CERTIFICATE COURSES': [
                    'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES',
                    'SCHOOL OF INDUSTRIAL TECHNOLOGY',
                    'SCHOOL OF INFORMATION TECHNOLOGY'
                ]
            },
            majorList: {
                'MASTER OF ARTS IN EDUCATION': [
                    'major in Science Education',
                    'major in Filipino Language Teaching',
                    'major in Guidance Counseling',
                    'major in Educational Management'
                ],
                'MASTER IN MANAGEMENT': [
                    'major in Public Management',
                    'major in Business Management'
                ],
                'MASTER OF SCIENCE IN AGRICULTURE': [
                    'major in Animal Science',
                    'major in Crop Science'
                ],
                'MASTER OF SCIENCE IN ENVIRONMENTAL MANAGEMENT': [],
                'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES': [
                    'major in Animal Science',
                    'major in Crop Science',
                    'major in Fisheries'
                ],
                'SCHOOL OF ARTS AND SCIENCES': [
                    'in Hospitality Management',
                    'in Tourism Management',
                    'in Political Science'
                ],
                'SCHOOL OF EDUCATION': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education major in English',
                    'Bachelor of Secondary Education major in Filipino',
                    'Bachelor of Secondary Education major in Science',
                    'Bachelor of Secondary Education major in Social Studies',
                    'Bachelor of Secondary Education major in Mathematics',
                    'Bachelor of Physical Education',
                    'Bachelor of Technology and Livelihood Education major in Home Economics',
                    'Bachelor of Technology and Livelihood Education major in Information and Communication Technology'
                ],
                'SCHOOL OF ENGINEERING': [
                    'Bachelor of Science in Civil Engineering major in Construction Engineering and Management',
                    'Bachelor of Science in Electrical Engineering',
                    'Bachelor of Science in Mechanical Engineering'
                ],
                'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES': [
                    'Bachelor of Science in Forestry',
                    'Diploma in Forest Technology'
                ],
                'SCHOOL OF INDUSTRIAL TECHNOLOGY': [
                    'Bachelor in Industrial Technology major in Automotive Engineering Technology',
                    'Diploma in Automotive Engineering Technician Course',
                    'Associate in Automotive Engineering Technician Course',
                    'Certificate in Automotive Engineering Technician Course',
                    'Bachelor in Industrial Technology major in Electrical Engineering Technology',
                    'Diploma in Electrical Engineering Technician Course',
                    'Associate in Electrical Engineering Technician Course',
                    'Certificate in Electrical Engineering Technician Course',
                    'Bachelor in Industrial Technology major in Civil Engineering Technology',
                    'Diploma in Civil Engineering Technician Course',
                    'Associate in Senior Civil Engineering Technician Course',
                    'Certificate in Junior Civil Engineering Technician Course',
                    'Bachelor in Industrial Technology major in Food Technology',
                    'Diploma in Food Technology',
                    'Certificate in Food Technology'
                ],
                'SCHOOL OF INFORMATION TECHNOLOGY': [
                    'Bachelor of Science in Information Technology with Specialization in Application Programming',
                    'Bachelor of Science in Information Technology with Specialization in Digital Design',
                    'Associate in Computer Technology'
                ],
            },
            // Centralized pagination object
            pagination: {
                current: 1,
                pageSize: 10,
                showSizeChanger: false,
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
                { 
                    title: 'STUDENT NUMBER', 
                    dataIndex: 'studentId', 
                    key: 'studentId', 
                    scopedSlots: { customRender: 'studentId' }, 
                    sorter: (a, b) => a.studentId.localeCompare(b.studentId), 
                    width: 180 
                },
                { 
                    title: 'STUDENT', 
                    dataIndex: 'name', 
                    key: 'name', 
                    scopedSlots: { customRender: 'name' }, 
                    sorter: (a, b) => a.name.localeCompare(b.name), 
                    width: 280 
                },
                { 
                    title: 'ADDRESS', 
                    dataIndex: 'address', 
                    key: 'address', 
                    scopedSlots: { customRender: 'address' }, 
                    sorter: (a, b) => a.address.localeCompare(b.address), 
                    width: 280 
                },
                { 
                    title: 'SEX', 
                    dataIndex: 'gender', 
                    key: 'gender', 
                    width: 120,
                    sorter: (a, b) => a.gender.localeCompare(b.gender), 
                    scopedSlots: { customRender: 'gender' } 
                },
                { 
                    title: 'COURSE', 
                    dataIndex: 'course', 
                    key: 'course',
                    sorter: (a, b) => a.course.localeCompare(b.course), 
                    scopedSlots: { customRender: 'course' } 
                },
                { 
                    title: 'CLASS OF', 
                    dataIndex: 'yearGraduated', 
                    key: 'yearGraduated',
                    sorter: (a, b) => a.yearGraduated.localeCompare(b.yearGraduated), 
                    scopedSlots: { customRender: 'yearGraduated' } ,
                    width: 130 
                },
                { 
                    title: 'ACHIEVEMENT', 
                    dataIndex: 'achievement', 
                    key: 'achievement', 
                    sorter: (a, b) => a.achievement.localeCompare(b.achievement), 
                    scopedSlots: { customRender: 'achievement' }, 
                    width: 200 
                },
                { 
                    title: 'ACTION', 
                    key: 'action', 
                    scopedSlots: { customRender: 'action' }, 
                    width: 150, 
                    align: 'center', 
                    fixed: 'right' 
                },
            ]
        }
    },
    watch: {
        dashboardFilters: {
            handler: 'applyFilters',
            deep: true
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
         achievementsData() {
            // Use selectedAchievementYear or latest year
            let year = this.selectedAchievementYear;
            const years = Object.keys(this.analyticsData.yearlyGender || {});
            if (!year && years.length > 0) {
                year = years.sort((a,b)=>b-a)[0];
                this.selectedAchievementYear = year;
            }
            // Filter allGraduates by year
            const grads = this.allGraduates.filter(g => String(g.yearGraduated) === String(year));
            const achievementCounts = {};
            grads.forEach(g => {
                const ach = g.achievement || 'None';
                achievementCounts[ach] = (achievementCounts[ach] || 0) + 1;
            });
            const total = grads.length || 1;
            const colors = ['#52c41a', '#1890ff', '#722ed1', '#faad14', '#eb2f96', '#1890ff'];
            return Object.keys(achievementCounts).map((key, idx) => ({
                type: key.toLowerCase().replace(/ /g, '_'),
                title: key,
                count: achievementCounts[key],
                percentage: Math.round((achievementCounts[key] / total) * 100),
                color: colors[idx % colors.length]
            }));
        },
        availableYears() {
            // Return all years for selected program, course, major
            if (!this.selectedProgram || !this.selectedCourse || !this.selectedMajor) return [];
            const years = this.allGraduates
                .filter(g => g.program === this.selectedProgram && g.course === this.selectedCourse && g.major === this.selectedMajor)
                .map(g => g.yearGraduated)
                .filter((v, i, arr) => arr.indexOf(v) === i)
                .sort((a, b) => parseInt(b) - parseInt(a));
            return years;
        },
        courseMetricsData() {
            return this.analyticsData.courseMetrics.map((course, index) => ({
                key: index,
                course: course.course,
                totalGraduates: course.total_graduates,
                completionRate: course.completion_rate,
                growthRate: course.growth_rate || 0
            }));
        },
        achievementsData() {
            return this.analyticsData.achievements || [];
        },
        departmentComparison() {
            return this.analyticsData.courseMetrics.map(course => ({
                name: course.course.length > 20 ? course.course.substring(0, 20) + '...' : course.course,
                totalGraduates: course.total_graduates,
                avgPerYear: course.avg_graduates_per_year,
                growthRate: Math.floor(Math.random() * 20) - 5 // Mocking growth rate for now
            }));
        },
        yearOptions() {
            // Get all unique years from allGraduates
            const years = this.allGraduates.map(g => g.yearGraduated).filter(Boolean);
            return Array.from(new Set(years)).sort((a, b) => b - a);
        },
        yearAnalysis() {
            const years = Object.keys(this.analyticsData.graduatesPerYear).sort((a, b) => b - a);
            return years.map((year, index) => {
                const graduates = this.analyticsData.graduatesPerYear[year] || 0;
                const prevYear = years[index + 1];
                const prevGraduates = prevYear ? this.analyticsData.graduatesPerYear[prevYear] || 0 : 0;
                
                const changePercent = prevGraduates > 0 ? 
                    Math.round(((graduates - prevGraduates) / prevGraduates) * 100) : 0;

                // Use the calculated gender data for this year, defaulting to 0 if missing
                const genderBreakdown = this.analyticsData.yearlyGender?.[year] || { male: 0, female: 0 };
                
                return {
                    year,
                    graduates,
                    changePercent: Math.abs(changePercent),
                    trend: changePercent > 0 ? 'up' : changePercent < 0 ? 'down' : 'same',
                    activeCourses: this.analyticsData.courseMetrics.length, // Simplified for now
                    maleCount: genderBreakdown.male,
                    femaleCount: genderBreakdown.female,
                    topCourse: this.getTopCourseForYear(year)
                };
            });
        }
    },
    created() {
        this.loadGraduateData();
        this.loadAnalyticsData();
    },
    methods: {
        // ...existing code...
        getCourseName(course) {
            if (!course) return '';
            // Remove school prefix if present
            const schools = Object.keys(this.schoolColors);
            for (const school of schools) {
                if (course.startsWith(school + ' - ')) {
                    return course.replace(school + ' - ', '');
                }
                if (course === school) {
                    return '';
                }
            }
            return course;
        },
        onDashboardFiltersChanged(filters) {
            this.dashboardFilters = filters;
        },
        applyFilters() {
            let grads = [...this.allGraduates];
            // Navigation filters
            if (this.selectedSchool) {
                grads = grads.filter(g => g.school === this.selectedSchool);
            }
            if (this.selectedCourse) {
                grads = grads.filter(g => g.course === this.selectedCourse);
            }
            if (this.selectedMajor) {
                grads = grads.filter(g => g.major === this.selectedMajor);
            }
            if (this.selectedYear) {
                grads = grads.filter(g => String(g.yearGraduated) === String(this.selectedYear));
            }
            // DashboardFilters.vue filters
            if (this.dashboardFilters) {
                if (this.dashboardFilters.classYear && this.dashboardFilters.classYear !== 'all') {
                    grads = grads.filter(g => String(g.yearGraduated) === String(this.dashboardFilters.classYear));
                }
                if (this.dashboardFilters.course && this.dashboardFilters.course !== 'all') {
                    grads = grads.filter(g => g.course === this.dashboardFilters.course);
                }
                // Add more filter fields as you extend DashboardFilters.vue
            }
            this.filteredGraduates = grads;
        },
        // School navigation handler
        selectSchool(school) {
            this.selectedSchool = school.name;
            this.selectedSchoolLogo = school.logo;
            this.selectedCourse = null;
            this.selectedMajor = null;
            this.selectedYear = null;
            this.applyFilters();
        },
        Chart,
        getTopCourseForYear(year) {
            // Logic could be expanded if course-per-year data is stored deeply
            return this.analyticsData.courseMetrics[0]?.course || 'N/A';
        },
        goBackTo(level) {
            if (level === 'school') {
                this.selectedSchool = null;
                this.selectedSchoolLogo = null;
                this.selectedCourse = null;
                this.selectedMajor = null;
                this.selectedYear = null;
            } else if (level === 'course') {
                this.selectedMajor = null;
                this.selectedYear = null;
            } else if (level === 'major') {
                this.selectedYear = null;
            }
            this.applyFilters();
        },

        // --- Graduates per School per Year Data ---
        graduatesPerSchoolPerYear() {
            // Build { [school]: { [year]: count } }
            const result = {};
            this.allGraduates.forEach(g => {
                const school = g.school;
                const year = g.yearGraduated;
                if (!school || !year) return;
                if (!result[school]) result[school] = {};
                result[school][year] = (result[school][year] || 0) + 1;
            });
            return result;
        },
        // --- Create Graduates per School per Year Chart ---
        createGraduatesPerSchoolChart() {
            const canvas = document.getElementById('graduatesPerSchoolChart');
            if (!canvas) return;
            const Chart = this.Chart || window.Chart;
            // Destroy existing
            if (this.graduatesPerSchoolChartInstance) {
                this.graduatesPerSchoolChartInstance.destroy();
                this.graduatesPerSchoolChartInstance = null;
            }
            const ctx = canvas.getContext('2d');
            const dataBySchool = this.graduatesPerSchoolPerYear();
            // Get all years (1997 to present)
            const years = [];
            const currentYear = new Date().getFullYear();
            for (let y = 1997; y <= currentYear; y++) years.push(String(y));
            // Build datasets
            const datasets = Object.keys(dataBySchool).map(school => {
                const color = this.schoolColors[school] || '#888';
                return {
                    label: school,
                    data: years.map(y => dataBySchool[school][y] || 0),
                    borderColor: color,
                    backgroundColor: color + '33',
                    fill: false,
                    tension: 0.3,
                    pointRadius: 2
                };
            });
            this.graduatesPerSchoolChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: years,
                    datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: { mode: 'index', intersect: false }
                    },
                    scales: {
                        y: { beginAtZero: true, title: { display: true, text: 'Graduates' } },
                        x: { title: { display: true, text: 'Year' } }
                    }
                }
            });
        },

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
        // Navigation handlers
        selectProgram(program) {
            this.selectedProgram = program;
            this.selectedCourse = null;
            this.selectedMajor = null;
            this.selectedYear = null;
        },
        selectCourse(course) {
            this.selectedCourse = course;
            this.selectedMajor = null;
            this.selectedYear = null;
            this.applyFilters();
        },
        selectMajor(major) {
            this.selectedMajor = major;
            this.selectedYear = null;
        },
        selectYear(year) {
            this.selectedYear = year;
            this.applyFilters();
        },
        goBackNav() {
            if (this.selectedYear) {
                this.selectedYear = null;
            } else if (this.selectedMajor) {
                this.selectedMajor = null;
            } else if (this.selectedCourse) {
                this.selectedCourse = null;
            } else if (this.selectedSchool) {
                this.selectedSchool = null;
                this.selectedSchoolLogo = null;
            }
            this.applyFilters();
        },
        // Filtering
        // Removed duplicate applyFilters. All filtering uses the comprehensive method above.
        // Table Change
        handleTableChange(pagination, filters, sorter) {
            const pager = { ...this.pagination };
            pager.current = pagination.current;
            pager.pageSize = pagination.pageSize;
            this.pagination = pager;
        },
        // Remove onPageSizeChange since page size is fixed
        // View
        viewGraduateDetails(graduate) {
            this.selectedGraduate = graduate;
            this.detailsModal = true;
        },
        // Edit - Open
        onEdit(record) {
            this.editingGraduate = { ...record };
            this.editModalVisible = true;
        },
        // Edit - Save
        async updateGraduate() {
            if (!this.editingGraduate) return;
            this.loading = true;
            try {
                const response = await this.$api.post('graduates/update', this.editingGraduate);
                if (response && response.data && !response.data.error) {
                    this.$message.success('Graduate record updated successfully');
                    this.editModalVisible = false;
                    this.loadGraduateData();
                } else {
                    const errorMsg = response?.data?.message || 'Failed to update record';
                    this.$message.error(errorMsg);
                }
            } catch (error) {
                const serverMsg = error.response?.data?.message || 'Server error during update';
                this.$message.error(serverMsg);
            } finally {
                this.loading = false;
            }
        },
        // Delete
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
            return colorMap[achievement] || 'skyblue';
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
        },
        async loadAnalyticsData() {
            this.loading = true;
            try {
                if (this.$api) {
                    // Switch to fetching the FULL list to calculate metrics client-side
                    // This ensures accuracy for Year-over-Year gender breakdown
                    const response = await this.$api.get("graduates/get/list");
                    
                    if (response.data && !response.data.error) {
                        this.processListToAnalytics(response.data.list);
                    } else {
                        this.loadFallbackData();
                    }
                } else {
                    this.loadFallbackData();
                }
            } catch (error) {
                console.error('Error loading analytics data:', error);
                this.loadFallbackData();
            }
            this.loading = false;
        },

        processListToAnalytics(list) {
            // Initialize structures
            const yearlyTrends = {};
            const yearlyGender = {};
            const genderTotals = { male: 0, female: 0 };
            const courseStats = {};
            const courseToSchool = {};
            const achievementCounts = {};

            // Process raw list
            list.forEach(grad => {
                const year = grad.yearGraduated || 'Unknown';
                const gender = (grad.gender || 'Unknown').toLowerCase();
                const course = grad.course || 'Unknown';
                const school = grad.school || '';
                const achievement = grad.achievement;

                // Yearly Total
                yearlyTrends[year] = (yearlyTrends[year] || 0) + 1;

                // Yearly Gender
                if (!yearlyGender[year]) yearlyGender[year] = { male: 0, female: 0 };
                if (gender === 'male') yearlyGender[year].male++;
                if (gender === 'female') yearlyGender[year].female++;

                // Global Gender
                if (gender === 'male') genderTotals.male++;
                if (gender === 'female') genderTotals.female++;

                // Course Stats
                if (!courseStats[course]) courseStats[course] = 0;
                courseStats[course]++;
                // Track school for each course (first seen wins)
                if (course && school && !courseToSchool[course]) {
                    courseToSchool[course] = school;
                }

                // Achievement Stats
                if (achievement) {
                    achievementCounts[achievement] = (achievementCounts[achievement] || 0) + 1;
                }
            });

            // Format Summary Stats
            this.summaryStats = [
                {
                    key: 'total',
                    title: 'Total Graduates',
                    value: list.length,
                    icon: 'user',
                    color: '#1890ff',
                    trend: 8.2 // Calculated trend logic can be added here
                },
                {
                    key: 'courses',
                    title: 'Active Courses',
                    value: Object.keys(courseStats).length,
                    icon: 'book',
                    color: '#52c41a',
                    trend: 2.1
                },
                {
                    key: 'departments',
                    title: 'Programs',
                    value: new Set(Object.keys(courseStats).map(c => c.split(' ')[0])).size,
                    icon: 'cluster',
                    color: '#faad14',
                    trend: 0
                }
            ];

            // Format Data for Charts
            this.analyticsData.graduatesPerYear = yearlyTrends;
            this.analyticsData.yearlyGender = yearlyGender;
            this.analyticsData.genderDistribution = genderTotals;

            // Format Course Metrics
            this.analyticsData.courseMetrics = Object.keys(courseStats).map(course => ({
                course: course,
                school: courseToSchool[course] || '',
                total_graduates: courseStats[course],
                completion_rate: 95 + Math.floor(Math.random() * 5), // Mocked
                avg_graduates_per_year: Math.round(courseStats[course] / Object.keys(yearlyTrends).length),
                growth_rate: Math.floor(Math.random() * 10)
            })).sort((a, b) => b.total_graduates - a.total_graduates);

            // Format Achievements
            this.analyticsData.achievements = Object.keys(achievementCounts).map((key, index) => ({
                type: key.toLowerCase().replace(/ /g, '_'),
                title: key,
                count: achievementCounts[key],
                percentage: Math.round((achievementCounts[key] / list.length) * 100),
                color: ['#52c41a', '#1890ff', '#722ed1', '#faad14', '#eb2f96'][index % 5]
            }));

            this.initializeCharts();
        },

        loadFallbackData() {
            // Mock data extended to show the logic handles wide ranges
            this.summaryStats = [
                { key: 'total', title: 'Total Graduates', value: 100, icon: 'user', color: '#1890ff', trend: 8.2 },
                { key: 'courses', title: 'Active Courses', value: 5, icon: 'book', color: '#52c41a', trend: 2.1 },
                { key: 'departments', title: 'Departments', value: 3, icon: 'cluster', color: '#faad14', trend: 0 }
            ];

            this.analyticsData = {
                graduatesPerYear: { 2025: 15, 2024: 8, 2023: 4 },
                // Mock data for yearly gender
                yearlyGender: {
                    2025: { male: 10, female: 5 },
                    2024: { male: 4, female: 4 },
                    2023: { male: 1, female: 3 }
                },
                genderDistribution: { male: 60, female: 40 },
                courseMetrics: [
                    { course: 'BS Information Tech', total_graduates: 50, completion_rate: 98, avg_graduates_per_year: 5 },
                    { course: 'BS Computer Science', total_graduates: 30, completion_rate: 95, avg_graduates_per_year: 4 },
                    { course: 'BS Education', total_graduates: 20, completion_rate: 100, avg_graduates_per_year: 3 }
                ],
                achievements: [
                    { type: 'cum_laude', title: 'Cum Laude', count: 5, percentage: 5, color: '#52c41a' },
                    { type: 'magna_cum_laude', title: 'Magna Cum Laude', count: 2, percentage: 2, color: '#1890ff' },
                    { type: 'summa_cum_laude', title: 'Summa Cum Laude', count: 1, percentage: 1, color: '#722ed1' }
                ],
                departmentOverview: []
            };
            
            this.initializeCharts();
        },

        initializeCharts() {
            this.$nextTick(() => {
                setTimeout(() => {
                    this.createGraduatesChart();
                    this.createGenderChart();
                    this.createCourseChart();
                    this.createDepartmentsChart();
                }, 100);
            });
        },

        createGraduatesChart() {
            const canvas = document.getElementById('graduatesChart');
            if (!canvas) return;

            // FIX: Destroy existing Chart.js instance on the canvas
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();

            // Destroy Vue reference
            if (this.graduatesChartInstance) {
                this.graduatesChartInstance.destroy();
                this.graduatesChartInstance = null;
            }

            const ctx = canvas.getContext('2d');
            const years = Object.keys(this.analyticsData.graduatesPerYear || {}).sort();
            const values = years.map(year => this.analyticsData.graduatesPerYear[year] || 0);

            this.graduatesChartInstance = new Chart(ctx, {
                type: this.graduatesChartType,
                data: {
                    labels: years,
                    datasets: [{
                        label: 'Total Graduates',
                        data: values,
                        borderColor: '#1890ff',
                        backgroundColor: this.graduatesChartType === 'bar' 
                            ? 'rgba(24, 144, 255, 0.6)' 
                            : 'rgba(24, 144, 255, 0.2)', 
                        borderWidth: 2,
                        fill: false,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: { 
                            beginAtZero: true, 
                            grid: { color: 'rgba(0, 0, 0, 0.1)' } 
                        },
                        x: { 
                            grid: { display: false } 
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                        }
                    }
                }
            });
        },

        createGenderChart() {
            const canvas = document.getElementById('genderChart');
            if (!canvas) return;

            // FIX: Destroy existing Chart.js instance on the canvas
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();

            if (this.genderChartInstance) {
                this.genderChartInstance.destroy();
                this.genderChartInstance = null;
            }

            const ctx = canvas.getContext('2d');
            // Use selected year or latest year
            let year = this.selectedGenderYear;
            const years = Object.keys(this.analyticsData.yearlyGender || {});
            if (!year && years.length > 0) {
                year = years.sort((a,b)=>b-a)[0];
                this.selectedGenderYear = year;
            }
            const data = (this.analyticsData.yearlyGender && this.analyticsData.yearlyGender[year]) || { male: 0, female: 0 };

            this.genderChartInstance = new Chart(ctx, {
                type: this.genderChartType === 'bar' ? 'bar' : this.genderChartType,
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        label: 'Gender',
                        data: [data.male, data.female],
                        backgroundColor: ['#1890ff', '#eb2f96'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { 
                        legend: { 
                            position: 'bottom',
                            display: this.genderChartType !== 'bar' 
                        } 
                    }
                }
            });
        },

        createCourseChart() {
            const canvas = document.getElementById('courseChart');
            if (!canvas) return;

            // FIX: Destroy existing Chart.js instance on the canvas
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();

            if (this.courseChartInstance) {
                this.courseChartInstance.destroy();
                this.courseChartInstance = null;
            }

            const ctx = canvas.getContext('2d');
            const courses = this.analyticsData.courseMetrics;
            const labels = courses.map(c => c.course.length > 20 ? c.course.substring(0, 20) + '...' : c.course);
            const values = courses.map(c => c.total_graduates);

            let chartType = this.courseMetricsChartType || 'line';
            let datasetConfig = {
                label: 'Total Graduates',
                data: values,
                backgroundColor: chartType === 'bar' ? '#52c41a' : 'rgba(82,196,26,0.2)',
                borderColor: '#52c41a',
                borderWidth: 2,
                fill: chartType === 'line',
                tension: 0.4
            };

            this.courseChartInstance = new Chart(ctx, {
                type: chartType,
                data: {
                    labels,
                    datasets: [datasetConfig]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        createDepartmentsChart() {
            const canvas = document.getElementById('departmentsChart');
            if (!canvas) return;

            // FIX: Destroy existing Chart.js instance on the canvas
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();

            if (this.departmentsChartInstance) {
                this.departmentsChartInstance.destroy();
                this.departmentsChartInstance = null;
            }

            const ctx = canvas.getContext('2d');
            const departments = {};
            this.analyticsData.courseMetrics.forEach(course => {
                console.log('[Dashboard] Processing course for department chart:', course.course);
                // Use the full department name for matching
                let dept = '';
                if (course.school.includes('SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES')) dept = 'SCHOOL OF AGRICULTURE AND AQUATIC SCIENCES';
                else if (course.school.includes('SCHOOL OF ARTS AND SCIENCES')) dept = 'SCHOOL OF ARTS AND SCIENCES';
                else if (course.school.includes('SCHOOL OF EDUCATION')) dept = 'SCHOOL OF EDUCATION';
                else if (course.school.includes('SCHOOL OF ENGINEERING')) dept = 'SCHOOL OF ENGINEERING';
                else if (course.school.includes('SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES')) dept = 'SCHOOL OF FORESTRY AND ENVIRONMENTAL SCIENCES';
                else if (course.school.includes('SCHOOL OF INDUSTRIAL TECHNOLOGY')) dept = 'SCHOOL OF INDUSTRIAL TECHNOLOGY';
                else if (course.school.includes('SCHOOL OF INFORMATION TECHNOLOGY')) dept = 'SCHOOL OF INFORMATION TECHNOLOGY';
                else if (course.school.includes('COLLEGE OF LAW')) dept = 'COLLEGE OF LAW';
                else if (course.school.includes('SCHOOL OF ACCOUNTANCY AND BUSINESS MANAGEMENT')) dept = 'SCHOOL OF ACCOUNTANCY AND BUSINESS MANAGEMENT';
                else if (course.school.includes('SCHOOL OF GRADUATE STUDIES')) dept = 'SCHOOL OF GRADUATE STUDIES';
                else dept = course.school.split(' ')[0];
                departments[dept] = (departments[dept] || 0) + course.total_graduates;
            });

            // Use color mapping from data, robust matching
            const backgroundColors = Object.keys(departments).map(dept => {
                // Try exact match first
                let color = this.schoolColors[dept];
                if (!color) {
                    // Try case-insensitive and trimmed match
                    const match = Object.keys(this.schoolColors).find(key => key.trim().toUpperCase() === dept.trim().toUpperCase());
                    if (match) color = this.schoolColors[match];
                }
                if (!color) {
                    // Try partial match
                    const partial = Object.keys(this.schoolColors).find(key => dept.toUpperCase().includes(key.trim().toUpperCase()) || key.trim().toUpperCase().includes(dept.toUpperCase()));
                    if (partial) color = this.schoolColors[partial];
                }
                if (!color) {
                    console.warn('[Dashboard] No color mapping for department:', dept);
                }
                return color || '#cccccc';
            });

            this.departmentsChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(departments),
                    datasets: [{
                        data: Object.values(departments),
                        backgroundColor: backgroundColors,
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                boxWidth: 24,
                                font: { size: 14 },
                                padding: 18,
                                color: '#222',
                                // Ensure full label display
                                textAlign: 'left',
                                usePointStyle: true
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    // Show full label and value
                                    return context.label + ': ' + context.parsed;
                                }
                            }
                        }
                    }
                }
            });
        },

        createDepartmentTrendsChart() {
            const canvas = document.getElementById('departmentTrendsChart');
            if (!canvas) return;

            // FIX: Destroy existing Chart.js instance on the canvas
            const existingChart = Chart.getChart(canvas);
            if (existingChart) existingChart.destroy();

            if (this.departmentTrendsChartInstance) {
                this.departmentTrendsChartInstance.destroy();
                this.departmentTrendsChartInstance = null;
            }

            const ctx = canvas.getContext('2d');
            const years = Object.keys(this.analyticsData.graduatesPerYear).sort();
            
            this.departmentTrendsChartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: years,
                    datasets: this.analyticsData.courseMetrics.map((course, index) => ({
                        label: course.course.length > 15 ? course.course.substring(0, 15) + '...' : course.course,
                        data: years.map(() => Math.floor(Math.random() * course.total_graduates) + 1),
                        borderColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'][index % 5],
                        backgroundColor: 'transparent',
                        tension: 0.4
                    }))
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } }
                }
            });
        },

        updateGraduatesChart() {
            this.createGraduatesChart();
        },

        updateGenderChart() {
            this.createGenderChart();
        },

        updateCourseMetrics() {
            if (this.courseMetricsView === 'chart') {
                this.$nextTick(() => this.createCourseChart());
            }
        },

        updateDepartmentsView() {
            this.$nextTick(() => {
                if (this.departmentsView === 'overview') {
                    this.createDepartmentsChart();
                } else if (this.departmentsView === 'trends') {
                    this.createDepartmentTrendsChart();
                }
            });
        },

        refreshData() {
            this.loadAnalyticsData();
        },

        exportReport() {
            this.exporting = true;
            
            try {
                const escape = (val) => {
                    if (val === null || val === undefined) return '';
                    const str = String(val);
                    if (str.includes(',') || str.includes('"') || str.includes('\n')) {
                        return `"${str.replace(/"/g, '""')}"`;
                    }
                    return str;
                };

                let csvContent = "";

                csvContent += "SUMMARY STATISTICS\n";
                csvContent += "Metric,Value,Trend\n";
                this.summaryStats.forEach(stat => {
                    const trend = stat.trend !== undefined ? `${stat.trend}%` : '-';
                    csvContent += `${escape(stat.title)},${escape(stat.value)}${escape(stat.suffix || '')},${escape(trend)}\n`;
                });
                csvContent += "\n";

                csvContent += "GRADUATES PER YEAR\n";
                csvContent += "Year,Count\n";
                const years = Object.keys(this.analyticsData.graduatesPerYear || {}).sort();
                if (years.length > 0) {
                    years.forEach(year => {
                        csvContent += `${escape(year)},${escape(this.analyticsData.graduatesPerYear[year])}\n`;
                    });
                } else {
                    csvContent += "No data available,\n";
                }
                csvContent += "\n";

                csvContent += "GENDER DISTRIBUTION\n";
                csvContent += "Gender,Count\n";
                const genderData = this.analyticsData.genderDistribution || {};
                csvContent += `Male,${escape(genderData.male || 0)}\n`;
                csvContent += `Female,${escape(genderData.female || 0)}\n`;
                csvContent += "\n";

                csvContent += "COURSE METRICS\n";
                csvContent += "Course Name,Total Graduates,Completion Rate (%),Avg Per Year,Growth Rate (%)\n";
                if (this.analyticsData.courseMetrics && this.analyticsData.courseMetrics.length > 0) {
                    this.analyticsData.courseMetrics.forEach(item => {
                        csvContent += `${escape(item.course)},${escape(item.total_graduates)},${escape(item.completion_rate)},${escape(item.avg_graduates_per_year)},${escape(item.growth_rate || 0)}\n`;
                    });
                } else {
                    csvContent += "No data available,,,,\n";
                }
                csvContent += "\n";

                csvContent += "ACHIEVEMENTS\n";
                csvContent += "Achievement,Count,Percentage (%)\n";
                if (this.analyticsData.achievements && this.analyticsData.achievements.length > 0) {
                    this.analyticsData.achievements.forEach(item => {
                        csvContent += `${escape(item.title)},${escape(item.count)},${escape(item.percentage)}\n`;
                    });
                } else {
                    csvContent += "No data available,,\n";
                }
                csvContent += "\n";

                csvContent += "DEPARTMENTS OVERVIEW\n";
                csvContent += "Department,Total Graduates\n";
                
                const departments = {};
                (this.analyticsData.courseMetrics || []).forEach(course => {
                    const dept = course.course ? course.course.split(' ')[0] : 'Unknown';
                    departments[dept] = (departments[dept] || 0) + course.total_graduates;
                });
                
                if (Object.keys(departments).length > 0) {
                    Object.keys(departments).forEach(dept => {
                        csvContent += `${escape(dept)},${escape(departments[dept])}\n`;
                    });
                } else {
                    csvContent += "No data available,\n";
                }

                const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                const url = URL.createObjectURL(blob);
                const link = document.createElement('a');
                
                const date = new Date().toISOString().slice(0, 10);
                link.setAttribute('href', url);
                link.setAttribute('download', `full_analytics_report_${date}.csv`);
                
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                this.$notification.success({
                    message: 'Export Successful',
                    description: 'The full analytics report has been downloaded successfully.'
                });

            } catch (error) {
                console.error('Export error:', error);
                this.$notification.error({
                    message: 'Export Failed',
                    description: 'An error occurred while generating the CSV file.'
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
    .folder-card {
        background: transparent;
        border-radius: 10px;
        // box-shadow: 0 1px 4px rgba(82,196,26,0.10);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 5px 0 8px 0;
        margin-bottom: 10px;
        min-height: 90px;
        max-width: 160px;
        width: 100%;
        cursor: pointer;
        // border: 1.5px solid #d9f7be;
        transition: box-shadow 0.18s, border-color 0.18s, background 0.18s;
        &:hover {
            box-shadow: 0 4px 16px rgba(82,196,26,0.18);
            border-color: #52c41a;
            background: #f6ffed;
        }
    }
    .small-folder {
        min-height: 90px;
        max-width: 152px;
        margin: 0 auto 10px auto;
        width: 100%;
    }
    .folder-label {
        font-size: 14px;
        font-weight: 500;
        color: #222;
        margin-top: 6px;
        text-align: center;
        word-break: break-word;
        line-height: 1.2;
        letter-spacing: 0.01em;
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

<style scoped>
/* Base layout */
.analytics-container {
    background: linear-gradient(135deg, #f5f7fa 0%, #e9ecef 100%);
    min-height: 100vh;
    padding: 24px;
}

/* Header styles */
.profile-nav-bg {
    height: 140px;
    position: relative;
    background-size: cover;
    background-position: center;
    border-radius: 12px 12px 0 0;
}

.card-profile-head {
    background: #fff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 32px;
    border-radius: 12px;
    overflow: hidden;
}

.col-info {
    display: flex;
    align-items: center;
    padding: 20px;
}

.avatar-info {
    margin-left: 20px;
}

.avatar-info h4 {
    color: #1a1a1a;
    margin-bottom: 6px;
    font-size: 20px;
    font-weight: 600;
}

.avatar-info p {
    color: #6c757d;
    margin: 0;
    font-size: 14px;
}

/* Card Styles */
.stat-card,
.chart-card,
.achievement-card,
.yoy-card {
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    overflow: hidden;
}

.stat-card:hover,
.chart-card:hover,
.achievement-card:hover,
.yoy-card:hover {
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
}

.card-title {
    font-size: 16px;
    font-weight: 600;
    color: #1a1a1a;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Summary Statistics */
.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
}

.stat-trend {
    margin-top: 12px;
}

.trend-tag {
    border-radius: 6px;
    font-size: 12px;
    padding: 2px 8px;
}

/* Chart containers */
.chart-container {
    height: 320px;
    position: relative;
    padding: 20px;
    background: #fafbfc;
    border-radius: 8px;
    margin: 16px 0;
}

/* Achievements */
.achievements-list {
    padding: 8px 0;
}

.achievement-item {
    margin-bottom: 24px;
    padding: 16px;
    background: #fafbfc;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.achievement-item:hover {
    background: #f0f2f5;
    transform: translateX(4px);
}

.achievement-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.achievement-title {
    font-weight: 500;
    color: #1a1a1a;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 6px;
}

/* Comparison grid */
.comparison-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
    padding: 20px;
    background: #fafbfc;
    border-radius: 8px;
}

.dept-comparison-item {
    padding: 20px;
    background: #fff;
    border-radius: 10px;
    border: 1px solid #e8eaed;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    transition: all 0.3s ease;
}

.dept-comparison-item:hover {
    border-color: #1890ff;
    box-shadow: 0 4px 12px rgba(24, 144, 255, 0.15);
    transform: translateY(-2px);
}

.dept-name {
    font-weight: 600;
    margin-bottom: 16px;
    color: #1a1a1a;
    font-size: 15px;
    padding-bottom: 12px;
    border-bottom: 2px solid #f0f2f5;
}

.dept-stats {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
}

/* Year-over-Year analysis */
.yoy-analysis {
    padding: 8px 0;
    max-height: 600px;
    overflow-y: auto;
}

.yoy-analysis::-webkit-scrollbar {
    width: 6px;
}

.yoy-analysis::-webkit-scrollbar-thumb {
    background-color: #d0d7de;
    border-radius: 3px;
}

.yoy-analysis::-webkit-scrollbar-thumb:hover {
    background-color: #b4bcc4;
}

.yoy-item {
    margin-bottom: 16px;
    padding: 20px;
    background: linear-gradient(135deg, #fafbfc 0%, #f6f8fa 100%);
    border-radius: 10px;
    border: 1px solid #e8eaed;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
    transition: all 0.2s ease;
}

.yoy-item:hover {
    border-color: #1890ff;
    box-shadow: 0 4px 12px rgba(24, 144, 255, 0.12);
}

.yoy-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 16px;
    padding-bottom: 12px;
    border-bottom: 2px solid #fff;
}

.yoy-header h4 {
    margin: 0;
    color: #1a1a1a;
    font-size: 18px;
    font-weight: 600;
}

.yoy-metrics {
    margin-top: 8px;
}

.metric {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 8px 0;
}

.metric-label {
    font-size: 13px;
    color: #6c757d;
    font-weight: 500;
}

.metric-value {
    font-weight: 600;
    color: #1a1a1a;
    font-size: 14px;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 80px;
}

/* Utilities */
.mb-24 {
    margin-bottom: 24px;
}

.mt-24 {
    margin-top: 24px;
}

/* Responsive design */
@media (max-width: 768px) {
    .analytics-container {
        padding: 16px;
    }
    
    .card-title {
        font-size: 14px;
    }
    
    .comparison-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .dept-stats {
        grid-template-columns: 1fr;
    }
    
    .chart-container {
        height: 250px;
        padding: 12px;
    }
    
    .yoy-analysis {
        max-height: 500px;
    }
    
    .stat-icon-wrapper {
        width: 40px;
        height: 40px;
    }
}

/* Ant Design overrides */
::v-deep .ant-card-head {
    border-bottom: 2px solid #f0f2f5;
    padding: 16px 24px;
}

::v-deep .ant-card-body {
    padding: 24px;
}

::v-deep .ant-statistic-title {
    font-size: 14px;
    color: #6c757d;
    margin-bottom: 8px;
    font-weight: 500;
}

::v-deep .ant-table-small {
    border-radius: 8px;
}

::v-deep .ant-progress-inner {
    border-radius: 4px;
}
</style>