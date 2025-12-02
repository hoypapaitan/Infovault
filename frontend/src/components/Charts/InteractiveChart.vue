<template>
  <a-card :bordered="false" class="header-solid h-full ant-card-p-0">
    <template #title>
      <a-row type="flex" align="middle">
        <a-col :span="24" :md="12">
          <h6 class="font-semibold m-0">{{ title }}</h6>
          <p class="text-muted">{{ description }}</p>
        </a-col>
        <a-col :span="24" :md="12" class="col-info" style="text-align: right;">
          <a-select
            v-if="showTypeSelector"
            v-model:value="selectedChartType"
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

    <div class="chart-container" style="position: relative; height: 300px; width: 100%;">
      <LineChart
        v-if="selectedChartType === 'line' || selectedChartType === 'area'"
        :data="computedChartData"
        :options="computedOptions"
        :chart-id="chartId"
      />
      <BarChart
        v-else-if="selectedChartType === 'bar'"
        :data="computedChartData"
        :options="computedOptions"
        :chart-id="chartId"
      />
      <DoughnutChart
        v-else-if="selectedChartType === 'pie'"
        :data="pieChartData"
        :options="pieChartOptions"
        :chart-id="chartId"
      />
    </div>

    <template #actions>
      <a-row v-if="showStats" :gutter="16" class="chart-stats" type="flex" justify="space-around">
        <a-col v-for="stat in stats" :key="stat.label">
          <a-statistic
            :title="stat.label"
            :value="stat.value"
            :precision="stat.precision || 0"
            :value-style="{ color: stat.color }"
          >
            <template #prefix>
              <component :is="stat.icon" v-if="stat.icon" />
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
    title: { type: String, default: 'Chart Title' },
    description: { type: String, default: 'Chart description' },
    chartData: { type: Object, required: true },
    chartOptions: { type: Object, default: () => ({}) },
    chartType: { type: String, default: 'line' },
    showTypeSelector: { type: Boolean, default: true },
    showStats: { type: Boolean, default: true },
    stats: { type: Array, default: () => [] },
    chartId: { type: String, default: () => `chart-${Math.random().toString(36).substr(2, 9)}` }
  },
  data() {
    return {
      selectedChartType: this.chartType
    };
  },
  computed: {
    // FIX 1 & 2: Handle Area Charts and generic Options
    computedChartData() {
      // Clone data to avoid mutating prop
      const dataCopy = JSON.parse(JSON.stringify(this.chartData));
      
      if (this.selectedChartType === 'area') {
        dataCopy.datasets = dataCopy.datasets.map(dataset => ({
          ...dataset,
          fill: true, // This makes it an Area chart
          // Add transparency to area color if not present
          backgroundColor: dataset.backgroundColor || 'rgba(24, 144, 255, 0.2)'
        }));
      }
      return dataCopy;
    },
    computedOptions() {
      return {
        ...this.chartOptions,
        responsive: true,
        maintainAspectRatio: false, // Important for CSS resizing
      };
    },
    // FIX 3: Fix Pie Chart Color Logic
    pieChartData() {
      // Use the first dataset for the pie chart
      const sourceDataset = this.chartData.datasets?.[0] || { data: [] };
      const dataValues = sourceDataset.data || [];
      const labels = this.chartData.labels || [];

      // Generate a color for EACH DATA POINT, not each dataset
      const backgroundColors = dataValues.map((_, index) => 
        `hsl(${(index * 360) / dataValues.length}, 70%, 60%)`
      );

      return {
        labels: labels,
        datasets: [{
          data: dataValues,
          backgroundColor: backgroundColors,
          borderWidth: 2,
          borderColor: '#fff',
          hoverOffset: 4
        }]
      };
    },
    pieChartOptions() {
      return {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'right' },
          title: { display: false }
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
    onChartTypeChange(value) {
      // Ant Design Vue 2/3 uses 'value' or 'change' depending on version
      // 'value' is passed directly in @change
      this.$emit('update:chartType', value); // Supports v-model on parent
      this.$emit('chart-type-changed', value);
    }
  }
};
</script>

<style lang="scss" scoped>
.chart-container {
  padding: 10px;
  /* Ensure canvas doesn't overflow */
  canvas {
    max-height: 100%;
    max-width: 100%;
  }
}

.col-info {
  display: flex;
  justify-content: flex-end;
}
</style>