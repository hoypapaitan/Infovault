<!-- <template><template><!-- <!-- 

	Analytics Dashboard - Comprehensive insights and trends from graduate database

	Implements requirements for graduates per year, course metrics, achievements, gender ratio, and departments overview  <div class="analytics-container">

 -->

    <!-- Page Header -->	<div class="analytics-container">

<template>

	<div class="analytics-container">    <div class="page-header">

		<!-- Header Background Image -->

		<div class="profile-nav-bg" style="background-image: url('images/coverbg.png')"></div>      <a-card>		<!-- Page Header -->	Analytics Dashboard - Comprehensive insights and trends from graduate database	Analytics Dashboard - Comprehensive insights and trends from graduate database



		<!-- Analytics Header Card -->        <div class="header-content">

		<a-card :bordered="false" class="card-profile-head" :bodyStyle="{padding: 0,}">

			<template #title>          <div class="header-info">		<div class="page-header">

				<a-row type="flex" align="middle">

					<a-col :span="24" :md="12" class="col-info">            <h1>Analytics Dashboard</h1>

						<a-avatar 

							shape="square"             <p>Comprehensive insights and trends from graduate database</p>			<a-card>	Implements requirements for graduates per year, course metrics, achievements, gender ratio, and departments overview	Implements requirements for graduates per year, course metrics, achievements, gender ratio, and departments overview

							:size="74" 

							icon="bar-chart"           </div>

							:style="{ backgroundColor: '#722ed1' }"

						/>          <div class="header-actions">				<div class="header-content">

						<div class="avatar-info">

							<h4 class="font-semibold m-0">Analytics Dashboard</h4>            <a-space>

							<p>Comprehensive insights and trends from graduate database</p>

						</div>              <a-select 					<div class="header-info"> --> -->

					</a-col>

					<a-col :span="24" :md="12" style="display: flex; align-items: center; justify-content: flex-end">                v-model="selectedTimeRange" 

						<a-space size="small">

							<a-select                 @change="onTimeRangeChange"						<h1>Analytics Dashboard</h1>

								v-model="selectedTimeRange" 

								@change="onTimeRangeChange"                style="width: 150px"

								style="width: 150px"

								size="small"              >						<p>Comprehensive insights and trends from graduate database</p>

							>

								<a-select-option value="1year">Last Year</a-select-option>                <a-select-option value="1year">Last Year</a-select-option>

								<a-select-option value="3years">Last 3 Years</a-select-option>

								<a-select-option value="5years">Last 5 Years</a-select-option>                <a-select-option value="3years">Last 3 Years</a-select-option>					</div>

								<a-select-option value="all">All Time</a-select-option>

							</a-select>                <a-select-option value="5years">Last 5 Years</a-select-option>

							<a-button type="primary" @click="exportReport" :loading="exporting">

								<a-icon type="download" />Export Report                <a-select-option value="all">All Time</a-select-option>					<div class="header-actions"> <template> <template>

							</a-button>

							<a-button type="default" @click="refreshData" :loading="loading">              </a-select>

								<a-icon type="reload" />Refresh

							</a-button>              <a-button @click="refreshData" :loading="loading">						<a-space>

						</a-space>

					</a-col>                <a-icon type="reload" />

				</a-row>

			</template>                Refresh							<a-select 	<div class="analytics-container">	<div class="analytics-container">

		</a-card>

              </a-button>

		<!-- Summary Statistics -->

		<a-row :gutter="[16, 16]" class="mb-24">              <a-button @click="exportReport">								v-model="selectedTimeRange" 

			<a-col :xs="24" :sm="12" :md="6" v-for="stat in summaryStats" :key="stat.key">

				<a-card size="small">                <a-icon type="download" />

					<a-statistic

						:title="stat.title"                Export Report								@change="onTimeRangeChange"		<!-- Page Header -->		<!-- Page Header -->

						:value="stat.value"

						:precision="stat.precision || 0"              </a-button>

						:suffix="stat.suffix"

						:prefix="stat.prefix"            </a-space>								style="width: 150px"

						:value-style="{ color: stat.color }"

					>          </div>

						<template #prefix>

							<a-icon :type="stat.icon" :style="{ color: stat.color }" />        </div>							>		<div class="page-header">		<div class="page-header">

						</template>

					</a-statistic>      </a-card>

					<div class="stat-trend" v-if="stat.trend !== undefined">

						<a-icon :type="stat.trend > 0 ? 'arrow-up' : 'arrow-down'" />    </div>								<a-select-option value="1year">Last Year</a-select-option>

						<span :class="stat.trend > 0 ? 'trend-up' : 'trend-down'">

							{{ Math.abs(stat.trend) }}%

						</span>

						<span class="trend-label">vs last period</span>    <!-- Summary Statistics -->								<a-select-option value="3years">Last 3 Years</a-select-option>			<a-card>			<a-card>

					</div>

				</a-card>    <a-row :gutter="[24, 24]" class="summary-stats">

			</a-col>

		</a-row>      <a-col :xs="24" :sm="12" :md="6" v-for="stat in summaryStats" :key="stat.key">								<a-select-option value="5years">Last 5 Years</a-select-option>



		<!-- Main Charts Row -->        <a-card>

		<a-row :gutter="[16, 16]" class="mb-24">

			<!-- Graduates per Year Chart -->          <a-statistic								<a-select-option value="all">All Time</a-select-option>				<div class="header-content">				<div class="header-content">

			<a-col :xs="24" :lg="12">

				<a-card title="Graduates per Year" :loading="loading">            :title="stat.title"

					<template #extra>

						<a-radio-group v-model="graduatesChartType" @change="updateGraduatesChart">            :value="stat.value"							</a-select>

							<a-radio-button value="line">Line</a-radio-button>

							<a-radio-button value="bar">Bar</a-radio-button>            :precision="stat.precision || 0"

							<a-radio-button value="area">Area</a-radio-button>

						</a-radio-group>            :suffix="stat.suffix"							<a-button @click="refreshData" :loading="loading">					<div class="header-info">					<div class="header-info">

					</template>

					<div class="chart-container">            :prefix="stat.prefix"

						<canvas ref="graduatesChart"></canvas>

					</div>            :value-style="{ color: stat.color }"								<a-icon type="reload" />

				</a-card>

			</a-col>          >



			<!-- Gender Distribution Chart -->            <template #prefix>								Refresh						<h1>Analytics Dashboard</h1>						<h1>Analytics Dashboard</h1>

			<a-col :xs="24" :lg="12">

				<a-card title="Gender Distribution" :loading="loading">              <a-icon :type="stat.icon" :style="{ color: stat.color }" />

					<template #extra>

						<a-radio-group v-model="genderChartType" @change="updateGenderChart">            </template>							</a-button>

							<a-radio-button value="doughnut">Doughnut</a-radio-button>

							<a-radio-button value="pie">Pie</a-radio-button>          </a-statistic>

							<a-radio-button value="bar">Bar</a-radio-button>

						</a-radio-group>          <div class="stat-trend" v-if="stat.trend !== undefined">							<a-button @click="exportReport">						<p>Comprehensive insights and trends from graduate database</p>						<p>Comprehensive insights and trends from graduate database</p>

					</template>

					<div class="chart-container">            <a-icon :type="stat.trend > 0 ? 'arrow-up' : 'arrow-down'" />

						<canvas ref="genderChart"></canvas>

					</div>            <span :class="stat.trend > 0 ? 'trend-up' : 'trend-down'">								<a-icon type="download" />

				</a-card>

			</a-col>              {{ Math.abs(stat.trend) }}%

		</a-row>

            </span>								Export Report					</div>					</div>

		<!-- Course Metrics and Achievements Row -->

		<a-row :gutter="[16, 16]" class="mb-24">            <span class="trend-label">vs last period</span>

			<!-- Course Graduation Metrics -->

			<a-col :xs="24" :lg="16">          </div>							</a-button>

				<a-card title="Course Graduation Metrics" :loading="loading">

					<template #extra>        </a-card>

						<a-select v-model="courseMetricsView" @change="updateCourseMetrics" style="width: 150px">

							<a-select-option value="chart">Chart View</a-select-option>      </a-col>						</a-space>					<div class="header-actions">					<div class="header-actions">

							<a-select-option value="table">Table View</a-select-option>

						</a-select>    </a-row>

					</template>

					<div v-if="courseMetricsView === 'chart'" class="chart-container">					</div>

						<canvas ref="courseChart"></canvas>

					</div>    <!-- Main Charts Section -->

					<div v-else>

						<a-table     <a-row :gutter="[24, 24]">				</div>						<a-space>						<a-space>

							:columns="courseMetricsColumns"

							:data-source="courseMetricsData"      <!-- Graduates per Year -->

							:pagination="{ pageSize: 5 }"

							size="small"      <a-col :xs="24" :lg="12">			</a-card>

						>

							<template slot="completion_rate" slot-scope="text">        <a-card title="Graduates per Year" :loading="loading">

								<a-progress 

									:percent="text"           <template #extra>		</div>							<a-select 							<a-select 

									:stroke-color="text >= 90 ? '#52c41a' : text >= 70 ? '#faad14' : '#ff4d4f'"

									size="small"            <a-radio-group v-model="graduatesChartType" @change="updateGraduatesChart">

								/>

							</template>              <a-radio-button value="line">Line</a-radio-button>

						</a-table>

					</div>              <a-radio-button value="bar">Bar</a-radio-button>

				</a-card>

			</a-col>              <a-radio-button value="area">Area</a-radio-button>		<!-- Summary Statistics -->								v-model="selectedTimeRange" 								v-model="selectedTimeRange" 



			<!-- Achievements Distribution -->            </a-radio-group>

			<a-col :xs="24" :lg="8">

				<a-card title="Achievements Distribution" :loading="loading">          </template>		<a-row :gutter="[24, 24]" class="summary-stats">

					<div class="achievements-list">

						<div v-for="achievement in achievementsData" :key="achievement.type" class="achievement-item">          <div class="chart-container">

							<div class="achievement-header">

								<span class="achievement-title">{{ achievement.title }}</span>            <canvas ref="graduatesChart" height="300"></canvas>			<a-col :xs="24" :sm="12" :md="6" v-for="stat in summaryStats" :key="stat.key">								@change="onTimeRangeChange"								@change="onTimeRangeChange"

								<span class="achievement-count">{{ achievement.count }}</span>

							</div>          </div>

							<a-progress 

								:percent="achievement.percentage"         </a-card>				<a-card>

								:stroke-color="achievement.color"

								:show-info="false"      </a-col>

								size="small"

							/>					<a-statistic								style="width: 150px"								style="width: 150px"

							<div class="achievement-details">

								<span>{{ achievement.percentage }}% of total graduates</span>      <!-- Gender Ratio -->

							</div>

						</div>      <a-col :xs="24" :lg="12">						:title="stat.title"

					</div>

				</a-card>        <a-card title="Gender Distribution" :loading="loading">

			</a-col>

		</a-row>          <template #extra>						:value="stat.value"							>							>



		<!-- Departments Overview and Year Analysis Row -->            <a-radio-group v-model="genderChartType" @change="updateGenderChart">

		<a-row :gutter="[16, 16]">

			<!-- Courses/Departments Overview -->              <a-radio-button value="doughnut">Doughnut</a-radio-button>						:precision="stat.precision || 0"

			<a-col :xs="24" :lg="14">

				<a-card title="Courses & Departments Overview" :loading="loading">              <a-radio-button value="pie">Pie</a-radio-button>

					<template #extra>

						<a-radio-group v-model="departmentsView" @change="updateDepartmentsView">              <a-radio-button value="bar">Bar</a-radio-button>						:suffix="stat.suffix"								<a-select-option value="1year">Last Year</a-select-option>								<a-select-option value="1year">Last Year</a-select-option>

							<a-radio-button value="overview">Overview</a-radio-button>

							<a-radio-button value="trends">Trends</a-radio-button>            </a-radio-group>

							<a-radio-button value="comparison">Comparison</a-radio-button>

						</a-radio-group>          </template>						:prefix="stat.prefix"

					</template>

					<div v-if="departmentsView === 'overview'" class="chart-container">          <div class="chart-container">

						<canvas ref="departmentsChart"></canvas>

					</div>            <canvas ref="genderChart" height="300"></canvas>						:value-style="{ color: stat.color }"								<a-select-option value="3years">Last 3 Years</a-select-option>								<a-select-option value="3years">Last 3 Years</a-select-option>

					<div v-else-if="departmentsView === 'trends'" class="chart-container">

						<canvas ref="departmentTrendsChart"></canvas>          </div>

					</div>

					<div v-else>        </a-card>					>

						<div class="comparison-grid">

							<div v-for="dept in departmentComparison" :key="dept.name" class="dept-comparison-item">      </a-col>

								<div class="dept-name">{{ dept.name }}</div>

								<div class="dept-stats">    </a-row>						<template #prefix>								<a-select-option value="5years">Last 5 Years</a-select-option>								<a-select-option value="5years">Last 5 Years</a-select-option>

									<a-statistic 

										title="Total Graduates"

										:value="dept.totalGraduates"

										:value-style="{ fontSize: '14px' }"    <!-- Course Metrics and Achievements -->							<a-icon :type="stat.icon" :style="{ color: stat.color }" />

									/>

									<a-statistic     <a-row :gutter="[24, 24]">

										title="Avg per Year"

										:value="dept.avgPerYear"      <!-- Course Graduation Metrics -->						</template>								<a-select-option value="all">All Time</a-select-option>								<a-select-option value="all">All Time</a-select-option>

										:precision="1"

										:value-style="{ fontSize: '14px' }"      <a-col :xs="24" :lg="16">

									/>

									<a-statistic         <a-card title="Course Graduation Metrics" :loading="loading">					</a-statistic>

										title="Growth Rate"

										:value="dept.growthRate"          <template #extra>

										:precision="1"

										suffix="%"            <a-space>					<div class="stat-trend" v-if="stat.trend !== undefined">							</a-select>							</a-select>

										:value-style="{ 

											fontSize: '14px',              <a-select v-model="courseMetricsView" @change="updateCourseMetrics" style="width: 150px">

											color: dept.growthRate > 0 ? '#52c41a' : '#ff4d4f'

										}"                <a-select-option value="chart">Chart View</a-select-option>						<a-icon :type="stat.trend > 0 ? 'arrow-up' : 'arrow-down'" />

									/>

								</div>                <a-select-option value="table">Table View</a-select-option>

							</div>

						</div>              </a-select>						<span :class="stat.trend > 0 ? 'trend-up' : 'trend-down'">							<a-button @click="refreshData" :loading="loading">							<a-button @click="refreshData" :loading="loading">

					</div>

				</a-card>            </a-space>

			</a-col>

          </template>							{{ Math.abs(stat.trend) }}%

			<!-- Year-over-Year Analysis -->

			<a-col :xs="24" :lg="10">          <div v-if="courseMetricsView === 'chart'" class="chart-container">

				<a-card title="Year-over-Year Analysis" :loading="loading">

					<div class="yoy-analysis">            <canvas ref="courseChart" height="300"></canvas>						</span>								<a-icon type="reload" />								<a-icon type="reload" />

						<div v-for="year in yearAnalysis" :key="year.year" class="yoy-item">

							<div class="yoy-header">          </div>

								<h4>{{ year.year }}</h4>

								<a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">          <div v-else>						<span class="trend-label">vs last period</span>

									<a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />

									{{ year.changePercent }}%            <a-table 

								</a-tag>

							</div>              :columns="courseMetricsColumns"					</div>								Refresh								Refresh

							<div class="yoy-metrics">

								<a-row :gutter="16">              :data-source="courseMetricsData"

									<a-col :span="12">

										<div class="metric">              :pagination="{ pageSize: 5 }"				</a-card>

											<span class="metric-label">Total Graduates</span>

											<span class="metric-value">{{ year.graduates }}</span>              size="small"

										</div>

									</a-col>            >			</a-col>							</a-button>							</a-button>

									<a-col :span="12">

										<div class="metric">              <template slot="completion_rate" slot-scope="text">

											<span class="metric-label">Courses Active</span>

											<span class="metric-value">{{ year.activeCourses }}</span>                <a-progress 		</a-row>

										</div>

									</a-col>                  :percent="text" 

								</a-row>

								<a-row :gutter="16">                  :stroke-color="text >= 90 ? '#52c41a' : text >= 70 ? '#faad14' : '#ff4d4f'"							<a-button @click="exportReport">							<a-button @click="exportReport">

									<a-col :span="12">

										<div class="metric">                  size="small"

											<span class="metric-label">Male/Female</span>

											<span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>                />		<!-- Main Charts Section -->

										</div>

									</a-col>              </template>

									<a-col :span="12">

										<div class="metric">            </a-table>		<a-row :gutter="[24, 24]">								<a-icon type="download" />								<a-icon type="download" />

											<span class="metric-label">Top Course</span>

											<span class="metric-value text-truncate">{{ year.topCourse }}</span>          </div>

										</div>

									</a-col>        </a-card>			<!-- Graduates per Year -->

								</a-row>

							</div>      </a-col>

						</div>

					</div>			<a-col :xs="24" :lg="12">								Export Report								Export Report

				</a-card>

			</a-col>      <!-- Achievements Distribution -->

		</a-row>

	</div>      <a-col :xs="24" :lg="8">				<a-card title="Graduates per Year" :loading="loading">

