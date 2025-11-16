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
        <!-- Course Filter -->
        <a-col :span="24" :md="12" :lg="8" class="mb-16">
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

        <!-- Class Year Filter -->
        <a-col :span="24" :md="12" :lg="8" class="mb-16">
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

        <!-- Apply Filters Button -->
        <a-col :span="24" :md="12" :lg="8" class="mb-16" style="display: flex; align-items: flex-end;">
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
    return {
      filters: {
        course: 'all',
        classYear: 'all'
      }
    };
  },
  computed: {
    hasActiveFilters() {
      return this.filters.course !== 'all' ||
             this.filters.classYear !== 'all';
    },
    activeFiltersList() {
      const activeFilters = [];
      
      if (this.filters.course !== 'all') {
        activeFilters.push({
          key: 'course',
          label: 'Course',
          value: this.filters.course,
          closable: true,
          color: 'blue'
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
      
      return activeFilters;
    }
  },
  methods: {
    onCourseChange(value) {
      this.filters.course = value;
      this.emitFiltersChange();
    },
    onClassYearChange(value) {
      this.filters.classYear = value;
      this.emitFiltersChange();
    },
    resetFilters() {
      this.filters = {
        course: 'all',
        classYear: 'all'
      };
      this.emitFiltersChange();
    },
    removeFilter(filterKey) {
      switch (filterKey) {
        case 'course':
          this.filters.course = 'all';
          break;
        case 'classYear':
          this.filters.classYear = 'all';
          break;
      }
      this.emitFiltersChange();
    },
    applyFilters() {
      this.emitFiltersChange();
    },
    emitFiltersChange() {
      this.$emit('filters-changed', this.filters);
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