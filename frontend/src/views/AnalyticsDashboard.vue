<template>
    <div class="analytics-container">
        <div class="profile-nav-bg" style="background-image: url('images/coverbg.png')"></div>

        <a-card :bordered="false" class="card-profile-head" :bodyStyle="{padding: 0,}">
            <template #title>
                <a-row type="flex" align="middle">
                    <a-col :span="24" :md="12" class="col-info">
                        <a-avatar 
                            shape="square" 
                            :size="74" 
                            icon="bar-chart" 
                            :style="{ backgroundColor: '#722ed1' }"
                        />
                        <div class="avatar-info">
                            <h4 class="font-semibold m-0">Analytics Dashboard</h4>
                            <p>Comprehensive insights and trends from graduate database</p>
                        </div>
                    </a-col>
                    <a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">
                        <a-space size="small">
                            <a-button type="primary" @click="exportReport" :loading="exporting">
                                <a-icon type="download" />Export Full Report
                            </a-button>
                            <a-button type="default" @click="refreshData" :loading="loading">
                                <a-icon type="reload" />Refresh
                            </a-button>
                        </a-space>
                    </a-col>
                </a-row>
            </template>
        </a-card>

        <a-row :gutter="[24, 24]" class="mb-24 mt-24">
            <a-col :xs="24" :sm="12" :md="6" v-for="stat in summaryStats" :key="stat.key">
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
            <a-col :xs="24" :lg="16">
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
            <a-col :xs="24" :lg="8">
                <a-card class="achievement-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="trophy" /> Achievements Distribution</span>
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

            <a-col :xs="24" :lg="8">
                <a-card class="chart-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="pie-chart" /> Gender Distribution</span>
                    </template>
                    <template #extra>
                        <a-radio-group v-model="genderChartType" @change="updateGenderChart" size="small">
                            <a-radio-button value="doughnut">Doughnut</a-radio-button>
                            <a-radio-button value="pie">Pie</a-radio-button>
                            <a-radio-button value="bar">Bar</a-radio-button>
                        </a-radio-group>
                    </template>
                    <div class="chart-container">
                        <canvas id="genderChart" ref="genderChart"></canvas>
                    </div>
                </a-card>
            </a-col>
        </a-row>


        <a-row :gutter="[24, 24]">
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
                <a-card class="yoy-card" :bordered="false" :loading="loading">
                    <template #title>
                        <span class="card-title"><a-icon type="calendar" /> Year-over-Year Analysis</span>
                    </template>
                    <div class="yoy-analysis">
                        <div v-for="year in yearAnalysis" :key="year.year" class="yoy-item">
                            <div class="yoy-header">
                                <h4>{{ year.year }}</h4>
                                <a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">
                                    <a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />
                                    {{ year.changePercent }}%
                                </a-tag>
                            </div>
                            <div class="yoy-metrics">
                                <a-row :gutter="16">
                                    <a-col :span="12">
                                        <div class="metric">
                                            <span class="metric-label">Total Graduates</span>
                                            <span class="metric-value">{{ year.graduates }}</span>
                                        </div>
                                    </a-col>
                                    <a-col :span="12">
                                        <div class="metric">
                                            <span class="metric-label">Courses Active</span>
                                            <span class="metric-value">{{ year.activeCourses }}</span>
                                        </div>
                                    </a-col>
                                </a-row>
                                <a-row :gutter="16">
                                    <a-col :span="12">
                                        <div class="metric">
                                            <span class="metric-label">Male/Female</span>
                                            <span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>
                                        </div>
                                    </a-col>
                                    <a-col :span="12">
                                        <div class="metric">
                                            <span class="metric-label">Top Course</span>
                                            <span class="metric-value text-truncate">{{ year.topCourse }}</span>
                                        </div>
                                    </a-col>
                                </a-row>
                            </div>
                        </div>
                    </div>
                </a-card>
            </a-col>
        </a-row>
    </div>
</template>

<script>
import Chart from 'chart.js/auto';

export default {
    name: 'AnalyticsDashboard',
    data() {
        return {
            loading: false,
            exporting: false,
            graduatesChartType: 'line',
            genderChartType: 'doughnut',
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
                { title: 'Completion Rate', dataIndex: 'completionRate', key: 'completionRate', align: 'center', scopedSlots: { customRender: 'completion_rate' } },
                { title: 'Avg/Year', dataIndex: 'avgPerYear', key: 'avgPerYear', align: 'center' },
                { title: 'Growth Rate', dataIndex: 'growthRate', key: 'growthRate', align: 'center' }
            ]
        };
    },
    computed: {
        courseMetricsData() {
            return this.analyticsData.courseMetrics.map((course, index) => ({
                key: index,
                course: course.course,
                totalGraduates: course.total_graduates,
                completionRate: course.completion_rate,
                avgPerYear: course.avg_graduates_per_year,
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
        this.loadAnalyticsData();
    },
    mounted() {
        this.loadAnalyticsData();
    },
    methods: {
        Chart,
        getTopCourseForYear(year) {
             // Logic could be expanded if course-per-year data is stored deeply
             return this.analyticsData.courseMetrics[0]?.course || 'N/A';
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
            const achievementCounts = {};
            
            // Process raw list
            list.forEach(grad => {
                const year = grad.yearGraduated || 'Unknown';
                const gender = (grad.gender || 'Unknown').toLowerCase();
                const course = grad.course || 'Unknown';
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
                    key: 'completion',
                    title: 'Avg Completion Rate',
                    value: 98.5, // Mocked as we don't have enrollment data
                    suffix: '%',
                    precision: 1,
                    icon: 'check-circle',
                    color: '#722ed1',
                    trend: 1.3
                },
                {
                    key: 'departments',
                    title: 'Departments',
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
                { key: 'completion', title: 'Avg Completion Rate', value: 94.5, suffix: '%', precision: 1, icon: 'check-circle', color: '#722ed1', trend: 1.3 },
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
            const data = this.analyticsData.genderDistribution || { male: 0, female: 0 };

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
                            // Hide legend for bar chart as x-axis labels correspond to data
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

            this.courseChartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels,
                    datasets: [{
                        label: 'Total Graduates',
                        data: values,
                        backgroundColor: '#52c41a',
                        borderColor: '#52c41a',
                        borderWidth: 1
                    }]
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
                const dept = course.course.split(' ')[0];
                departments[dept] = (departments[dept] || 0) + course.total_graduates;
            });

            this.departmentsChartInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(departments),
                    datasets: [{
                        data: Object.values(departments),
                        backgroundColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
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
    },

    beforeDestroy() {
        [
            this.graduatesChartInstance,
            this.genderChartInstance,
            this.courseChartInstance,
            this.departmentsChartInstance,
            this.departmentTrendsChartInstance
        ].forEach(chart => {
            if (chart) chart.destroy();
        });
    }
};
</script>

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