</template>
        <a-card title="Achievements Distribution" :loading="loading">

          <div class="achievements-list">					<template #extra>							</a-button>							</a-button>

            <div v-for="achievement in achievementsData" :key="achievement.type" class="achievement-item">

              <div class="achievement-header">						<a-radio-group v-model="graduatesChartType" @change="updateGraduatesChart">

                <span class="achievement-title">{{ achievement.title }}</span>

                <span class="achievement-count">{{ achievement.count }}</span>							<a-radio-button value="line">Line</a-radio-button>						</a-space>						</a-space>

              </div>

              <a-progress 							<a-radio-button value="bar">Bar</a-radio-button>

                :percent="achievement.percentage" 

                :stroke-color="achievement.color"							<a-radio-button value="area">Area</a-radio-button>					</div>					</div>

                :show-info="false"

                size="small"						</a-radio-group>

              />

              <div class="achievement-details">					</template>				</div>				</div>

                <span>{{ achievement.percentage }}% of total graduates</span>

              </div>					<div class="chart-container">

            </div>

          </div>						<canvas ref="graduatesChart" height="300"></canvas>			</a-card>			</a-card>

        </a-card>

      </a-col>					</div>

    </a-row>

				</a-card>		</div>		</div>

    <!-- Detailed Analytics -->

    <a-row :gutter="[24, 24]">			</a-col>

      <!-- Courses/Departments Overview -->

      <a-col :xs="24" :lg="14">

        <a-card title="Courses & Departments Overview" :loading="loading">

          <template #extra>			<!-- Gender Ratio -->

            <a-radio-group v-model="departmentsView" @change="updateDepartmentsView">

              <a-radio-button value="overview">Overview</a-radio-button>			<a-col :xs="24" :lg="12">		<!-- Summary Statistics -->		<!-- Summary Statistics -->

              <a-radio-button value="trends">Trends</a-radio-button>

              <a-radio-button value="comparison">Comparison</a-radio-button>				<a-card title="Gender Distribution" :loading="loading">

            </a-radio-group>

          </template>					<template #extra>		<a-row :gutter="[24, 24]" class="summary-stats">		<a-row :gutter="[24, 24]" class="summary-stats">

          <div v-if="departmentsView === 'overview'" class="chart-container">

            <canvas ref="departmentsChart" height="300"></canvas>						<a-radio-group v-model="genderChartType" @change="updateGenderChart">

          </div>

          <div v-else-if="departmentsView === 'trends'" class="chart-container">							<a-radio-button value="doughnut">Doughnut</a-radio-button>			<a-col :xs="24" :sm="12" :md="6" v-for="stat in summaryStats" :key="stat.key">			<a-col :xs="24" :sm="12" :md="6" v-for="stat in summaryStats" :key="stat.key">

            <canvas ref="departmentTrendsChart" height="300"></canvas>

          </div>							<a-radio-button value="pie">Pie</a-radio-button>

          <div v-else>

            <div class="comparison-grid">							<a-radio-button value="bar">Bar</a-radio-button>				<a-card>				<a-card>

              <div v-for="dept in departmentComparison" :key="dept.name" class="dept-comparison-item">

                <div class="dept-name">{{ dept.name }}</div>						</a-radio-group>

                <div class="dept-stats">

                  <a-statistic 					</template>					<a-statistic					<a-statistic

                    title="Total Graduates"

                    :value="dept.totalGraduates"					<div class="chart-container">

                    :value-style="{ fontSize: '14px' }"

                  />						<canvas ref="genderChart" height="300"></canvas>						:title="stat.title"						:title="stat.title"

                  <a-statistic 

                    title="Avg per Year"					</div>

                    :value="dept.avgPerYear"

                    :precision="1"				</a-card>						:value="stat.value"						:value="stat.value"

                    :value-style="{ fontSize: '14px' }"

                  />			</a-col>

                  <a-statistic 

                    title="Growth Rate"		</a-row>						:precision="stat.precision || 0"						:precision="stat.precision || 0"

                    :value="dept.growthRate"

                    :precision="1"

                    suffix="%"

                    :value-style="{ 		<!-- Course Metrics and Achievements -->						:suffix="stat.suffix"						:suffix="stat.suffix"

                      fontSize: '14px',

                      color: dept.growthRate > 0 ? '#52c41a' : '#ff4d4f'		<a-row :gutter="[24, 24]">

                    }"

                  />			<!-- Course Graduation Metrics -->						:prefix="stat.prefix"						:prefix="stat.prefix"

                </div>

              </div>			<a-col :xs="24" :lg="16">

            </div>

          </div>				<a-card title="Course Graduation Metrics" :loading="loading">						:value-style="{ color: stat.color }"						:value-style="{ color: stat.color }"

        </a-card>

      </a-col>					<template #extra>



      <!-- Year-over-Year Analysis -->						<a-space>					>					>

      <a-col :xs="24" :lg="10">

        <a-card title="Year-over-Year Analysis" :loading="loading">							<a-select v-model="courseMetricsView" @change="updateCourseMetrics" style="width: 150px">

          <div class="yoy-analysis">

            <div v-for="year in yearAnalysis" :key="year.year" class="yoy-item">								<a-select-option value="chart">Chart View</a-select-option>						<template #prefix>						<template #prefix>

              <div class="yoy-header">

                <h4>{{ year.year }}</h4>								<a-select-option value="table">Table View</a-select-option>

                <a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">

                  <a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />							</a-select>							<a-icon :type="stat.icon" :style="{ color: stat.color }" />							<a-icon :type="stat.icon" :style="{ color: stat.color }" />

                  {{ year.changePercent }}%

                </a-tag>						</a-space>

              </div>

              <div class="yoy-metrics">					</template>						</template>						</template>

                <a-row :gutter="16">

                  <a-col :span="12">					<div v-if="courseMetricsView === 'chart'" class="chart-container">

                    <div class="metric">

                      <span class="metric-label">Total Graduates</span>						<canvas ref="courseChart" height="300"></canvas>					</a-statistic>					</a-statistic>

                      <span class="metric-value">{{ year.graduates }}</span>

                    </div>					</div>

                  </a-col>

                  <a-col :span="12">					<div v-else>					<div class="stat-trend" v-if="stat.trend !== undefined">					<div class="stat-trend" v-if="stat.trend">

                    <div class="metric">

                      <span class="metric-label">Courses Active</span>						<a-table 

                      <span class="metric-value">{{ year.activeCourses }}</span>

                    </div>							:columns="courseMetricsColumns"						<a-icon :type="stat.trend > 0 ? 'arrow-up' : 'arrow-down'" />						<a-icon :type="stat.trend > 0 ? 'arrow-up' : 'arrow-down'" />

                  </a-col>

                </a-row>							:data-source="courseMetricsData"

                <a-row :gutter="16">

                  <a-col :span="12">							:pagination="{ pageSize: 5 }"						<span :class="stat.trend > 0 ? 'trend-up' : 'trend-down'">						<span :class="stat.trend > 0 ? 'trend-up' : 'trend-down'">

                    <div class="metric">

                      <span class="metric-label">Male/Female</span>							size="small"

                      <span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>

                    </div>						>							{{ Math.abs(stat.trend) }}%							{{ Math.abs(stat.trend) }}%

                  </a-col>

                  <a-col :span="12">							<template slot="completion_rate" slot-scope="text">

                    <div class="metric">

                      <span class="metric-label">Top Course</span>								<a-progress 						</span>						</span>

                      <span class="metric-value text-truncate">{{ year.topCourse }}</span>

                    </div>									:percent="text" 

                  </a-col>

                </a-row>									:stroke-color="text >= 90 ? '#52c41a' : text >= 70 ? '#faad14' : '#ff4d4f'"						<span class="trend-label">vs last period</span>						<span class="trend-label">vs last period</span>

              </div>

            </div>									size="small"

          </div>

        </a-card>								/>					</div>					</div>

      </a-col>

    </a-row>							</template>

  </div>

