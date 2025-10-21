<!-- 
	This is the dashboard page, it uses the dashboard layout in: 
	"./layouts/Dashboard.vue" .
 -->

 <template>
	<div>
		<!-- Counter Widgets -->
		<a-row :gutter="24">
			<a-col :span="24" :lg="24" class="mb-24">
				Search Filter
			</a-col>
			<a-col :span="24" :lg="3" class="mb-24">
        <a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="Year From">
              <a-select
                  v-model="selectedYears.from"
                  v-decorator="[
                    'schoolYeaer',
                    { rules: [{ required: true, message: 'Please select year' }] },
                  ]"
                  placeholder="Select a Year"
                  :options="yearMultipleOpts"
                  @change="changeYear"
              />
          </a-form-item>
        </a-form>
			</a-col>
			<a-col :span="24" :lg="3" class="mb-24">
        <a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="Year To">
              <a-select
                  v-model="selectedYears.to"
                  v-decorator="[
                    'schoolYeaer',
                    { rules: [{ required: true, message: 'Please select year' }] },
                  ]"
                  placeholder="Select a Year"
                  :options="yearMultipleOpts"
                  @change="changeYear"
              />
          </a-form-item>
        </a-form>
			</a-col>
			<a-col :span="24" :lg="6" class="mb-24">
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="Type of Report">
              <a-select
                  v-model="filters.reportType"
                  v-decorator="[
                      'reportType',
                      { rules: [{ required: true, message: 'Please select type of report' }] },
                  ]"
                  placeholder="Select Type of Report"
                  :options="typeOfReportOpt"
                  @change="getCourceList"
              />
          </a-form-item>
        </a-form>
			</a-col>
      <a-col v-if="filters.reportType !== 'employee'" :span="24" :lg="6" class="mb-24">
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="Term/Semester">
            <a-select
                v-model="filters.course"
                v-decorator="[
                    'term',
                    { rules: [{ required: true, message: 'Please select your gender!' }] },
                ]"
                placeholder="Select a School Term"
                :options="courseOpt"
                @change="getDepartmentSelection"
            />
          </a-form-item>
        </a-form>
			</a-col>
			<a-col v-if="filters.reportType !== 'employee'" :span="24" :lg="6" class="mb-24">
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="School/Courses">
            <a-select
                v-model="filters.department"
                v-decorator="[
                    'course',
                    { rules: [{ required: true, message: 'Please select your gender!' }] },
                ]"
                placeholder="Select a School/Course"
                :options="schoolOpt"
                @change="getListSelection"
            />
          </a-form-item>
        </a-form>
			</a-col>
			


			<a-col v-if="filters.reportType === 'employee'" :span="24" :lg="6" class="mb-24">
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="Employee Category">
            <a-select
                v-model="filters.course"
                placeholder="Select a Employee Category"
                :options="courseOpt"
                @change="getDepartmentSelection"
            />
          </a-form-item>
        </a-form>
			</a-col>
			<a-col v-if="filters.reportType === 'employee'" :span="24" :lg="6" class="mb-24">
				<a-form :label-col="{ span: 24 }" :wrapper-col="{ span: 24 }">
          <a-form-item label="Department">
              <a-select
                v-model="filters.department"
                placeholder="Select a Year"
                :options="schoolOpt"
                @change="getListSelection"
              />
          </a-form-item>
        </a-form>
			</a-col>
		</a-row>
		<!-- / Counter Widgets -->

		<!-- Charts -->
		<a-row :gutter="24" type="flex" align="stretch">
			<a-col :span="24" :lg="24" class="mb-24">
				<CardBarChart
          v-if="(filters.reportType === 'enrollment')"
          title="No. of Enrolled Students"
          description="Population rates representaion annually"
          :chartData.sync="seriesDataEnroll"
          :groupData.sync="groupDataEnroll"
        />
				<CardBarChart
          v-if="(filters.reportType === 'graduate')"
          title="No. of Graduate Students"
          description="Population rates representaion annually"
          :chartData.sync="seriesDataGrad"
          :groupData.sync="groupDataGrad"
        />
			</a-col>
			<a-col :span="24" :lg="24" class="mb-24">
				<CardLineChart
          v-if="(filters.reportType !== 'employee')"
          title="No. of Enrolled Students"
          :chartData.sync="seriesData"
          :groupData.sync="groupData"
        />

				<CardEmployeeChart
          v-if="(filters.reportType === 'employee')"
          title="Employee Analytical Chart"
          :chartData.sync="seriesData"
          :groupData.sync="groupData"
        />
			</a-col>
		</a-row>
		<!-- / Charts -->

	</div>
