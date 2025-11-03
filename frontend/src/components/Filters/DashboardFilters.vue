<!-- Advanced Dashboard Filters Component -->
<template>
  <a-card :bordered="false" class="filter-panel">
    <template #title>
      <a-row type="flex" align="middle">
        <a-col :span="18">
          <h6 class="font-semibold m-0">
            <a-icon type="filter" /> Dashboard Filters
          </h6>
        </a-col>
        <a-col :span="6" style="text-align: right">
          <a-button 
            size="small" 
            type="link" 
            @click="resetFilters"
            :disabled="!hasActiveFilters"
          >
            Reset All
          </a-button>
        </a-col>
      </a-row>
    </template>

    <div class="filters-content">
      <a-row :gutter="16">
        <!-- Year Range Filter -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Year Range" class="mb-0">
            <a-range-picker
              v-model="filters.yearRange"
              mode="year"
              placeholder="['Start Year', 'End Year']"
              format="YYYY"
              @change="onYearRangeChange"
              style="width: 100%"
            />
          </a-form-item>
        </a-col>

        <!-- Course Filter -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Course/School" class="mb-0">
            <a-select
              v-model="filters.course"
              placeholder="Select Course"
              show-search
              allow-clear
              @change="onCourseChange"
              style="width: 100%"
            >
              <a-select-option value="all">All Courses</a-select-option>
              <a-select-option 
                v-for="course in courseOptions" 
                :key="course" 
                :value="course"
              >
                {{ course }}
              </a-select-option>
            </a-select>
          </a-form-item>
        </a-col>

        <!-- Report Type Filter -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Report Type" class="mb-0">
            <a-select
              v-model="filters.reportType"
              placeholder="Select Report Type"
              @change="onReportTypeChange"
              style="width: 100%"
            >
              <a-select-option value="all">All Types</a-select-option>
              <a-select-option value="enrollment">Enrollment</a-select-option>
              <a-select-option value="graduate">Graduates</a-select-option>
            </a-select>
          </a-form-item>
        </a-col>

        <!-- Class Year Filter -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Class Year" class="mb-0">
            <a-select
              v-model="filters.classYear"
              placeholder="Select Class Year"
              allow-clear
              @change="onClassYearChange"
              style="width: 100%"
            >
              <a-select-option value="all">All Years</a-select-option>
              <a-select-option 
                v-for="year in classYearOptions" 
                :key="year" 
                :value="year"
              >
                {{ year }}
              </a-select-option>
            </a-select>
          </a-form-item>
        </a-col>

        <!-- Term Filter -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Term" class="mb-0">
            <a-select
              v-model="filters.term"
              placeholder="Select Term"
              allow-clear
              @change="onTermChange"
              style="width: 100%"
            >
              <a-select-option value="all">All Terms</a-select-option>
              <a-select-option 
                v-for="term in termOptions" 
                :key="term" 
                :value="term"
              >
                {{ term }}
              </a-select-option>
            </a-select>
          </a-form-item>
        </a-col>

        <!-- Gender Filter -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Gender Analysis" class="mb-0">
            <a-select
              v-model="filters.gender"
              placeholder="Select Gender View"
              @change="onGenderChange"
              style="width: 100%"
            >
              <a-select-option value="combined">Combined</a-select-option>
              <a-select-option value="separated">Male/Female</a-select-option>
              <a-select-option value="male">Male Only</a-select-option>
              <a-select-option value="female">Female Only</a-select-option>
            </a-select>
          </a-form-item>
        </a-col>

        <!-- Quick Filters -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16">
          <a-form-item label="Quick Filters" class="mb-0">
            <a-button-group style="width: 100%">
              <a-button 
                :type="filters.quickFilter === 'current-year' ? 'primary' : 'default'"
                @click="setQuickFilter('current-year')"
                size="small"
              >
                Current Year
              </a-button>
              <a-button 
                :type="filters.quickFilter === 'last-5-years' ? 'primary' : 'default'"
                @click="setQuickFilter('last-5-years')"
                size="small"
              >
                Last 5 Years
              </a-button>
            </a-button-group>
          </a-form-item>
        </a-col>

        <!-- Apply Filters Button -->
        <a-col :span="24" :md="12" :lg="6" class="mb-16" style="display: flex; align-items: flex-end;">
          <a-button 
            type="primary" 
            icon="search"
            @click="applyFilters"
            :loading="loading"
            style="width: 100%"
          >
            Apply Filters
          </a-button>
        </a-col>
      </a-row>

      <!-- Active Filters Display -->
      <div v-if="hasActiveFilters" class="active-filters">
        <a-divider>Active Filters</a-divider>
        <a-row :gutter="8">
          <a-col v-for="filter in activeFiltersList" :key="filter.key">
            <a-tag 
              :closable="filter.closable" 
              @close="removeFilter(filter.key)"
              :color="filter.color || 'blue'"
            >
              {{ filter.label }}: {{ filter.value }}
            </a-tag>
          </a-col>
        </a-row>
      </div>
    </div>
  </a-card>
</template>

<script>
import moment from 'moment';

