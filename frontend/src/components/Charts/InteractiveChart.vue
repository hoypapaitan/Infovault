<!-- Enhanced Interactive Dashboard Chart Component -->
<template>
  <a-card :bordered="false" class="header-solid h-full ant-card-p-0">
    <template #title>
      <a-row type="flex" align="middle">
        <a-col :span="24" :md="12">
          <h6 class="font-semibold m-0">{{ title }}</h6>
          <p class="text-muted">{{ description }}</p>
        </a-col>
        <a-col :span="24" :md="12" class="col-info">
          <a-select
            v-if="showTypeSelector"
            v-model="selectedChartType"
            style="width: 120px"
            @change="onChartTypeChange"
          >
            <a-select-option value="line">Line</a-select-option>
            <a-select-option value="bar">Bar</a-select-option>
            <a-select-option value="area">Area</a-select-option>
            <a-select-option value="pie">Pie</a-select-option>
          </a-select>
        </a-col>
      </a-row>
    </template>

    <div class="chart-container" style="position: relative; height: 400px;">
      <LineChart
        v-if="selectedChartType === 'line'"
        :chart-data="chartData"
        :options="chartOptions"
        :chart-id="chartId"
        :width="400"
        :height="400"
      />
      <BarChart
        v-else-if="selectedChartType === 'bar'"
        :chart-data="chartData"
        :options="chartOptions"
        :chart-id="chartId"
        :width="400"
        :height="400"
      />
      <DoughnutChart
        v-else-if="selectedChartType === 'pie'"
        :chart-data="pieChartData"
        :options="pieChartOptions"
        :chart-id="chartId"
        :width="400"
        :height="400"
      />
    </div>

    <!-- Chart Legend/Stats -->
    <template #actions>
      <a-row v-if="showStats" :gutter="16" class="chart-stats">
        <a-col v-for="stat in stats" :key="stat.label" :span="8">
          <a-statistic
            :title="stat.label"
            :value="stat.value"
            :precision="stat.precision || 0"
            :value-style="{ color: stat.color }"
          >
            <template #prefix>
              <a-icon :type="stat.icon" v-if="stat.icon" />
            </template>
          </a-statistic>
        </a-col>
      </a-row>
    </template>
  </a-card>
</template>

<script>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler
} from 'chart.js';

import { Line as LineChart, Bar as BarChart, Doughnut as DoughnutChart } from 'vue-chartjs';

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
  Title,
  Tooltip,
  Legend,
  ArcElement,
  Filler
);

export default {
  name: 'InteractiveChart',
  components: {
    LineChart,
    BarChart,
    DoughnutChart
  },
  props: {
    title: {
      type: String,
      default: 'Chart Title'
    },
    description: {
      type: String,
      default: 'Chart description'
    },
    chartData: {
      type: Object,
      required: true
    },
    chartOptions: {
      type: Object,
      default: () => ({})
    },
    chartType: {
      type: String,
      default: 'line'
    },
    showTypeSelector: {
      type: Boolean,
      default: true
    },
    showStats: {
      type: Boolean,
      default: true
    },
    stats: {
      type: Array,
      default: () => []
    },
    chartId: {
      type: String,
      default: () => `chart-${Math.random().toString(36).substr(2, 9)}`
    }
  },
  data() {
    return {
      selectedChartType: this.chartType
    };
  },
  computed: {
    pieChartData() {
      if (this.selectedChartType !== 'pie') return this.chartData;
      
      // Convert line/bar data to pie chart format
      const datasets = this.chartData.datasets || [];
      const labels = this.chartData.labels || [];
      
      if (datasets.length > 0) {
        const data = datasets[0].data || [];
        const backgroundColor = datasets.map((_, index) => 
          `hsl(${(index * 360) / datasets.length}, 70%, 60%)`
        );
        
        return {
          labels: labels,
          datasets: [{
            data: data,
            backgroundColor: backgroundColor,
            borderWidth: 2,
            borderColor: '#fff'
          }]
        };
      }
      
      return this.chartData;
    },
    pieChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          },
          title: {
            display: false
          }
        }
      };
    }
  },
  watch: {
    chartType(newType) {
      this.selectedChartType = newType;
    }
  },
  methods: {
    onChartTypeChange(type) {
      this.$emit('chart-type-changed', type);
    }
  }
};
</script>

<style lang="scss" scoped>
.chart-container {
  padding: 16px 0;
}

.chart-stats {
  margin-top: 16px;
  padding-top: 16px;
  border-top: 1px solid #f0f0f0;
}

.ant-card-p-0 .ant-card-body {
  padding: 24px 24px 0 24px;
}
</style>