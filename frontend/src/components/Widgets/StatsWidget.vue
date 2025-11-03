<!-- Dashboard Statistics Widget Component -->
<template>
  <a-card 
    :bordered="false" 
    :loading="loading"
    class="stats-widget"
    :class="{ 'trend-up': trend === 'up', 'trend-down': trend === 'down' }"
  >
    <template #title>
      <div class="stats-header">
        <div class="stats-icon">
          <a-icon 
            :type="icon" 
            :style="{ 
              fontSize: '24px', 
              color: iconColor || '#1890ff'
            }" 
          />
        </div>
        <div class="stats-info">
          <h4 class="stats-title">{{ title }}</h4>
          <p class="stats-description">{{ description }}</p>
        </div>
      </div>
    </template>

    <div class="stats-content">
      <div class="main-stat">
        <a-statistic
          :title="mainStatLabel"
          :value="mainValue"
          :precision="precision"
          :suffix="suffix"
          :prefix="prefix"
          :value-style="{ 
            fontSize: '32px', 
            fontWeight: 'bold',
            color: valueColor || '#1890ff'
          }"
        />
      </div>

      <!-- Secondary Stats -->
      <div v-if="secondaryStats && secondaryStats.length > 0" class="secondary-stats">
        <a-row :gutter="8">
          <a-col 
            v-for="stat in secondaryStats" 
            :key="stat.label"
            :span="24 / secondaryStats.length"
          >
            <div class="secondary-stat">
              <div class="secondary-value">{{ stat.value }}</div>
              <div class="secondary-label">{{ stat.label }}</div>
            </div>
          </a-col>
        </a-row>
      </div>

      <!-- Trend Indicator -->
      <div v-if="showTrend && trendValue !== null" class="trend-section">
        <a-divider />
        <div class="trend-indicator">
          <a-icon 
            :type="trend === 'up' ? 'arrow-up' : trend === 'down' ? 'arrow-down' : 'minus'" 
            :style="{ 
              color: trend === 'up' ? '#52c41a' : trend === 'down' ? '#f5222d' : '#d9d9d9',
              marginRight: '4px'
            }"
          />
          <span 
            :style="{ 
              color: trend === 'up' ? '#52c41a' : trend === 'down' ? '#f5222d' : '#d9d9d9',
              fontWeight: 'bold'
            }"
          >
            {{ Math.abs(trendValue) }}{{ trendSuffix }}
          </span>
          <span class="trend-text">{{ trendText }}</span>
        </div>
      </div>

      <!-- Mini Chart -->
      <div v-if="showMiniChart && chartData" class="mini-chart">
        <canvas :id="chartId" width="100" height="40"></canvas>
      </div>

      <!-- Gender Breakdown -->
      <div v-if="showGenderBreakdown && genderData" class="gender-breakdown">
        <a-divider />
        <a-row :gutter="16">
          <a-col :span="12">
            <div class="gender-stat male">
              <a-icon type="man" style="color: #1890ff;" />
              <span class="gender-value">{{ genderData.male || 0 }}</span>
              <span class="gender-label">Male</span>
            </div>
          </a-col>
          <a-col :span="12">
            <div class="gender-stat female">
              <a-icon type="woman" style="color: #f1356d;" />
              <span class="gender-value">{{ genderData.female || 0 }}</span>
              <span class="gender-label">Female</span>
            </div>
          </a-col>
        </a-row>
      </div>
    </div>

    <!-- Actions -->
    <template v-if="actions && actions.length > 0" #actions>
      <a-button
        v-for="action in actions"
        :key="action.key"
        type="link"
        :icon="action.icon"
        @click="$emit('action-click', action.key)"
      >
        {{ action.label }}
      </a-button>
    </template>
  </a-card>
</template>

<script>
import { Chart as ChartJS, LineElement, PointElement, CategoryScale, LinearScale } from 'chart.js';

ChartJS.register(LineElement, PointElement, CategoryScale, LinearScale);