</template>

<script>
import moment from "moment";
import CardBarChart from '../components/Cards/Analytics/CardBarChart' ;
import CardLineChart from '../components/Cards/Analytics/CardLineChart' ;
import CardEmployeeChart from '../components/Cards/Analytics/CardEmployeeChart' ;
export default ({
	components: {
		CardLineChart,
    CardBarChart,
    CardEmployeeChart
	},
	data() {
		return {
			// Counter Widgets Stats
			filters: {
        reportType: 'enrollment',
        course: '',
        department: '',
        term: '',
      },

      selectedYears: {
        from: "2019",
        to: "2024"
      },
      showCharts: false,
      courseOpt: [],
      schoolOpt: [],
      departments: [],
      chartOptions: [],
      chartGradOptions: [],
      seriesData: {
        male: [],
        female: [],
      },
      groupData: [],
      typeOfReportOpt: [
          {
              label: "Enrollment",
              value: "enrollment",
          },
          {
              label: "Graduates",
              value: "graduate",
          },
          {
              label: "Employment",
              value: "employee",
          },
      ],

      seriesDataEnroll: [],
			groupDataEnroll: [],
			seriesDataGrad: [],
			groupDataGrad: [],
		}
	},
    computed:{
      yearMultipleOpts() {
				let res = [];
				let startDate = 1997;
				let currentYear = new Date().getFullYear();

				for (let year = startDate; year <= currentYear; year++) {
					res.push({
					label: year.toString(),
					value: year.toString()
					});
				}

				return res;
			}
    },
    created(){
      this.getCourceList()
    },
    methods:{
        moment,
        changeYear(){
            this.getCourceList()
        },
        async getCourceList(){
            let payload = {
              from: this.selectedYears.from,
              to: this.selectedYears.to,
              reportType: this.filters.reportType
            }
            this.$api.post("analytics/get/graph/options", payload).then((res) => {
              let response = {...res.data}
              if(!response.error){
                
                this.courseOpt = []
                this.departments = res.data
                for (const i in res.data) {
                  this.courseOpt.push({
                    label: i,
                    value: i,
                  })
                }

                this.courseOpt = this.courseOpt.filter(
                  (v, i, a) => a.findIndex(t => (t.label === v.label && t.value === v.value)) === i
                );

                this.filters.course = this.courseOpt[0].value
                this.getDepartmentSelection()
                
                // if(this.filters.reportType === 'employee'){
                //   this.courseOpt = []
                //   this.departments = res.data
                //   for (const i in res.data) {
                //     this.courseOpt.push({
                //       label: i,
                //       value: i,
                //     })
                //   }
                //   this.filters.course = this.courseOpt[0].value
                //   this.getDepartmentSelection()
                // } else {
                //   this.courseOpt = res.data
                //   this.filters.course = this.courseOpt[0].value
                // }
                
                this.getListSelection()
              } else {
                // show Error
                console.log('there is some error')
              }
            })
        },
        getDepartmentSelection(){
          let typeData = typeof this.departments[this.filters.course];
          this.schoolOpt = typeData === 'object' ? 
          Object.values(this.departments[this.filters.course]) : 
          this.departments[this.filters.course];

          this.schoolOpt = this.schoolOpt.filter(
            (v, i, a) => a.findIndex(t => (t.label === v.label && t.value === v.value)) === i
          );
          
          this.filters.department = this.schoolOpt[0].value

          this.getListSelection()
        },
        async getListSelection(){
            let payload = {
              from: this.selectedYears.from,
              to: this.selectedYears.to,
              ...this.filters
            }

            this.$api.post("analytics/get/graph", payload).then((res) => {
              let response = {...res.data}
              if(!response.error){
                
                let selected = [];
                let series = []
                let groups = []

                if(typeof res.data === "object"){
                    for (const el in res.data) {
                        selected.push(res.data[el])
                    }
                } else {
                    selected = res.data
                }

                if(this.filters.reportType === 'enrollment'){
                  series = {
                    male: [],
                    female: [],
                  }
                  selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))
                  selected.forEach(el => {
                    groups.push(el.group.title)
                    
                    el.series.forEach(sel => {
                      if(sel.x === 'Male'){
                        series.male.push(sel.y)
                      } else {
                        series.female.push(sel.y)
                      }
                    })
                  });

                  this.getEnrollmentData()
                } else if(this.filters.reportType === 'graduate'){
                    series = {
                      male: [],
                      female: [],
                    }
                    let maleSeries = []
                    let femaleSeries = []
                    for(const i in res.data){
                        maleSeries.push(res.data[i].male)
                        femaleSeries.push(res.data[i].female)
                        groups.push(res.data[i].categories)
                    }
                    series.male = maleSeries
                    series.female = femaleSeries
                    this.getGraduateData()
                } else if(this.filters.reportType === 'employee'){
                  series = {
                    male: [],
                    female: [],
                    vacant: [],
                  }
                  res.data[0].series.data.forEach((el, indx) => {
                    if(indx === 1){
                      series.female.push(el)
                    } else if(indx === 2){
                      series.vacant.push(el)
                    } else {
                      series.male.push(el)
                    }
                  })
                  groups = [`${res.data[0].group }`]
                }
                
                // console.log(series, groups)
                this.seriesData = series
                this.groupData = groups
              } else {
                // show Error
                console.log('there is some error')
              }
            })
        },

      async getEnrollmentData(){
				let payload = {
					...this.selectedYears,
					reportType: 'enrollment',
          page: 'analytics',
          course: this.filters.department,
				}
				this.$api.post("analytics/get/graph/dashboard", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						let selected = [];
						let series = {
							male: [],
							female: [],
						}
						let groups = []
						if(typeof res.data.list === "object"){
							for (const el in res.data.list) {
								groups.push(el)
								selected.push(res.data.list[el])
							}
						} else {
							selected = res.data.list
						}
						// selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))
						selected.forEach((el) => {
							let totalMale = 0
							let totalFemale = 0

							if(typeof el === "object"){
								for (const i in el) {
									totalMale += el[i][0].y
									totalFemale += el[i][1].y
								}
							} else {
								el.forEach(sel => {
									totalMale += sel[0].y
									totalFemale += sel[1].y
								})
							}
							series.male.push(totalMale)
							series.female.push(totalFemale)
						});

						this.seriesDataEnroll = series
            this.groupDataEnroll = groups
						
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			},
			async getGraduateData(){
				let payload = {
					...this.selectedYears,
					reportType: 'graduate',
          page: 'analytics',
          course: this.filters.department,
				}
				this.$api.post("analytics/get/graph/dashboard", payload).then((res) => {
					let response = {...res.data}
					if(!response.error){
						let selected = [];
						let series = {
							male: [],
							female: [],
						}
						let groups = []

						if(typeof res.data.list === "object"){
							for (const el in res.data.list) {
								groups.push(el)
								selected.push(res.data.list[el])
							}
						} else {
							selected = res.data.list
						}
						
						// selected.sort((a, b) => +(a.group.title > b.group.title) || -(a.group.title < b.group.title))
						selected.forEach((el) => {
							let totalMale = 0
							let totalFemale = 0
							if(typeof el === "object"){
								for (const i in el) {
									totalMale += el[i].male
									totalFemale += el[i].female
								}
							}

							series.male.push(totalMale)
							series.female.push(totalFemale)
						});

						this.seriesDataGrad = series
            this.groupDataGrad = groups

						
					} else {
						// show Error
						console.log('there is some error')
					}
				})
			}


    }
})

</script>
