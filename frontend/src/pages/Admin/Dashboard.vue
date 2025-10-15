<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Application</span><br/>
            </div>
        </div>
        <div class="row">
            <div
                v-for="(item, index) in dashboardCards"
                :key="index"
                class="col-12 col-xs-12 col-sm-6 col-md-3 q-pa-sm"
            >
                <q-card class="my-card" flat bordered>
                    <q-card-section class="text-h6">
                        {{ item.title }}
                    </q-card-section>
                    <q-item>
                        <q-item-section avatar>
                            <q-avatar :color="item.iconColor"  rounded>
                                <q-icon :name="item.icon" color="white" size="34px" />
                            </q-avatar>
                        </q-item-section>

                        <q-item-section>
                            <q-item-label class="text-h4">{{ item.count }}</q-item-label>
                            <q-item-label caption>
                                {{ item.description }}
                            </q-item-label>
                        </q-item-section>
                    </q-item>

                </q-card>
            </div>
        </div>
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-6 q-pa-sm">
                <q-card class="my-card" flat bordered>
                    <q-card-section class="text-h6">
                        Analytical Representation of Scholarship
                    </q-card-section>
                    <q-card-section>
                        <canvas ref="linechart"></canvas>
                    </q-card-section>
                </q-card>
            </div>
            <div class="col-12 col-xs-12 col-sm-12 col-md-6 q-pa-sm">
                <q-card class="my-card" flat bordered>
                    <q-card-section class="text-h6">
                        Analytical Representation of Scholarship
                    </q-card-section>
                    <q-card-section>
                        <canvas ref="piechart"></canvas>
                    </q-card-section>
                </q-card>
            </div>
        </div>
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    data(){
        return {
            dashboardCards: [
                {
                    title: "Users",
                    count: 0,
                    description: "Students registered in the portal",
                    icon: "mdi-account-school-outline",
                    iconColor: "primary",
                },
                {
                    title: "Qualified Students",
                    count: 0,
                    description: "Students get approved on the programs",
                    icon: "mdi-thumb-up",
                    iconColor: "green",
                },
                {
                    title: "Unqualified Students",
                    count: 0,
                    description: "Students are declined on the programs",
                    icon: "mdi-thumb-down",
                    iconColor: "red",
                },
                {
                    title: "Pendings",
                    count: 0,
                    description: "Applications subject for evaluation and approval ",
                    icon: "mdi-clipboard-text-clock",
                    iconColor: "secondary",
                },
            ],
            chart: {},
            chartLine: {},
            chartLineDatas: {
                labels: [],
                datasets: [
                    {
                        label: 'Qualified',
                        data: [],
                        borderColor: '#72e37c',
                        backgroundColor: 'red',
                        pointStyle: 'circle',
                        pointRadius: 10,
                        pointHoverRadius: 15
                    },
                    {
                        label: 'Unqualified',
                        data: [],
                        borderColor: '#ff0000',
                        backgroundColor: '#ff000040',
                        pointStyle: 'circle',
                        pointRadius: 10,
                        pointHoverRadius: 15
                    },
                    {
                        label: 'Pendings',
                        data: [],
                        borderColor: '#72e37c',
                        backgroundColor: 'red',
                        pointStyle: 'circle',
                        pointRadius: 10,
                        pointHoverRadius: 15
                    },
                ]
            },
            chartPieDatas: {
                labels: [],
                datasets: [{
                    label: '',
                    data: [],
                    borderWidth: 1,
                    backgroundColor: [],
                }]
            },
        }
    },
    computed: {
        user: function(){
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
    },
    mounted(){
        this.getDasboard()
    },
    methods: {
        moment,
        async renderPie(){
            let ctx = this.$refs.piechart.getContext("2d");
            this.chart = new Chart(ctx, {
                type: 'pie',
                data: this.chartPieDatas,
                options: {
                    plugins: {
                        legend: {
                            onHover: this.handleHover,
                            onLeave: this.handleLeave
                        }
                    }
                }
            });
        },
        async renderLine(){
            let ctx = this.$refs.linechart.getContext("2d");
            this.chartLine = new Chart(ctx, {
                type: 'bar',
                data: this.chartLineDatas,
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: (ctx) => 'Point Style: ' + ctx.chart.data.datasets[0].pointStyle,
                        }
                    }
                }
            });
        },
        handleHover(evt, item, legend) {
            legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                colors[index] = index === item.index || color.length === 9 ? color : color + '4D';
            });
            legend.chart.update();
        },
        handleLeave(evt, item, legend) {
            legend.chart.data.datasets[0].backgroundColor.forEach((color, index, colors) => {
                colors[index] = color.length === 9 ? color.slice(0, -2) : color;
            });
            legend.chart.update();
        },
        async getDasboard(){
            this.$api.get('misc/dashboard').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.dashboardCards[0].count = data.users
                    this.dashboardCards[1].count = data.qualified
                    this.dashboardCards[2].count = data.unqualified
                    this.dashboardCards[3].count = data.pendings
                    // this.renderPie()

                    for(const i in data.applications){
                        this.chartLineDatas.labels.push(i)

                        let chartData = {
                            p: 0,
                            q: 0,
                            u: 0,
                        }

                        for (const p in data.applications[i]) {
                            if(Number(data.applications[i][p]) === 1 || Number(data.applications[i][p]) === 0){
                                chartData.p += 1
                            } else if(Number(data.applications[i][p]) === 2) {
                                chartData.q += 1
                            } else {
                                chartData.u += 1
                            }
                        }

                        this.chartLineDatas.datasets[0].data.push(chartData.q)
                        this.chartLineDatas.datasets[0].backgroundColor = '#72e37c'
                        this.chartLineDatas.datasets[1].data.push(chartData.u)
                        this.chartLineDatas.datasets[1].backgroundColor = '#f44336'
                        this.chartLineDatas.datasets[2].data.push(chartData.p)
                        this.chartLineDatas.datasets[2].backgroundColor = '#366ff4'
                    }
                    this.renderLine()

                    for(const i in data.pieApps){
                        this.chartPieDatas.labels.push(i)
                        let dataCount = 0

                        if(typeof data.pieApps[i] === 'object'){
                            dataCount = Object.keys(data.pieApps[i]).length
                        } else {
                            dataCount = data.pieApps[i].length
                        }

                        this.chartPieDatas.datasets[0].data.push(dataCount)
                        this.chartPieDatas.datasets[0].backgroundColor.push('#'+Math.floor(Math.random()*16777215).toString(16))
                    }
                    this.renderPie()
                } else {
                   console.log('error something went wrong')
                }
            })
        },
    }
}
</script>
<style scoped>
.my-card{
    border-radius: 15px;
    box-shadow: 0px 0px 3px -2px !important;
}
.my-card-item{
    border-radius: 10px;
}
.generatedDash{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(255 251 176) 0%, rgb(0 55 255 / 61%) 89%);
}
.generatedDash2{
  color: white;
  background: rgb(0,177,250);
  background: linear-gradient(122deg, rgb(38 148 243) 0%, rgb(45 253 215 / 61%) 89%);
}
</style>