export default {
  name: 'StatsWidget',
  props: {
    title: {
      type: String,
      required: true
    },
    description: {
      type: String,
      default: ''
    },
    icon: {
      type: String,
      default: 'bar-chart'
    },
    iconColor: {
      type: String,
      default: '#1890ff'
    },
    mainValue: {
      type: [Number, String],
      required: true
    },
    mainStatLabel: {
      type: String,
      default: ''
    },
    precision: {
      type: Number,
      default: 0
    },
    suffix: {
      type: String,
      default: ''
    },
    prefix: {
      type: String,
      default: ''
    },
    valueColor: {
      type: String,
      default: '#1890ff'
    },
    secondaryStats: {
      type: Array,
      default: () => []
    },
    showTrend: {
      type: Boolean,
      default: false
    },
    trend: {
      type: String,
      default: 'stable', // 'up', 'down', 'stable'
      validator: value => ['up', 'down', 'stable'].includes(value)
    },
    trendValue: {
      type: Number,
      default: null
    },
    trendSuffix: {
      type: String,
      default: '%'
    },
    trendText: {
      type: String,
      default: 'from last period'
    },
    showMiniChart: {
      type: Boolean,
      default: false
    },
    chartData: {
      type: Array,
      default: () => []
    },
    showGenderBreakdown: {
      type: Boolean,
      default: false
    },
    genderData: {
      type: Object,
      default: () => ({ male: 0, female: 0 })
    },
    actions: {
      type: Array,
      default: () => []
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      chartId: `mini-chart-${Math.random().toString(36).substr(2, 9)}`
    };
  },
  mounted() {
    if (this.showMiniChart && this.chartData && this.chartData.length > 0) {
      this.createMiniChart();
    }
  },
  watch: {
    chartData: {
      handler() {
        if (this.showMiniChart && this.chartData && this.chartData.length > 0) {
          this.createMiniChart();
        }
      },
      deep: true
    }
  },
  methods: {
    createMiniChart() {
      const canvas = document.getElementById(this.chartId);
      if (!canvas) return;
      
      const ctx = canvas.getContext('2d');
      
      new ChartJS(ctx, {
        type: 'line',
        data: {
          labels: this.chartData.map((_, index) => index),
          datasets: [{
            data: this.chartData,
            borderColor: this.iconColor || '#1890ff',
            backgroundColor: `${this.iconColor || '#1890ff'}20`,
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            pointRadius: 0,
            pointHoverRadius: 0
          }]
        },
        options: {
          responsive: false,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false
            },
            tooltip: {
              enabled: false
            }
          },
          scales: {
            x: {
              display: false
            },
            y: {
              display: false
            }
          },
          elements: {
            point: {
              radius: 0
            }
          }
        }
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.stats-widget {
  height: 100%;
  transition: all 0.3s ease;
  
  &:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
  }
  
  &.trend-up {
    border-left: 4px solid #52c41a;
  }
  
  &.trend-down {
    border-left: 4px solid #f5222d;
  }
}

.stats-header {
  display: flex;
  align-items: center;
  gap: 12px;
  
  .stats-icon {
    flex-shrink: 0;
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(24, 144, 255, 0.1);
    border-radius: 8px;
  }
  
  .stats-info {
    flex: 1;
    
    .stats-title {
      margin: 0;
      font-size: 16px;
      font-weight: 600;
      color: #262626;
    }
    
    .stats-description {
      margin: 0;
      font-size: 12px;
      color: #8c8c8c;
      line-height: 1.4;
    }
  }
}

.stats-content {
  .main-stat {
    text-align: center;
    margin-bottom: 16px;
  }
  
  .secondary-stats {
    margin-bottom: 16px;
    
    .secondary-stat {
      text-align: center;
      padding: 8px 4px;
      border-radius: 4px;
      background: #fafafa;
      
      .secondary-value {
        font-size: 18px;
        font-weight: 600;
        color: #262626;
        line-height: 1;
      }
      
      .secondary-label {
        font-size: 11px;
        color: #8c8c8c;
        margin-top: 2px;
      }
    }
  }
  
  .trend-section {
    .trend-indicator {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 4px;
      font-size: 14px;
      
      .trend-text {
        color: #8c8c8c;
        margin-left: 4px;
      }
    }
  }
  
  .mini-chart {
    margin-top: 16px;
    text-align: center;
    
    canvas {
      border-radius: 4px;
    }
  }
  
  .gender-breakdown {
    .gender-stat {
      display: flex;
      align-items: center;
      gap: 6px;
      padding: 8px;
      border-radius: 4px;
      background: #fafafa;
      
      .gender-value {
        font-size: 16px;
        font-weight: 600;
        color: #262626;
      }
      
      .gender-label {
        font-size: 12px;
        color: #8c8c8c;
      }
      
      &.male {
        background: rgba(24, 144, 255, 0.05);
      }
      
      &.female {
        background: rgba(241, 53, 109, 0.05);
      }
    }
  }
}

@media (max-width: 768px) {
  .stats-header {
    .stats-icon {
      width: 40px;
      height: 40px;
    }
    
    .stats-info {
      .stats-title {
        font-size: 14px;
      }
      
      .stats-description {
        font-size: 11px;
      }
    }
  }
}
</style>