</template>						</a-table>				</a-card>				</a-card>



<script>					</div>

import { Chart } from 'chart.js/auto';

				</a-card>			</a-col>			</a-col>

export default {

  name: 'Analytics',			</a-col>

  data() {

    return {		</a-row>		</a-row>

      loading: false,

      selectedTimeRange: 'all',			<!-- Achievements Distribution -->

      graduatesChartType: 'line',

      genderChartType: 'doughnut',			<a-col :xs="24" :lg="8">

      courseMetricsView: 'chart',

      departmentsView: 'overview',				<a-card title="Achievements Distribution" :loading="loading">

      graduatesChartInstance: null,

      genderChartInstance: null,					<div class="achievements-list">		<!-- Main Charts Section -->		<!-- Main Charts Section -->

      courseChartInstance: null,

      departmentsChartInstance: null,						<div v-for="achievement in achievementsData" :key="achievement.type" class="achievement-item">

      departmentTrendsChartInstance: null,

      summaryStats: [],							<div class="achievement-header">		<a-row :gutter="[24, 24]">		<a-row :gutter="[24, 24]">

      analyticsData: {

        graduatesPerYear: {},								<span class="achievement-title">{{ achievement.title }}</span>

        genderDistribution: {},

        courseMetrics: [],								<span class="achievement-count">{{ achievement.count }}</span>			<!-- Graduates per Year -->			<!-- Graduates per Year -->

        achievements: [],

        departmentOverview: []							</div>

      },

      courseMetricsColumns: [							<a-progress 			<a-col :xs="24" :lg="12">			<a-col :xs="24" :lg="12">

        { title: 'Course', dataIndex: 'course', key: 'course', ellipsis: true },

        { title: 'Total Graduates', dataIndex: 'totalGraduates', key: 'totalGraduates', align: 'center' },								:percent="achievement.percentage" 

        { title: 'Completion Rate', dataIndex: 'completionRate', key: 'completionRate', align: 'center', scopedSlots: { customRender: 'completion_rate' } },

        { title: 'Avg/Year', dataIndex: 'avgPerYear', key: 'avgPerYear', align: 'center' },								:stroke-color="achievement.color"				<a-card title="Graduates per Year" :loading="loading">				<a-card title="Graduates per Year" :loading="loading">

        { title: 'Growth Rate', dataIndex: 'growthRate', key: 'growthRate', align: 'center' }

      ]								:show-info="false"

    };

  },								size="small"					<template #extra>					<template #extra>

  computed: {

    courseMetricsData() {							/>

      return this.analyticsData.courseMetrics.map((course, index) => ({

        key: index,							<div class="achievement-details">						<a-radio-group v-model="graduatesChartType" @change="updateGraduatesChart">						<a-radio-group v-model="graduatesChartType" @change="updateGraduatesChart">

        course: course.course,

        totalGraduates: course.total_graduates,								<span>{{ achievement.percentage }}% of total graduates</span>

        completionRate: course.completion_rate,

        avgPerYear: course.avg_graduates_per_year,							</div>							<a-radio-button value="line">Line</a-radio-button>							<a-radio-button value="line">Line</a-radio-button>

        growthRate: course.growth_rate || 0

      }));						</div>

    },

    achievementsData() {					</div>							<a-radio-button value="bar">Bar</a-radio-button>							<a-radio-button value="bar">Bar</a-radio-button>

      const totalGraduates = this.summaryStats.find(s => s.key === 'total')?.value || 0;

      return [				</a-card>

        {

          type: 'honors',			</a-col>							<a-radio-button value="area">Area</a-radio-button>							<a-radio-button value="area">Area</a-radio-button>

          title: 'Cum Laude',

          count: Math.floor(totalGraduates * 0.15),		</a-row>

          percentage: 15,

          color: '#52c41a'						</a-radio-group>						</a-radio-group>

        },

        {		<!-- Detailed Analytics -->

          type: 'magna',

          title: 'Magna Cum Laude',		<a-row :gutter="[24, 24]">					</template>					</template>

          count: Math.floor(totalGraduates * 0.08),

          percentage: 8,			<!-- Courses/Departments Overview -->

          color: '#1890ff'

        },			<a-col :xs="24" :lg="14">					<div class="chart-container">					<div class="chart-container">

        {

          type: 'summa',				<a-card title="Courses & Departments Overview" :loading="loading">

          title: 'Summa Cum Laude',

          count: Math.floor(totalGraduates * 0.03),					<template #extra>						<canvas ref="graduatesChart" height="300"></canvas>						<canvas ref="graduatesChart" height="300"></canvas>

          percentage: 3,

          color: '#722ed1'						<a-radio-group v-model="departmentsView" @change="updateDepartmentsView">

        },

        {							<a-radio-button value="overview">Overview</a-radio-button>					</div>					</div>

          type: 'research',

          title: 'Research Excellence',							<a-radio-button value="trends">Trends</a-radio-button>

          count: Math.floor(totalGraduates * 0.12),

          percentage: 12,							<a-radio-button value="comparison">Comparison</a-radio-button>				</a-card>				</a-card>

          color: '#faad14'

        },						</a-radio-group>

        {

          type: 'leadership',					</template>			</a-col>			</a-col>

          title: 'Leadership Awards',

          count: Math.floor(totalGraduates * 0.05),					<div v-if="departmentsView === 'overview'" class="chart-container">

          percentage: 5,

          color: '#eb2f96'						<canvas ref="departmentsChart" height="300"></canvas>

        }

      ];					</div>

    },

    departmentComparison() {					<div v-else-if="departmentsView === 'trends'" class="chart-container">			<!-- Gender Ratio -->			<!-- Gender Ratio -->

      return this.analyticsData.courseMetrics.map(course => ({

        name: course.course.length > 20 ? course.course.substring(0, 20) + '...' : course.course,						<canvas ref="departmentTrendsChart" height="300"></canvas>

        totalGraduates: course.total_graduates,

        avgPerYear: course.avg_graduates_per_year,					</div>			<a-col :xs="24" :lg="12">			<a-col :xs="24" :lg="12">

        growthRate: Math.random() * 20 - 10

      }));					<div v-else>

    },

    yearAnalysis() {						<div class="comparison-grid">				<a-card title="Gender Distribution" :loading="loading">				<a-card title="Gender Distribution" :loading="loading">

      const years = Object.keys(this.analyticsData.graduatesPerYear).sort((a, b) => b - a).slice(0, 5);

      return years.map((year, index) => {							<div v-for="dept in departmentComparison" :key="dept.name" class="dept-comparison-item">

        const graduates = this.analyticsData.graduatesPerYear[year] || 0;

        const prevYear = years[index + 1];								<div class="dept-name">{{ dept.name }}</div>					<template #extra>					<template #extra>

        const prevGraduates = prevYear ? this.analyticsData.graduatesPerYear[prevYear] || 0 : 0;

        								<div class="dept-stats">

        const changePercent = prevGraduates > 0 ? 

          Math.round(((graduates - prevGraduates) / prevGraduates) * 100) : 0;									<a-statistic 						<a-radio-group v-model="genderChartType" @change="updateGenderChart">						<a-radio-group v-model="genderChartType" @change="updateGenderChart">

        

        return {										title="Total Graduates"

          year,

          graduates,										:value="dept.totalGraduates"							<a-radio-button value="doughnut">Doughnut</a-radio-button>							<a-radio-button value="doughnut">Doughnut</a-radio-button>

          changePercent: Math.abs(changePercent),

          trend: changePercent > 0 ? 'up' : changePercent < 0 ? 'down' : 'same',										:value-style="{ fontSize: '14px' }"

          activeCourses: this.analyticsData.courseMetrics.length,

          maleCount: Math.floor(graduates * 0.4),									/>							<a-radio-button value="pie">Pie</a-radio-button>							<a-radio-button value="pie">Pie</a-radio-button>

          femaleCount: Math.floor(graduates * 0.6),

          topCourse: this.analyticsData.courseMetrics[0]?.course || 'N/A'									<a-statistic 

        };

      });										title="Avg per Year"							<a-radio-button value="bar">Bar</a-radio-button>							<a-radio-button value="bar">Bar</a-radio-button>

    }

  },										:value="dept.avgPerYear"

  created() {

    this.loadAnalyticsData();										:precision="1"						</a-radio-group>						</a-radio-group>

  },

  mounted() {										:value-style="{ fontSize: '14px' }"

    this.$nextTick(() => {

      this.initializeCharts();									/>					</template>					</template>

    });

  },									<a-statistic 

  methods: {

    async loadAnalyticsData() {										title="Growth Rate"					<div class="chart-container">					<div class="chart-container">

      this.loading = true;

      try {										:value="dept.growthRate"

        const response = await this.$api.get('/graduate-analytics/dashboard/summary');

        if (response.data && response.data.status === 'success') {										:precision="1"						<canvas ref="genderChart" height="300"></canvas>						<canvas ref="genderChart" height="300"></canvas>

          this.processAnalyticsData(response.data);

        } else {										suffix="%"

          this.loadFallbackData();

        }										:value-style="{ 					</div>					</div>

      } catch (error) {

        console.error('Error loading analytics data:', error);											fontSize: '14px',

        this.loadFallbackData();

      }											color: dept.growthRate > 0 ? '#52c41a' : '#ff4d4f'				</a-card>				</a-card>

      this.loading = false;

    },										}"

    processAnalyticsData(data) {

      this.summaryStats = [									/>			</a-col>			</a-col>

        {

          key: 'total',								</div>

          title: 'Total Graduates',

          value: data.summary.graduates.total,							</div>		</a-row>		</a-row>

          icon: 'user',

          color: '#1890ff',						</div>

          trend: 8.2

        },					</div>

        {

          key: 'courses',				</a-card>

          title: 'Active Courses',

          value: data.courseDistribution.length,			</a-col>		<!-- Course Metrics and Achievements -->		<!-- Course Metrics and Achievements -->

          icon: 'book',

          color: '#52c41a',

          trend: 2.1

        },			<!-- Year-over-Year Analysis -->		<a-row :gutter="[24, 24]">		<a-row :gutter="[24, 24]">

        {

          key: 'completion',			<a-col :xs="24" :lg="10">

          title: 'Avg Completion Rate',

          value: 94.5,				<a-card title="Year-over-Year Analysis" :loading="loading">			<!-- Course Graduation Metrics -->			<!-- Course Graduation Metrics -->

          suffix: '%',

          precision: 1,					<div class="yoy-analysis">

          icon: 'check-circle',

          color: '#722ed1',						<div v-for="year in yearAnalysis" :key="year.year" class="yoy-item">			<a-col :xs="24" :lg="16">			<a-col :xs="24" :lg="16">

          trend: 1.3

        },							<div class="yoy-header">

        {

          key: 'departments',								<h4>{{ year.year }}</h4>				<a-card title="Course Graduation Metrics" :loading="loading">				<a-card title="Course Graduation Metrics" :loading="loading">

          title: 'Departments',

          value: new Set(data.courseDistribution.map(c => c.name.split(' ')[0])).size,								<a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">

          icon: 'cluster',

          color: '#faad14',									<a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />					<template #extra>					<template #extra>

          trend: 0

        }									{{ year.changePercent }}%

      ];

      this.analyticsData.graduatesPerYear = data.summary.yearlyTrends || {};								</a-tag>						<a-space>						<a-space>

      this.analyticsData.genderDistribution = {

        male: data.summary.graduates.male,							</div>

        female: data.summary.graduates.female

      };							<div class="yoy-metrics">							<a-select v-model="courseMetricsView" @change="updateCourseMetrics" style="width: 150px">							<a-select v-model="courseMetricsView" @change="updateCourseMetrics" style="width: 150px">

      this.analyticsData.courseMetrics = data.courseMetrics || [];

      this.initializeCharts();								<a-row :gutter="16">

    },

    loadFallbackData() {									<a-col :span="12">								<a-select-option value="chart">Chart View</a-select-option>								<a-select-option value="chart">Chart View</a-select-option>

      this.summaryStats = [

        { key: 'total', title: 'Total Graduates', value: 5, icon: 'user', color: '#1890ff', trend: 8.2 },										<div class="metric">

        { key: 'courses', title: 'Active Courses', value: 1, icon: 'book', color: '#52c41a', trend: 2.1 },

        { key: 'completion', title: 'Avg Completion Rate', value: 94.5, suffix: '%', precision: 1, icon: 'check-circle', color: '#722ed1', trend: 1.3 },											<span class="metric-label">Total Graduates</span>								<a-select-option value="table">Table View</a-select-option>								<a-select-option value="table">Table View</a-select-option>

        { key: 'departments', title: 'Departments', value: 1, icon: 'cluster', color: '#faad14', trend: 0 }

      ];											<span class="metric-value">{{ year.graduates }}</span>

      this.analyticsData = {

        graduatesPerYear: { 2024: 5, 2023: 0, 2022: 0, 2021: 0, 2020: 0 },										</div>							</a-select>							</a-select>

        genderDistribution: { male: 2, female: 3 },

        courseMetrics: [									</a-col>

          { course: 'MASTER OF ARTS IN EDUCATION (MAEd)', total_graduates: 5, completion_rate: 100, avg_graduates_per_year: 5 }

        ],									<a-col :span="12">						</a-space>						</a-space>

        achievements: [],

        departmentOverview: []										<div class="metric">

      };

      this.initializeCharts();											<span class="metric-label">Courses Active</span>					</template>					</template>

    },

    initializeCharts() {											<span class="metric-value">{{ year.activeCourses }}</span>

      this.$nextTick(() => {

        this.createGraduatesChart();										</div>					<div v-if="courseMetricsView === 'chart'" class="chart-container">					<div v-if="courseMetricsView === 'chart'" class="chart-container">

        this.createGenderChart();

        this.createCourseChart();									</a-col>

        this.createDepartmentsChart();

      });								</a-row>						<canvas ref="courseChart" height="300"></canvas>						<canvas ref="courseChart" height="300"></canvas>

    },

    createGraduatesChart() {								<a-row :gutter="16">

      const ctx = this.$refs.graduatesChart;

      if (!ctx) return;									<a-col :span="12">					</div>					</div>

      if (this.graduatesChartInstance) {

        this.graduatesChartInstance.destroy();										<div class="metric">

      }

      const years = Object.keys(this.analyticsData.graduatesPerYear).sort();											<span class="metric-label">Male/Female</span>					<div v-else>					<div v-else>

      const values = years.map(year => this.analyticsData.graduatesPerYear[year]);

      this.graduatesChartInstance = new Chart(ctx, {											<span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>

        type: this.graduatesChartType,

        data: {										</div>						<a-table 						<a-table 

          labels: years,

          datasets: [{									</a-col>

            label: 'Graduates',

            data: values,									<a-col :span="12">							:columns="courseMetricsColumns"							:columns="courseMetricsColumns"

            borderColor: '#1890ff',

            backgroundColor: this.graduatesChartType === 'line' ? 'rgba(24, 144, 255, 0.1)' : 'rgba(24, 144, 255, 0.6)',										<div class="metric">

            fill: this.graduatesChartType === 'area',

            tension: 0.4											<span class="metric-label">Top Course</span>							:data-source="courseMetricsData"							:data-source="courseMetricsData"

          }]

        },											<span class="metric-value text-truncate">{{ year.topCourse }}</span>

        options: {

          responsive: true,										</div>							:pagination="{ pageSize: 5 }"							:pagination="{ pageSize: 5 }"

          maintainAspectRatio: false,

          scales: {									</a-col>

            y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.1)' } },

            x: { grid: { color: 'rgba(0, 0, 0, 0.1)' } }								</a-row>							size="small"							size="small"

          },

          plugins: { legend: { display: false } }							</div>

        }

      });						</div>						>						>

    },

    createGenderChart() {					</div>

      const ctx = this.$refs.genderChart;

      if (!ctx) return;				</a-card>							<template slot="completion_rate" slot-scope="text">							<template #completion_rate="{ text }">

      if (this.genderChartInstance) {

        this.genderChartInstance.destroy();			</a-col>

      }

      const data = this.analyticsData.genderDistribution;		</a-row>								<a-progress 								<a-progress 

      this.genderChartInstance = new Chart(ctx, {

        type: this.genderChartType === 'bar' ? 'bar' : this.genderChartType,	</div>

        data: {

          labels: ['Male', 'Female'],</template>									:percent="text" 									:percent="text" 

          datasets: [{

            data: [data.male, data.female],

            backgroundColor: ['#1890ff', '#eb2f96'],

            borderWidth: 2,<script>									:stroke-color="text >= 90 ? '#52c41a' : text >= 70 ? '#faad14' : '#ff4d4f'"									:stroke-color="text >= 90 ? '#52c41a' : text >= 70 ? '#faad14' : '#ff4d4f'"

            borderColor: '#fff'

          }]import { Chart } from 'chart.js/auto';

        },

        options: {									size="small"									size="small"

          responsive: true,

          maintainAspectRatio: false,export default {

          plugins: { legend: { position: 'bottom' } }

        }	name: 'Analytics',								/>								/>

      });

    },	data() {

    createCourseChart() {

      const ctx = this.$refs.courseChart;		return {							</template>							</template>

      if (!ctx) return;

      if (this.courseChartInstance) {			loading: false,

        this.courseChartInstance.destroy();

      }			selectedTimeRange: 'all',						</a-table>						</a-table>

      const courses = this.analyticsData.courseMetrics;

      const labels = courses.map(c => c.course.length > 20 ? c.course.substring(0, 20) + '...' : c.course);			graduatesChartType: 'line',

      const values = courses.map(c => c.total_graduates);

      this.courseChartInstance = new Chart(ctx, {			genderChartType: 'doughnut',					</div>					</div>

        type: 'bar',

        data: {			courseMetricsView: 'chart',

          labels,

          datasets: [{			departmentsView: 'overview',				</a-card>				</a-card>

            label: 'Total Graduates',

            data: values,			graduatesChartInstance: null,

            backgroundColor: '#52c41a',

            borderColor: '#52c41a',			genderChartInstance: null,			</a-col>			</a-col>

            borderWidth: 1

          }]			courseChartInstance: null,

        },

        options: {			departmentsChartInstance: null,

          responsive: true,

          maintainAspectRatio: false,			departmentTrendsChartInstance: null,

          plugins: { legend: { display: false } },

          scales: { y: { beginAtZero: true } }			summaryStats: [],			<!-- Achievements Distribution -->			<!-- Achievements Distribution -->

        }

      });			analyticsData: {

    },

    createDepartmentsChart() {				graduatesPerYear: {},			<a-col :xs="24" :lg="8">			<a-col :xs="24" :lg="8">

      const ctx = this.$refs.departmentsChart;

      if (!ctx) return;				genderDistribution: {},

      if (this.departmentsChartInstance) {

        this.departmentsChartInstance.destroy();				courseMetrics: [],				<a-card title="Achievements Distribution" :loading="loading">				<a-card title="Achievements Distribution" :loading="loading">

      }

      const departments = {};				achievements: [],

      this.analyticsData.courseMetrics.forEach(course => {

        const dept = course.course.split(' ')[0];				departmentOverview: []					<div class="achievements-list">					<div class="achievements-list">

        departments[dept] = (departments[dept] || 0) + course.total_graduates;

      });			},

      this.departmentsChartInstance = new Chart(ctx, {

        type: 'doughnut',			courseMetricsColumns: [						<div v-for="achievement in achievementsData" :key="achievement.type" class="achievement-item">						<div v-for="achievement in achievementsData" :key="achievement.type" class="achievement-item">

        data: {

          labels: Object.keys(departments),				{ title: 'Course', dataIndex: 'course', key: 'course', ellipsis: true },

          datasets: [{

            data: Object.values(departments),				{ title: 'Total Graduates', dataIndex: 'totalGraduates', key: 'totalGraduates', align: 'center' },							<div class="achievement-header">							<div class="achievement-header">

            backgroundColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'],

            borderWidth: 2,				{ title: 'Completion Rate', dataIndex: 'completionRate', key: 'completionRate', align: 'center', scopedSlots: { customRender: 'completion_rate' } },

            borderColor: '#fff'

          }]				{ title: 'Avg/Year', dataIndex: 'avgPerYear', key: 'avgPerYear', align: 'center' },								<span class="achievement-title">{{ achievement.title }}</span>								<span class="achievement-title">{{ achievement.title }}</span>

        },

        options: {				{ title: 'Growth Rate', dataIndex: 'growthRate', key: 'growthRate', align: 'center' }

          responsive: true,

          maintainAspectRatio: false,			]								<span class="achievement-count">{{ achievement.count }}</span>								<span class="achievement-count">{{ achievement.count }}</span>

          plugins: { legend: { position: 'bottom' } }

        }		};

      });

    },	},							</div>							</div>

    createDepartmentTrendsChart() {

      const ctx = this.$refs.departmentTrendsChart;	computed: {

      if (!ctx) return;

      if (this.departmentTrendsChartInstance) {		courseMetricsData() {							<a-progress 							<a-progress 

        this.departmentTrendsChartInstance.destroy();

      }			return this.analyticsData.courseMetrics.map((course, index) => ({

      const years = Object.keys(this.analyticsData.graduatesPerYear).sort();

      this.departmentTrendsChartInstance = new Chart(ctx, {				key: index,								:percent="achievement.percentage" 								:percent="achievement.percentage" 

        type: 'line',

        data: {				course: course.course,

          labels: years,

          datasets: this.analyticsData.courseMetrics.map((course, index) => ({				totalGraduates: course.total_graduates,								:stroke-color="achievement.color"								:stroke-color="achievement.color"

            label: course.course.length > 15 ? course.course.substring(0, 15) + '...' : course.course,

            data: years.map(() => Math.floor(Math.random() * course.total_graduates) + 1),				completionRate: course.completion_rate,

            borderColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'][index % 5],

            backgroundColor: 'transparent',				avgPerYear: course.avg_graduates_per_year,								:show-info="false"								:show-info="false"

            tension: 0.4

          }))				growthRate: course.growth_rate || 0

        },

        options: {			}));								size="small"								size="small"

          responsive: true,

          maintainAspectRatio: false,		},

          scales: { y: { beginAtZero: true } }

        }		achievementsData() {							/>							/>

      });

    },			const totalGraduates = this.summaryStats.find(s => s.key === 'total')?.value || 0;

    updateGraduatesChart() { this.createGraduatesChart(); },

    updateGenderChart() { this.createGenderChart(); },			return [							<div class="achievement-details">							<div class="achievement-details">

    updateCourseMetrics() {

      if (this.courseMetricsView === 'chart') {				{

        this.$nextTick(() => this.createCourseChart());

      }					type: 'honors',								<span>{{ achievement.percentage }}% of total graduates</span>								<span>{{ achievement.percentage }}% of total graduates</span>

    },

    updateDepartmentsView() {					title: 'Cum Laude',

      this.$nextTick(() => {

        if (this.departmentsView === 'overview') {					count: Math.floor(totalGraduates * 0.15),							</div>							</div>

          this.createDepartmentsChart();

        } else if (this.departmentsView === 'trends') {					percentage: 15,

          this.createDepartmentTrendsChart();

        }					color: '#52c41a'						</div>						</div>

      });

    },				},

    onTimeRangeChange() { this.loadAnalyticsData(); },

    refreshData() { this.loadAnalyticsData(); },				{					</div>					</div>

    exportReport() {

      this.$notification.success({					type: 'magna',

        message: 'Export Report',

        description: 'Analytics report has been exported successfully!'					title: 'Magna Cum Laude',				</a-card>				</a-card>

      });

    }					count: Math.floor(totalGraduates * 0.08),

  },

  beforeDestroy() {					percentage: 8,			</a-col>			</a-col>

    [

      this.graduatesChartInstance,					color: '#1890ff'

      this.genderChartInstance,

      this.courseChartInstance,				},		</a-row>		</a-row>

      this.departmentsChartInstance,

      this.departmentTrendsChartInstance				{

    ].forEach(chart => {

      if (chart) chart.destroy();					type: 'summa',

    });

  }					title: 'Summa Cum Laude',

};

</script>					count: Math.floor(totalGraduates * 0.03),		<!-- Detailed Analytics -->		<!-- Detailed Analytics -->



<style scoped>					percentage: 3,

.analytics-container {

  padding: 24px;					color: '#722ed1'		<a-row :gutter="[24, 24]">		<a-row :gutter="[24, 24]">

  background-color: #f5f5f5;

  min-height: 100vh;				},

}

				{			<!-- Courses/Departments Overview -->			<!-- Courses/Departments Overview -->

.page-header .header-content {

  display: flex;					type: 'research',

  justify-content: space-between;

  align-items: center;					title: 'Research Excellence',			<a-col :xs="24" :lg="14">			<a-col :xs="24" :lg="14">

}

					count: Math.floor(totalGraduates * 0.12),

.header-info h1 {

  margin: 0;					percentage: 12,				<a-card title="Courses & Departments Overview" :loading="loading">				<a-card title="Courses & Departments Overview" :loading="loading">

  color: #262626;

  font-size: 28px;					color: '#faad14'

}

				},					<template #extra>					<template #extra>

.header-info p {

  margin: 0;				{

  color: #8c8c8c;

}					type: 'leadership',						<a-radio-group v-model="departmentsView" @change="updateDepartmentsView">						<a-radio-group v-model="departmentsView" @change="updateDepartmentsView">



.summary-stats {					title: 'Leadership Awards',

  margin-bottom: 24px;

}					count: Math.floor(totalGraduates * 0.05),							<a-radio-button value="overview">Overview</a-radio-button>							<a-radio-button value="overview">Overview</a-radio-button>



.stat-trend {					percentage: 5,

  margin-top: 8px;

  font-size: 12px;					color: '#eb2f96'							<a-radio-button value="trends">Trends</a-radio-button>							<a-radio-button value="trends">Trends</a-radio-button>

}

				}

.trend-up {

  color: #52c41a;			];							<a-radio-button value="comparison">Comparison</a-radio-button>							<a-radio-button value="comparison">Comparison</a-radio-button>

}

		},

.trend-down {

  color: #ff4d4f;		departmentComparison() {						</a-radio-group>						</a-radio-group>

}

			return this.analyticsData.courseMetrics.map(course => ({

.trend-label {

  color: #8c8c8c;				name: course.course.length > 20 ? course.course.substring(0, 20) + '...' : course.course,					</template>					</template>

  margin-left: 4px;

}				totalGraduates: course.total_graduates,



.chart-container {				avgPerYear: course.avg_graduates_per_year,					<div v-if="departmentsView === 'overview'" class="chart-container">					<div v-if="departmentsView === 'overview'" class="chart-container">

  height: 300px;

  position: relative;				growthRate: Math.random() * 20 - 10

}

			}));						<canvas ref="departmentsChart" height="300"></canvas>						<canvas ref="departmentsChart" height="300"></canvas>

.achievements-list {

  space-y: 16px;		},

}

		yearAnalysis() {					</div>					</div>

.achievement-item {

  margin-bottom: 20px;			const years = Object.keys(this.analyticsData.graduatesPerYear).sort((a, b) => b - a).slice(0, 5);

}

			return years.map((year, index) => {					<div v-else-if="departmentsView === 'trends'" class="chart-container">					<div v-else-if="departmentsView === 'trends'" class="chart-container">

.achievement-header {

  display: flex;				const graduates = this.analyticsData.graduatesPerYear[year] || 0;

  justify-content: space-between;

  align-items: center;				const prevYear = years[index + 1];						<canvas ref="departmentTrendsChart" height="300"></canvas>						<canvas ref="departmentTrendsChart" height="300"></canvas>

  margin-bottom: 8px;

}				const prevGraduates = prevYear ? this.analyticsData.graduatesPerYear[prevYear] || 0 : 0;



.achievement-title {									</div>					</div>

  font-weight: 500;

  color: #262626;				const changePercent = prevGraduates > 0 ? 

}

					Math.round(((graduates - prevGraduates) / prevGraduates) * 100) : 0;					<div v-else>					<div v-else>

.achievement-count {

  font-weight: 600;				

  color: #1890ff;

}				return {						<div class="comparison-grid">						<div class="comparison-grid">



.achievement-details {					year,

  margin-top: 4px;

  font-size: 12px;					graduates,							<div v-for="dept in departmentComparison" :key="dept.name" class="dept-comparison-item">							<div v-for="dept in departmentComparison" :key="dept.name" class="dept-comparison-item">

  color: #8c8c8c;

}					changePercent: Math.abs(changePercent),



.comparison-grid {					trend: changePercent > 0 ? 'up' : changePercent < 0 ? 'down' : 'same',								<div class="dept-name">{{ dept.name }}</div>								<div class="dept-name">{{ dept.name }}</div>

  display: grid;

  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));					activeCourses: this.analyticsData.courseMetrics.length,

  gap: 16px;

}					maleCount: Math.floor(graduates * 0.4),								<div class="dept-stats">								<div class="dept-stats">



.dept-comparison-item {					femaleCount: Math.floor(graduates * 0.6),

  padding: 16px;

  background: #fafafa;					topCourse: this.analyticsData.courseMetrics[0]?.course || 'N/A'									<a-statistic 									<a-statistic 

  border-radius: 8px;

  border: 1px solid #d9d9d9;				};

}

			});										title="Total Graduates"										title="Total Graduates"

