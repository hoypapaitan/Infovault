// Graduate Analytics API Service - only uses tblgraduates table
import axios from 'axios'

const API_BASE_URL = process.env.VUE_APP_API_BASE_URL || 'http://localhost:8080/api'

export const graduateAnalyticsAPI = {
  // Get dashboard summary with graduate statistics
  async getDashboardSummary(fromYear = null, toYear = null) {
    try {
      const params = {}
      if (fromYear) params.from = fromYear
      if (toYear) params.to = toYear
      
      const response = await axios.get(`${API_BASE_URL}/graduate-analytics/dashboard/summary`, {
        params
      })
      return response.data
    } catch (error) {
      console.error('Error fetching graduate dashboard summary:', error)
      throw error
    }
  },

  // Get filter options (years, courses, batches) from graduates table
  async getFilterOptions() {
    try {
      const response = await axios.get(`${API_BASE_URL}/graduate-analytics/filter/options`)
      return response.data
    } catch (error) {
      console.error('Error fetching graduate filter options:', error)
      throw error
    }
  }
}

export default graduateAnalyticsAPI