export default {
  name: 'DashboardFilters',
  props: {
    loading: {
      type: Boolean,
      default: false
    },
    courseOptions: {
      type: Array,
      default: () => []
    },
    classYearOptions: {
      type: Array,
      default: () => ['1st Year', '2nd Year', '3rd Year', '4th Year']
    },
    termOptions: {
      type: Array,
      default: () => ['First', 'Second']
    }
  },
  data() {
    const currentYear = moment().year();
    return {
      filters: {
        yearRange: [moment().year(currentYear - 4), moment().year(currentYear)],
        course: 'all',
        reportType: 'all',
        classYear: 'all',
        term: 'all',
        gender: 'combined',
        quickFilter: 'last-5-years'
      }
    };
  },
  computed: {
    hasActiveFilters() {
      return this.filters.course !== 'all' ||
             this.filters.reportType !== 'all' ||
             this.filters.classYear !== 'all' ||
             this.filters.term !== 'all' ||
             this.filters.gender !== 'combined' ||
             this.filters.quickFilter !== 'last-5-years';
    },
    activeFiltersList() {
      const activeFilters = [];
      
      if (this.filters.yearRange && this.filters.yearRange.length === 2) {
        activeFilters.push({
          key: 'yearRange',
          label: 'Year Range',
          value: `${this.filters.yearRange[0].format('YYYY')} - ${this.filters.yearRange[1].format('YYYY')}`,
          closable: false,
          color: 'green'
        });
      }
      
      if (this.filters.course !== 'all') {
        activeFilters.push({
          key: 'course',
          label: 'Course',
          value: this.filters.course,
          closable: true,
          color: 'blue'
        });
      }
      
      if (this.filters.reportType !== 'all') {
        activeFilters.push({
          key: 'reportType',
          label: 'Report Type',
          value: this.filters.reportType,
          closable: true,
          color: 'purple'
        });
      }
      
      if (this.filters.classYear !== 'all') {
        activeFilters.push({
          key: 'classYear',
          label: 'Class Year',
          value: this.filters.classYear,
          closable: true,
          color: 'orange'
        });
      }
      
      if (this.filters.term !== 'all') {
        activeFilters.push({
          key: 'term',
          label: 'Term',
          value: this.filters.term,
          closable: true,
          color: 'cyan'
        });
      }
      
      if (this.filters.gender !== 'combined') {
        activeFilters.push({
          key: 'gender',
          label: 'Gender',
          value: this.filters.gender,
          closable: true,
          color: 'magenta'
        });
      }
      
      return activeFilters;
    }
  },
  methods: {
    onYearRangeChange(dates) {
      this.filters.yearRange = dates;
      this.emitFiltersChange();
    },
    onCourseChange(value) {
      this.filters.course = value;
      this.emitFiltersChange();
    },
    onReportTypeChange(value) {
      this.filters.reportType = value;
      this.emitFiltersChange();
    },
    onClassYearChange(value) {
      this.filters.classYear = value;
      this.emitFiltersChange();
    },
    onTermChange(value) {
      this.filters.term = value;
      this.emitFiltersChange();
    },
    onGenderChange(value) {
      this.filters.gender = value;
      this.emitFiltersChange();
    },
    setQuickFilter(filterType) {
      const currentYear = moment().year();
      this.filters.quickFilter = filterType;
      
      switch (filterType) {
        case 'current-year':
          this.filters.yearRange = [moment().year(currentYear), moment().year(currentYear)];
          break;
        case 'last-5-years':
          this.filters.yearRange = [moment().year(currentYear - 4), moment().year(currentYear)];
          break;
      }
      
      this.emitFiltersChange();
    },
    resetFilters() {
      const currentYear = moment().year();
      this.filters = {
        yearRange: [moment().year(currentYear - 4), moment().year(currentYear)],
        course: 'all',
        reportType: 'all',
        classYear: 'all',
        term: 'all',
        gender: 'combined',
        quickFilter: 'last-5-years'
      };
      this.emitFiltersChange();
    },
    removeFilter(filterKey) {
      switch (filterKey) {
        case 'course':
          this.filters.course = 'all';
          break;
        case 'reportType':
          this.filters.reportType = 'all';
          break;
        case 'classYear':
          this.filters.classYear = 'all';
          break;
        case 'term':
          this.filters.term = 'all';
          break;
        case 'gender':
          this.filters.gender = 'combined';
          break;
      }
      this.emitFiltersChange();
    },
    applyFilters() {
      this.emitFiltersChange();
    },
    emitFiltersChange() {
      // Convert moment objects to strings for API calls
      const filtersForAPI = {
        ...this.filters,
        from: this.filters.yearRange?.[0]?.format('YYYY'),
        to: this.filters.yearRange?.[1]?.format('YYYY')
      };
      
      this.$emit('filters-changed', filtersForAPI);
    },
    getFormattedFilters() {
      return {
        ...this.filters,
        from: this.filters.yearRange?.[0]?.format('YYYY'),
        to: this.filters.yearRange?.[1]?.format('YYYY')
      };
    }
  },
  mounted() {
    // Emit initial filters
    this.emitFiltersChange();
  }
};
</script>

<style lang="scss" scoped>
.filter-panel {
  margin-bottom: 24px;
}

.filters-content {
  .mb-16 {
    margin-bottom: 16px;
  }
  
  .ant-form-item {
    margin-bottom: 0;
    
    .ant-form-item-label {
      line-height: 1.5;
      margin-bottom: 4px;
    }
  }
}

.active-filters {
  margin-top: 16px;
  
  .ant-tag {
    margin-bottom: 8px;
  }
}

@media (max-width: 768px) {
  .filters-content {
    .ant-col {
      margin-bottom: 16px;
    }
  }
}
</style>