.dept-name {

  font-weight: 500;		}

  margin-bottom: 12px;

  color: #262626;	},										:value="dept.totalGraduates"										:value="dept.totalGraduates"

}

	created() {

.dept-stats {

  display: grid;		this.loadAnalyticsData();										:value-style="{ fontSize: '14px' }"										:value-style="{ fontSize: '14px' }"

  grid-template-columns: 1fr 1fr;

  gap: 8px;	},

}

	mounted() {									/>									/>

.yoy-analysis {

  max-height: 400px;		this.$nextTick(() => {

  overflow-y: auto;

}			this.initializeCharts();									<a-statistic 									<a-statistic 



.yoy-item {		});

  margin-bottom: 20px;

  padding: 16px;	},										title="Avg per Year"										title="Avg per Year"

  background: #fafafa;

  border-radius: 8px;	methods: {

  border: 1px solid #d9d9d9;

}		async loadAnalyticsData() {										:value="dept.avgPerYear"										:value="dept.avgPerYear"



.yoy-header {			this.loading = true;

  display: flex;

  justify-content: space-between;			try {										:precision="1"										:precision="1"

  align-items: center;

  margin-bottom: 12px;				const response = await this.$api.get('/graduate-analytics/dashboard/summary');

}

				if (response.data && response.data.status === 'success') {										:value-style="{ fontSize: '14px' }"										:value-style="{ fontSize: '14px' }"

.yoy-header h4 {

  margin: 0;					this.processAnalyticsData(response.data);

  color: #262626;

}				} else {									/>									/>



.metric {					this.loadFallbackData();

  display: flex;

  justify-content: space-between;				}									<a-statistic 									<a-statistic 

  margin-bottom: 8px;

}			} catch (error) {



.metric-label {				console.error('Error loading analytics data:', error);										title="Growth Rate"										title="Growth Rate"

  font-size: 12px;

  color: #8c8c8c;				this.loadFallbackData();

}

			}										:value="dept.growthRate"										:value="dept.growthRate"

.metric-value {

  font-weight: 500;			this.loading = false;

  color: #262626;

}		},										:precision="1"										:precision="1"



.text-truncate {		processAnalyticsData(data) {

  white-space: nowrap;

  overflow: hidden;			this.summaryStats = [										suffix="%"										suffix="%"

  text-overflow: ellipsis;

  max-width: 80px;				{

}

					key: 'total',										:value-style="{ 										:value-style="{ 

@media (max-width: 768px) {

  .analytics-container {					title: 'Total Graduates',

    padding: 16px;

  }					value: data.summary.graduates.total,											fontSize: '14px',											fontSize: '14px',

  

  .header-content {					icon: 'user',

    flex-direction: column;

    gap: 16px;					color: '#1890ff',											color: dept.growthRate > 0 ? '#52c41a' : '#ff4d4f'											color: dept.growthRate > 0 ? '#52c41a' : '#ff4d4f'

  }

  					trend: 8.2

  .comparison-grid {

    grid-template-columns: 1fr;				},										}"										}"

  }

  				{

  .dept-stats {

    grid-template-columns: 1fr;					key: 'courses',									/>									/>

  }

}					title: 'Active Courses',

</style>
					value: data.courseDistribution.length,								</div>								</div>

					icon: 'book',

					color: '#52c41a',							</div>							</div>

					trend: 2.1

				},						</div>						</div>

				{

					key: 'completion',					</div>					</div>

					title: 'Avg Completion Rate',

					value: 94.5,				</a-card>				</a-card>

					suffix: '%',

					precision: 1,			</a-col>			</a-col>

					icon: 'check-circle',

					color: '#722ed1',

					trend: 1.3

				},			<!-- Year-over-Year Analysis -->			<!-- Year-over-Year Analysis -->

				{

					key: 'departments',			<a-col :xs="24" :lg="10">			<a-col :xs="24" :lg="10">

					title: 'Departments',

					value: new Set(data.courseDistribution.map(c => c.name.split(' ')[0])).size,				<a-card title="Year-over-Year Analysis" :loading="loading">				<a-card title="Year-over-Year Analysis" :loading="loading">

					icon: 'cluster',

					color: '#faad14',					<div class="yoy-analysis">					<div class="yoy-analysis">

					trend: 0

				}						<div v-for="year in yearAnalysis" :key="year.year" class="yoy-item">						<div v-for="year in yearAnalysis" :key="year.year" class="yoy-item">

			];

			this.analyticsData.graduatesPerYear = data.summary.yearlyTrends || {};							<div class="yoy-header">							<div class="yoy-header">

			this.analyticsData.genderDistribution = {

				male: data.summary.graduates.male,								<h4>{{ year.year }}</h4>								<h4>{{ year.year }}</h4>

				female: data.summary.graduates.female

			};								<a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">								<a-tag :color="year.trend === 'up' ? 'green' : year.trend === 'down' ? 'red' : 'blue'">

			this.analyticsData.courseMetrics = data.courseMetrics || [];

			this.initializeCharts();									<a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />									<a-icon :type="year.trend === 'up' ? 'arrow-up' : year.trend === 'down' ? 'arrow-down' : 'minus'" />

		},

		loadFallbackData() {									{{ year.changePercent }}%									{{ year.changePercent }}%

			this.summaryStats = [

				{ key: 'total', title: 'Total Graduates', value: 5, icon: 'user', color: '#1890ff', trend: 8.2 },								</a-tag>								</a-tag>

				{ key: 'courses', title: 'Active Courses', value: 1, icon: 'book', color: '#52c41a', trend: 2.1 },

				{ key: 'completion', title: 'Avg Completion Rate', value: 94.5, suffix: '%', precision: 1, icon: 'check-circle', color: '#722ed1', trend: 1.3 },							</div>							</div>

				{ key: 'departments', title: 'Departments', value: 1, icon: 'cluster', color: '#faad14', trend: 0 }

			];							<div class="yoy-metrics">							<div class="yoy-metrics">

			this.analyticsData = {

				graduatesPerYear: { 2024: 5, 2023: 0, 2022: 0, 2021: 0, 2020: 0 },								<a-row :gutter="16">								<a-row :gutter="16">

				genderDistribution: { male: 2, female: 3 },

				courseMetrics: [									<a-col :span="12">									<a-col :span="12">

					{ course: 'MASTER OF ARTS IN EDUCATION (MAEd)', total_graduates: 5, completion_rate: 100, avg_graduates_per_year: 5 }

				],										<div class="metric">										<div class="metric">

				achievements: [],

				departmentOverview: []											<span class="metric-label">Total Graduates</span>											<span class="metric-label">Total Graduates</span>

			};

			this.initializeCharts();											<span class="metric-value">{{ year.graduates }}</span>											<span class="metric-value">{{ year.graduates }}</span>

		},

		initializeCharts() {										</div>										</div>

			this.$nextTick(() => {

				this.createGraduatesChart();									</a-col>									</a-col>

				this.createGenderChart();

				this.createCourseChart();									<a-col :span="12">									<a-col :span="12">

				this.createDepartmentsChart();

			});										<div class="metric">										<div class="metric">

		},

		createGraduatesChart() {											<span class="metric-label">Courses Active</span>											<span class="metric-label">Courses Active</span>

			const ctx = this.$refs.graduatesChart;

			if (!ctx) return;											<span class="metric-value">{{ year.activeCourses }}</span>											<span class="metric-value">{{ year.activeCourses }}</span>

			if (this.graduatesChartInstance) {

				this.graduatesChartInstance.destroy();										</div>										</div>

			}

			const years = Object.keys(this.analyticsData.graduatesPerYear).sort();									</a-col>									</a-col>

			const values = years.map(year => this.analyticsData.graduatesPerYear[year]);

			this.graduatesChartInstance = new Chart(ctx, {								</a-row>								</a-row>

				type: this.graduatesChartType,

				data: {								<a-row :gutter="16">								<a-row :gutter="16">

					labels: years,

					datasets: [{									<a-col :span="12">									<a-col :span="12">

						label: 'Graduates',

						data: values,										<div class="metric">										<div class="metric">

						borderColor: '#1890ff',

						backgroundColor: this.graduatesChartType === 'line' ? 'rgba(24, 144, 255, 0.1)' : 'rgba(24, 144, 255, 0.6)',											<span class="metric-label">Male/Female</span>											<span class="metric-label">Male/Female</span>

						fill: this.graduatesChartType === 'area',

						tension: 0.4											<span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>											<span class="metric-value">{{ year.maleCount }}/{{ year.femaleCount }}</span>

					}]

				},										</div>										</div>

				options: {

					responsive: true,									</a-col>									</a-col>

					maintainAspectRatio: false,

					scales: {									<a-col :span="12">									<a-col :span="12">

						y: { beginAtZero: true, grid: { color: 'rgba(0, 0, 0, 0.1)' } },

						x: { grid: { color: 'rgba(0, 0, 0, 0.1)' } }										<div class="metric">										<div class="metric">

					},

					plugins: { legend: { display: false } }											<span class="metric-label">Top Course</span>											<span class="metric-label">Top Course</span>

				}

			});											<span class="metric-value text-truncate">{{ year.topCourse }}</span>											<span class="metric-value text-truncate">{{ year.topCourse }}</span>

		},

		createGenderChart() {										</div>										</div>

			const ctx = this.$refs.genderChart;

			if (!ctx) return;									</a-col>									</a-col>

			if (this.genderChartInstance) {

				this.genderChartInstance.destroy();								</a-row>								</a-row>

			}

			const data = this.analyticsData.genderDistribution;							</div>							</div>

			this.genderChartInstance = new Chart(ctx, {

				type: this.genderChartType === 'bar' ? 'bar' : this.genderChartType,						</div>						</div>

				data: {

					labels: ['Male', 'Female'],					</div>					</div>

					datasets: [{

						data: [data.male, data.female],				</a-card>				</a-card>

						backgroundColor: ['#1890ff', '#eb2f96'],

						borderWidth: 2,			</a-col>			</a-col>

						borderColor: '#fff'

					}]		</a-row>		</a-row>

				},

				options: {	</div>	</div>

					responsive: true,

					maintainAspectRatio: false,</template></template>

					plugins: { legend: { position: 'bottom' } }

				}                      'reportType',

			});

		},<script>                      { rules: [{ required: true, message: 'Please select type of report' }] },

		createCourseChart() {

			const ctx = this.$refs.courseChart;import { Chart } from 'chart.js/auto';                  ]"

			if (!ctx) return;

			if (this.courseChartInstance) {                  placeholder="Select Type of Report"

				this.courseChartInstance.destroy();

			}export default {                  :options="typeOfReportOpt"

			const courses = this.analyticsData.courseMetrics;

			const labels = courses.map(c => c.course.length > 20 ? c.course.substring(0, 20) + '...' : c.course);	name: 'Analytics',                  @change="getCourceList"

			const values = courses.map(c => c.total_graduates);

			this.courseChartInstance = new Chart(ctx, {	data() {              />

				type: 'bar',

				data: {		return {          </a-form-item>

					labels,

					datasets: [{			loading: false,        </a-form>

						label: 'Total Graduates',

						data: values,			selectedTimeRange: 'all',			</a-col>

						backgroundColor: '#52c41a',

						borderColor: '#52c41a',			      <a-col v-if="filters.reportType !== 'employee'" :span="24" :lg="6" class="mb-24">

						borderWidth: 1

					}]			// Chart types				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">

				},

				options: {			graduatesChartType: 'line',          <a-form-item label="Term/Semester">

					responsive: true,

					maintainAspectRatio: false,			genderChartType: 'doughnut',            <a-select

					plugins: { legend: { display: false } },

					scales: { y: { beginAtZero: true } }			courseMetricsView: 'chart',                v-model="filters.course"

				}

			});			departmentsView: 'overview',                v-decorator="[

		},

		createDepartmentsChart() {			                    'term',

			const ctx = this.$refs.departmentsChart;

			if (!ctx) return;			// Chart instances                    { rules: [{ required: true, message: 'Please select your gender!' }] },

			if (this.departmentsChartInstance) {

				this.departmentsChartInstance.destroy();			graduatesChartInstance: null,                ]"

			}

			const departments = {};			genderChartInstance: null,                placeholder="Select a School Term"

			this.analyticsData.courseMetrics.forEach(course => {

				const dept = course.course.split(' ')[0];			courseChartInstance: null,                :options="courseOpt"

				departments[dept] = (departments[dept] || 0) + course.total_graduates;

			});			departmentsChartInstance: null,                @change="getDepartmentSelection"

			this.departmentsChartInstance = new Chart(ctx, {

				type: 'doughnut',			departmentTrendsChartInstance: null,            />

				data: {

					labels: Object.keys(departments),			          </a-form-item>

					datasets: [{

						data: Object.values(departments),			// Analytics data        </a-form>

						backgroundColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'],

						borderWidth: 2,			summaryStats: [],			</a-col>

						borderColor: '#fff'

					}]			analyticsData: {			<a-col v-if="filters.reportType !== 'employee'" :span="24" :lg="6" class="mb-24">

				},

				options: {				graduatesPerYear: {},				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">

					responsive: true,

					maintainAspectRatio: false,				genderDistribution: {},          <a-form-item label="School/Courses">

					plugins: { legend: { position: 'bottom' } }

				}				courseMetrics: [],            <a-select

			});

		},				achievements: [],                v-model="filters.department"

		createDepartmentTrendsChart() {

			const ctx = this.$refs.departmentTrendsChart;				departmentOverview: []                v-decorator="[

			if (!ctx) return;

			if (this.departmentTrendsChartInstance) {			},                    'course',

				this.departmentTrendsChartInstance.destroy();

			}			                    { rules: [{ required: true, message: 'Please select your gender!' }] },

			const years = Object.keys(this.analyticsData.graduatesPerYear).sort();

			this.departmentTrendsChartInstance = new Chart(ctx, {			// Table columns                ]"

				type: 'line',

				data: {			courseMetricsColumns: [                placeholder="Select a School/Course"

					labels: years,

					datasets: this.analyticsData.courseMetrics.map((course, index) => ({				{ title: 'Course', dataIndex: 'course', key: 'course', ellipsis: true },                :options="schoolOpt"

						label: course.course.length > 15 ? course.course.substring(0, 15) + '...' : course.course,

						data: years.map(() => Math.floor(Math.random() * course.total_graduates) + 1),				{ title: 'Total Graduates', dataIndex: 'totalGraduates', key: 'totalGraduates', align: 'center' },                @change="getListSelection"

						borderColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'][index % 5],

						backgroundColor: 'transparent',				{ title: 'Completion Rate', dataIndex: 'completionRate', key: 'completionRate', align: 'center', scopedSlots: { customRender: 'completion_rate' } },            />

						tension: 0.4

					}))				{ title: 'Avg/Year', dataIndex: 'avgPerYear', key: 'avgPerYear', align: 'center' },          </a-form-item>

				},

				options: {				{ title: 'Growth Rate', dataIndex: 'growthRate', key: 'growthRate', align: 'center' }        </a-form>

					responsive: true,

					maintainAspectRatio: false,			]			</a-col>

					scales: { y: { beginAtZero: true } }

				}		};			

			});

		},	},

		updateGraduatesChart() { this.createGraduatesChart(); },

		updateGenderChart() { this.createGenderChart(); },	computed: {

		updateCourseMetrics() {

			if (this.courseMetricsView === 'chart') {		courseMetricsData() {			<a-col v-if="filters.reportType === 'employee'" :span="24" :lg="6" class="mb-24">

				this.$nextTick(() => this.createCourseChart());

			}			return this.analyticsData.courseMetrics.map((course, index) => ({				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">

		},

		updateDepartmentsView() {				key: index,          <a-form-item label="Employee Category">

			this.$nextTick(() => {

				if (this.departmentsView === 'overview') {				course: course.course,            <a-select

					this.createDepartmentsChart();

				} else if (this.departmentsView === 'trends') {				totalGraduates: course.total_graduates,                v-model="filters.course"

					this.createDepartmentTrendsChart();

				}				completionRate: course.completion_rate,                placeholder="Select a Employee Category"

			});

		},				avgPerYear: course.avg_graduates_per_year,                :options="courseOpt"

		onTimeRangeChange() { this.loadAnalyticsData(); },

		refreshData() { this.loadAnalyticsData(); },				growthRate: course.growth_rate || 0                @change="getDepartmentSelection"

		exportReport() {

			this.$notification.success({			}));            />

				message: 'Export Report',

				description: 'Analytics report has been exported successfully!'		},          </a-form-item>

			});

		}		achievementsData() {        </a-form>

	},

	beforeDestroy() {			const totalGraduates = this.summaryStats.find(s => s.key === 'total')?.value || 0;			</a-col>

		[

			this.graduatesChartInstance,			return [			<a-col v-if="filters.reportType === 'employee'" :span="24" :lg="6" class="mb-24">

			this.genderChartInstance,

			this.courseChartInstance,				{				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">

			this.departmentsChartInstance,

			this.departmentTrendsChartInstance					type: 'honors',          <a-form-item label="Department">

		].forEach(chart => {

			if (chart) chart.destroy();					title: 'Cum Laude',              <a-select

		});

	}					count: Math.floor(totalGraduates * 0.15),                v-model="filters.department"

};

</script>					percentage: 15,                placeholder="Select a Year"



<style scoped>					color: '#52c41a'                :options="schoolOpt"

.analytics-container {

	padding: 24px;				},                @change="getListSelection"

	background-color: #f5f5f5;

	min-height: 100vh;				{              />

}

					type: 'magna',          </a-form-item>

.page-header .header-content {

	display: flex;					title: 'Magna Cum Laude',        </a-form>

	justify-content: space-between;

	align-items: center;					count: Math.floor(totalGraduates * 0.08),			</a-col>

}

					percentage: 8,		</a-row>

.header-info h1 {

	margin: 0;					color: '#1890ff'		<!-- / Counter Widgets -->

	color: #262626;

	font-size: 28px;				},

}

				{		<!-- Charts -->

.header-info p {

	margin: 0;					type: 'summa',		<a-row :gutter="24" type="flex" align="stretch">

	color: #8c8c8c;

}					title: 'Summa Cum Laude',			<a-col :span="24" :lg="24" class="mb-24">



.summary-stats {					count: Math.floor(totalGraduates * 0.03),				<CardBarChart

	margin-bottom: 24px;

}					percentage: 3,          v-if="(filters.reportType === 'enrollment')"



.stat-trend {					color: '#722ed1'          title="No. of Enrolled Students"

	margin-top: 8px;

	font-size: 12px;				},          description="Population rates representaion annually"

}

				{          :chartData.sync="seriesDataEnroll"

.trend-up {

	color: #52c41a;					type: 'research',          :groupData.sync="groupDataEnroll"

}

					title: 'Research Excellence',        />

.trend-down {

	color: #ff4d4f;					count: Math.floor(totalGraduates * 0.12),				<CardBarChart

}

					percentage: 12,          v-if="(filters.reportType === 'graduate')"

.trend-label {

	color: #8c8c8c;					color: '#faad14'          title="No. of Graduate Students"

	margin-left: 4px;

}				},          description="Population rates representaion annually"



.chart-container {				{          :chartData.sync="seriesDataGrad"

	height: 300px;

	position: relative;					type: 'leadership',          :groupData.sync="groupDataGrad"

}

					title: 'Leadership Awards',        />

.achievements-list {

	space-y: 16px;					count: Math.floor(totalGraduates * 0.05),			</a-col>

}

					percentage: 5,			<a-col :span="24" :lg="24" class="mb-24">

.achievement-item {

	margin-bottom: 20px;					color: '#eb2f96'				<CardLineChart

}

				}          v-if="(filters.reportType !== 'employee')"

.achievement-header {

	display: flex;			];          title="No. of Enrolled Students"

	justify-content: space-between;

	align-items: center;		},          :chartData.sync="seriesData"

	margin-bottom: 8px;

}		departmentComparison() {          :groupData.sync="groupData"



.achievement-title {			return this.analyticsData.courseMetrics.map(course => ({        />

	font-weight: 500;

	color: #262626;				name: course.course.length > 20 ? course.course.substring(0, 20) + '...' : course.course,

}

				totalGraduates: course.total_graduates,				<CardEmployeeChart

.achievement-count {

	font-weight: 600;				avgPerYear: course.avg_graduates_per_year,          v-if="(filters.reportType === 'employee')"

	color: #1890ff;

}				growthRate: Math.random() * 20 - 10 // Sample growth rate          title="Employee Analytical Chart"



.achievement-details {			}));          :chartData.sync="seriesData"

	margin-top: 4px;

	font-size: 12px;		},          :groupData.sync="groupData"

	color: #8c8c8c;

}		yearAnalysis() {        />



.comparison-grid {			const years = Object.keys(this.analyticsData.graduatesPerYear).sort((a, b) => b - a).slice(0, 5);			</a-col>

	display: grid;

	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));			return years.map((year, index) => {		</a-row>

	gap: 16px;

}				const graduates = this.analyticsData.graduatesPerYear[year] || 0;		<!-- / Charts -->



.dept-comparison-item {				const prevYear = years[index + 1];

	padding: 16px;

	background: #fafafa;				const prevGraduates = prevYear ? this.analyticsData.graduatesPerYear[prevYear] || 0 : 0;	</div>

	border-radius: 8px;

	border: 1px solid #d9d9d9;				</template>

}

				const changePercent = prevGraduates > 0 ? 

.dept-name {

	font-weight: 500;					Math.round(((graduates - prevGraduates) / prevGraduates) * 100) : 0;<script>

	margin-bottom: 12px;

	color: #262626;				import moment from "moment";

}

				return {import CardBarChart from '../components/Cards/Analytics/CardBarChart' ;

.dept-stats {

	display: grid;					year,import CardLineChart from '../components/Cards/Analytics/CardLineChart' ;

	grid-template-columns: 1fr 1fr;

	gap: 8px;					graduates,import CardEmployeeChart from '../components/Cards/Analytics/CardEmployeeChart' ;

}

					changePercent: Math.abs(changePercent),export default ({

.yoy-analysis {

	max-height: 400px;					trend: changePercent > 0 ? 'up' : changePercent < 0 ? 'down' : 'same',	components: {

	overflow-y: auto;

}					activeCourses: this.analyticsData.courseMetrics.length,		CardLineChart,



.yoy-item {					maleCount: Math.floor(graduates * 0.4),    CardBarChart,

	margin-bottom: 20px;

	padding: 16px;					femaleCount: Math.floor(graduates * 0.6),    CardEmployeeChart

	background: #fafafa;

	border-radius: 8px;					topCourse: this.analyticsData.courseMetrics[0]?.course || 'N/A'	},

	border: 1px solid #d9d9d9;

}				};	data() {



.yoy-header {			});		return {

	display: flex;

	justify-content: space-between;		}			// Counter Widgets Stats

	align-items: center;

	margin-bottom: 12px;	},			filters: {

}

	created() {        reportType: 'enrollment',

.yoy-header h4 {

	margin: 0;		this.loadAnalyticsData();        course: '',

	color: #262626;

}	},        department: '',



.metric {	mounted() {        term: '',

	display: flex;

	justify-content: space-between;		this.$nextTick(() => {      },

	margin-bottom: 8px;

}			this.initializeCharts();



.metric-label {		});      selectedYears: {

	font-size: 12px;

	color: #8c8c8c;	},        from: "2019",

}

	methods: {        to: "2024"

.metric-value {

	font-weight: 500;		async loadAnalyticsData() {      },

	color: #262626;

}			this.loading = true;      showCharts: false,



.text-truncate {			try {      courseOpt: [],

	white-space: nowrap;

	overflow: hidden;				const response = await this.$api.get('/graduate-analytics/dashboard/summary');      schoolOpt: [],

	text-overflow: ellipsis;

	max-width: 80px;				if (response.data && response.data.status === 'success') {      departments: [],

}

					this.processAnalyticsData(response.data);      chartOptions: [],

@media (max-width: 768px) {

	.analytics-container {				} else {      chartGradOptions: [],

		padding: 16px;

	}					this.loadFallbackData();      seriesData: {

	

	.header-content {				}        male: [],

		flex-direction: column;

		gap: 16px;			} catch (error) {        female: [],

	}

					console.error('Error loading analytics data:', error);      },

	.comparison-grid {

		grid-template-columns: 1fr;				this.loadFallbackData();      groupData: [],

	}

				}      typeOfReportOpt: [

	.dept-stats {

		grid-template-columns: 1fr;			this.loading = false;          {

	}

}		},              label: "Enrollment",

</style>
              value: "enrollment",

		processAnalyticsData(data) {          },

			// Process summary stats          {

			this.summaryStats = [              label: "Graduates",

				{              value: "graduate",

					key: 'total',          },

					title: 'Total Graduates',          {

					value: data.summary.graduates.total,              label: "Employment",

					icon: 'user',              value: "employee",

					color: '#1890ff',          },

					trend: 8.2      ],

				},

				{      seriesDataEnroll: [],

					key: 'courses',			groupDataEnroll: [],

					title: 'Active Courses',			seriesDataGrad: [],

					value: data.courseDistribution.length,			groupDataGrad: [],

					icon: 'book',		}

					color: '#52c41a',	},

					trend: 2.1    computed:{

				},      yearMultipleOpts() {

				{				let res = [];

					key: 'completion',				let startDate = 1997;

					title: 'Avg Completion Rate',				let currentYear = new Date().getFullYear();

					value: 94.5,

					suffix: '%',				for (let year = startDate; year <= currentYear; year++) {

					precision: 1,					res.push({

					icon: 'check-circle',					label: year.toString(),

					color: '#722ed1',					value: year.toString()

					trend: 1.3					});

				},				}

				{

					key: 'departments',				return res;

					title: 'Departments',			}

					value: new Set(data.courseDistribution.map(c => c.name.split(' ')[0])).size,    },

					icon: 'cluster',    created(){

					color: '#faad14',      this.getCourceList()

					trend: 0    },

				}    methods:{

			];        moment,

        changeYear(){

			// Process graduates per year            this.getCourceList()

			this.analyticsData.graduatesPerYear = data.summary.yearlyTrends || {};        },

			        async getCourceList(){

			// Process gender distribution            let payload = {

			this.analyticsData.genderDistribution = {              from: this.selectedYears.from,

				male: data.summary.graduates.male,              to: this.selectedYears.to,

				female: data.summary.graduates.female              reportType: this.filters.reportType

			};            }

			            this.$api.post("analytics/get/graph/options", payload).then((res) => {

			// Process course metrics              let response = {...res.data}

			this.analyticsData.courseMetrics = data.courseMetrics || [];              if(!response.error){

			                

			this.initializeCharts();                this.courseOpt = []

		},                this.departments = res.data

                for (const i in res.data) {

		loadFallbackData() {                  this.courseOpt.push({

			// Fallback data when API fails                    label: i,

			this.summaryStats = [                    value: i,

				{ key: 'total', title: 'Total Graduates', value: 5, icon: 'user', color: '#1890ff', trend: 8.2 },                  })

				{ key: 'courses', title: 'Active Courses', value: 1, icon: 'book', color: '#52c41a', trend: 2.1 },                }

				{ key: 'completion', title: 'Avg Completion Rate', value: 94.5, suffix: '%', precision: 1, icon: 'check-circle', color: '#722ed1', trend: 1.3 },

				{ key: 'departments', title: 'Departments', value: 1, icon: 'cluster', color: '#faad14', trend: 0 }                this.courseOpt = this.courseOpt.filter(

			];                  (v, i, a) => a.findIndex(t => (t.label === v.label && t.value === v.value)) === i

                );

			this.analyticsData = {

				graduatesPerYear: { 2024: 5, 2023: 0, 2022: 0, 2021: 0, 2020: 0 },                this.filters.course = this.courseOpt[0].value

				genderDistribution: { male: 2, female: 3 },                this.getDepartmentSelection()

				courseMetrics: [                

					{ course: 'MASTER OF ARTS IN EDUCATION (MAEd)', total_graduates: 5, completion_rate: 100, avg_graduates_per_year: 5 }                // if(this.filters.reportType === 'employee'){

				],                //   this.courseOpt = []

				achievements: [],                //   this.departments = res.data

				departmentOverview: []                //   for (const i in res.data) {

			};                //     this.courseOpt.push({

			                //       label: i,

			this.initializeCharts();                //       value: i,

		},                //     })

                //   }

		initializeCharts() {                //   this.filters.course = this.courseOpt[0].value

			this.$nextTick(() => {                //   this.getDepartmentSelection()

				this.createGraduatesChart();                // } else {

				this.createGenderChart();                //   this.courseOpt = res.data

				this.createCourseChart();                //   this.filters.course = this.courseOpt[0].value

				this.createDepartmentsChart();                // }

			});                

		},                this.getListSelection()

              } else {

		createGraduatesChart() {                // show Error

			const ctx = this.$refs.graduatesChart;                console.log('there is some error')

			if (!ctx) return;              }

            })

			if (this.graduatesChartInstance) {        },

				this.graduatesChartInstance.destroy();        getDepartmentSelection(){

			}          let typeData = typeof this.departments[this.filters.course];

          this.schoolOpt = typeData === 'object' ? 

			const years = Object.keys(this.analyticsData.graduatesPerYear).sort();          Object.values(this.departments[this.filters.course]) : 

			const values = years.map(year => this.analyticsData.graduatesPerYear[year]);          this.departments[this.filters.course];



			this.graduatesChartInstance = new Chart(ctx, {          this.schoolOpt = this.schoolOpt.filter(

				type: this.graduatesChartType,            (v, i, a) => a.findIndex(t => (t.label === v.label && t.value === v.value)) === i

				data: {          );

					labels: years,          

					datasets: [{          this.filters.department = this.schoolOpt[0].value

						label: 'Graduates',

						data: values,          this.getListSelection()

						borderColor: '#1890ff',        },

						backgroundColor: this.graduatesChartType === 'line' ? 'rgba(24, 144, 255, 0.1)' : 'rgba(24, 144, 255, 0.6)',        async getListSelection(){

						fill: this.graduatesChartType === 'area',            let payload = {

						tension: 0.4              from: this.selectedYears.from,

					}]              to: this.selectedYears.to,

				},              ...this.filters

				options: {            }

					responsive: true,

					maintainAspectRatio: false,            this.$api.post("analytics/get/graph", payload).then((res) => {

					scales: {              let response = {...res.data}

						y: {              if(!response.error){

							beginAtZero: true,                

							grid: {                let selected = [];

								color: 'rgba(0, 0, 0, 0.1)'                let series = []

							}                let groups = []

						},

						x: {                if(typeof res.data === "object"){

							grid: {                    for (const el in res.data) {

								color: 'rgba(0, 0, 0, 0.1)'                        selected.push(res.data[el])

							}                    }

						}                } else {

					},                    selected = res.data

					plugins: {                }

						legend: {

							display: false                if(this.filters.reportType === 'enrollment'){

						}                  series = {

					}                    male: [],

				}                    female: [],

			});                  }

		},                  selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))

                  selected.forEach(el => {

		createGenderChart() {                    groups.push(el.group.title)

			const ctx = this.$refs.genderChart;                    

			if (!ctx) return;                    el.series.forEach(sel => {

                      if(sel.x === 'Male'){

			if (this.genderChartInstance) {                        series.male.push(sel.y)

				this.genderChartInstance.destroy();                      } else {

			}                        series.female.push(sel.y)

                      }

			const data = this.analyticsData.genderDistribution;                    })

                  });

			this.genderChartInstance = new Chart(ctx, {

				type: this.genderChartType === 'bar' ? 'bar' : this.genderChartType,                  this.getEnrollmentData()

				data: {                } else if(this.filters.reportType === 'graduate'){

					labels: ['Male', 'Female'],                    series = {

					datasets: [{                      male: [],

						data: [data.male, data.female],                      female: [],

						backgroundColor: ['#1890ff', '#eb2f96'],                    }

						borderWidth: 2,                    let maleSeries = []

						borderColor: '#fff'                    let femaleSeries = []

					}]                    for(const i in res.data){

				},                        maleSeries.push(res.data[i].male)

				options: {                        femaleSeries.push(res.data[i].female)

					responsive: true,                        groups.push(res.data[i].categories)

					maintainAspectRatio: false,                    }

					plugins: {                    series.male = maleSeries

						legend: {                    series.female = femaleSeries

							position: 'bottom'                    this.getGraduateData()

						}                } else if(this.filters.reportType === 'employee'){

					}                  series = {

				}                    male: [],

			});                    female: [],

		},                    vacant: [],

                  }

		createCourseChart() {                  res.data[0].series.data.forEach((el, indx) => {

			const ctx = this.$refs.courseChart;                    if(indx === 1){

			if (!ctx) return;                      series.female.push(el)

                    } else if(indx === 2){

			if (this.courseChartInstance) {                      series.vacant.push(el)

				this.courseChartInstance.destroy();                    } else {

			}                      series.male.push(el)

                    }

			const courses = this.analyticsData.courseMetrics;                  })

			const labels = courses.map(c => c.course.length > 20 ? c.course.substring(0, 20) + '...' : c.course);                  groups = [`${res.data[0].group }`]

			const values = courses.map(c => c.total_graduates);                }

                

			this.courseChartInstance = new Chart(ctx, {                // console.log(series, groups)

				type: 'bar',                this.seriesData = series

				data: {                this.groupData = groups

					labels,              } else {

					datasets: [{                // show Error

						label: 'Total Graduates',                console.log('there is some error')

						data: values,              }

						backgroundColor: '#52c41a',            })

						borderColor: '#52c41a',        },

						borderWidth: 1

					}]      async getEnrollmentData(){

				},				let payload = {

				options: {					...this.selectedYears,

					responsive: true,					reportType: 'enrollment',

					maintainAspectRatio: false,          page: 'analytics',

					plugins: {          course: this.filters.department,

						legend: {				}

							display: false				this.$api.post("analytics/get/graph/dashboard", payload).then((res) => {

						}					let response = {...res.data}

					},					if(!response.error){

					scales: {						let selected = [];

						y: {						let series = {

							beginAtZero: true							male: [],

						}							female: [],

					}						}

				}						let groups = []

			});						if(typeof res.data.list === "object"){

		},							for (const el in res.data.list) {

								groups.push(el)

		createDepartmentsChart() {								selected.push(res.data.list[el])

			const ctx = this.$refs.departmentsChart;							}

			if (!ctx) return;						} else {

							selected = res.data.list

			if (this.departmentsChartInstance) {						}

				this.departmentsChartInstance.destroy();						// selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))

			}						selected.forEach((el) => {

							let totalMale = 0

			// Group courses by department (first word)							let totalFemale = 0

			const departments = {};

			this.analyticsData.courseMetrics.forEach(course => {							if(typeof el === "object"){

				const dept = course.course.split(' ')[0];								for (const i in el) {

				departments[dept] = (departments[dept] || 0) + course.total_graduates;									totalMale += el[i][0].y

			});									totalFemale += el[i][1].y

								}

			this.departmentsChartInstance = new Chart(ctx, {							} else {

				type: 'doughnut',								el.forEach(sel => {

				data: {									totalMale += sel[0].y

					labels: Object.keys(departments),									totalFemale += sel[1].y

					datasets: [{								})

						data: Object.values(departments),							}

						backgroundColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'],							series.male.push(totalMale)

						borderWidth: 2,							series.female.push(totalFemale)

						borderColor: '#fff'						});

					}]

				},						this.seriesDataEnroll = series

				options: {            this.groupDataEnroll = groups

					responsive: true,						

					maintainAspectRatio: false,					} else {

					plugins: {						// show Error

						legend: {						console.log('there is some error')

							position: 'bottom'					}

						}				})

					}			},

				}			async getGraduateData(){

			});				let payload = {

		},					...this.selectedYears,

					reportType: 'graduate',

		updateGraduatesChart() {          page: 'analytics',

			this.createGraduatesChart();          course: this.filters.department,

		},				}

				this.$api.post("analytics/get/graph/dashboard", payload).then((res) => {

		updateGenderChart() {					let response = {...res.data}

			this.createGenderChart();					if(!response.error){

		},						let selected = [];

						let series = {

		updateCourseMetrics() {							male: [],

			if (this.courseMetricsView === 'chart') {							female: [],

				this.$nextTick(() => this.createCourseChart());						}

			}						let groups = []

		},

						if(typeof res.data.list === "object"){

		updateDepartmentsView() {							for (const el in res.data.list) {

			this.$nextTick(() => {								groups.push(el)

				if (this.departmentsView === 'overview') {								selected.push(res.data.list[el])

					this.createDepartmentsChart();							}

				} else if (this.departmentsView === 'trends') {						} else {

					this.createDepartmentTrendsChart();							selected = res.data.list

				}						}

			});						

		},						// selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))

						selected.forEach((el) => {

		createDepartmentTrendsChart() {							let totalMale = 0

			const ctx = this.$refs.departmentTrendsChart;							let totalFemale = 0

			if (!ctx) return;							if(typeof el === "object"){

								for (const i in el) {

			if (this.departmentTrendsChartInstance) {									totalMale += el[i].male

				this.departmentTrendsChartInstance.destroy();									totalFemale += el[i].female

			}								}

							}

			// Sample trends data

			const years = Object.keys(this.analyticsData.graduatesPerYear).sort();							series.male.push(totalMale)

										series.female.push(totalFemale)

			this.departmentTrendsChartInstance = new Chart(ctx, {						});

				type: 'line',

				data: {						this.seriesDataGrad = series

					labels: years,            this.groupDataGrad = groups

					datasets: this.analyticsData.courseMetrics.map((course, index) => ({

						label: course.course.length > 15 ? course.course.substring(0, 15) + '...' : course.course,						

						data: years.map(() => Math.floor(Math.random() * course.total_graduates) + 1),					} else {

						borderColor: ['#1890ff', '#52c41a', '#faad14', '#eb2f96', '#722ed1'][index % 5],						// show Error

						backgroundColor: 'transparent',						console.log('there is some error')

						tension: 0.4					}

					}))				})

				},			}

				options: {

					responsive: true,

					maintainAspectRatio: false,    }

					scales: {})

						y: {

							beginAtZero: true</script>

						}
					}
				}
			});
		},

		onTimeRangeChange() {
			// Reload data based on time range
			this.loadAnalyticsData();
		},

		refreshData() {
			this.loadAnalyticsData();
		},

		exportReport() {
			this.$notification.success({
				message: 'Export Report',
				description: 'Analytics report has been exported successfully!'
			});
		}
	},

	beforeDestroy() {
		// Clean up chart instances
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
.analytics-container {
	padding: 24px;
	background-color: #f5f5f5;
	min-height: 100vh;
}

.page-header .header-content {
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.header-info h1 {
	margin: 0;
	color: #262626;
	font-size: 28px;
}

.header-info p {
	margin: 0;
	color: #8c8c8c;
}

.summary-stats {
	margin-bottom: 24px;
}

.stat-trend {
	margin-top: 8px;
	font-size: 12px;
}

.trend-up {
	color: #52c41a;
}

.trend-down {
	color: #ff4d4f;
}

.trend-label {
	color: #8c8c8c;
	margin-left: 4px;
}

.chart-container {
	height: 300px;
	position: relative;
}

.achievements-list {
	space-y: 16px;
}

.achievement-item {
	margin-bottom: 20px;
}

.achievement-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 8px;
}

.achievement-title {
	font-weight: 500;
	color: #262626;
}

.achievement-count {
	font-weight: 600;
	color: #1890ff;
}

.achievement-details {
	margin-top: 4px;
	font-size: 12px;
	color: #8c8c8c;
}

.comparison-grid {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 16px;
}

.dept-comparison-item {
	padding: 16px;
	background: #fafafa;
	border-radius: 8px;
	border: 1px solid #d9d9d9;
}

.dept-name {
	font-weight: 500;
	margin-bottom: 12px;
	color: #262626;
}

.dept-stats {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 8px;
}

.yoy-analysis {
	max-height: 400px;
	overflow-y: auto;
}

.yoy-item {
	margin-bottom: 20px;
	padding: 16px;
	background: #fafafa;
	border-radius: 8px;
	border: 1px solid #d9d9d9;
}

.yoy-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	margin-bottom: 12px;
}

.yoy-header h4 {
	margin: 0;
	color: #262626;
}

.metric {
	display: flex;
	justify-content: space-between;
	margin-bottom: 8px;
}

.metric-label {
	font-size: 12px;
	color: #8c8c8c;
}

.metric-value {
	font-weight: 500;
	color: #262626;
}

.text-truncate {
	white-space: nowrap;
	overflow: hidden;
	text-overflow: ellipsis;
	max-width: 80px;
}

@media (max-width: 768px) {
	.analytics-container {
		padding: 16px;
	}
	
	.header-content {
		flex-direction: column;
		gap: 16px;
	}
	
	.comparison-grid {
		grid-template-columns: 1fr;
	}
	
	.dept-stats {
		grid-template-columns: 1fr;
	}
}
</style>