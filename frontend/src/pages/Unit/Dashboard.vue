<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <span class="text-h6 text-bold">Hi {{`${user.fullName}`}},</span><br/>
                <span class="text-caption">Welcome to ASCOT Scholarship Management System</span><br/>
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
            <div  v-if="selectedProvider === ''" class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <div class=" row wrap justify-start items-center content-center">
                    <span class="text-h6 text-bold">Program List</span>
                    <q-space />
                    <q-btn @click="selectedProvider = 'All'" flat rounded color="primary" label="View All Application" />
                </div>
                <div 
                    v-if="!tableLoading && itemsList.length === 0" 
                    class="text-center q-pa-md"
                >
                    <q-icon color="grey-4" name="ti-dropbox-alt" size="6em" /> <br/>
                    <span class="text-caption text-grey-8">
                        No Data Can Be Shown.
                    </span>
                </div>
                <q-list
                    v-for="(item, idx) in providerList"
                    :key="idx"
                    bordered 
                    class="rounded-borders itemBorder q-mb-sm"
                >

                    <q-item>

                        <q-item-section >
                            <q-item-label lines="1">
                                <span class="text-bold text-primary">{{`${item.provider}`}}</span><br/>
                                <span class="text-grey-8">
                                    {{item.description}}
                                </span>
                            </q-item-label>
                        </q-item-section>

                        <q-item-section side>
                        <div class="text-grey-8 q-gutter-xs">
                            <!-- <q-btn class="gt-xs" size="12px" flat dense round icon="delete" /> -->
                            <q-btn @click="viewApplicationList(item)" class="gt-xs" flat color="primary" no-caps  dense label="View Applications" />
                            <!-- <q-btn size="12px" flat dense round icon="more_vert" /> -->
                        </div>
                        </q-item-section>
                    </q-item>
                </q-list>
            </div>

            <div v-if="selectedProvider !== ''" class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-btn @click="backToProviderList" class="gt-xs q-mr-sm" flat color="red" rounded no-caps  dense icon="mdi-arrow-left" />
                <span class="text-h6 text-bold">Applications Subject for {{Number(user.userType) === 3 ? `Evaluation` : `Approval`}}</span><br/><br/>
                
                <div v-if="tableLoading && itemsList.length === 0" class="text-center">
                    <q-spinner-bars
                        color="primary"
                        size="3em"
                    />
                </div>
                <div 
                    v-if="!tableLoading && itemsList.length === 0" 
                    class="text-center q-pa-md"
                >
                    <q-icon color="grey-4" name="ti-dropbox-alt" size="6em" /> <br/>
                    <span class="text-caption text-grey-8">
                        No Data Can Be Shown.
                    </span>
                </div>
                <q-table
                    class="my-sticky-last-column-table"
                    v-if="itemsList.length > 0"
                    flat
                    bordered
                    :rows="filteredList"
                    wrap-cells
                    :columns="tableColumns"
                    row-key="sname"
                    :filter="search"
                >  
                <template v-slot:top-right>
                    <q-input 
                        dense 
                        debounce="300" 
                        v-model="search" 
                        placeholder="Search Student Number"
                        mask="##-##-####NNNN"
                    >
                        <template v-slot:append>
                            <q-icon name="search" />
                        </template>
                    </q-input>
                </template>
                    <template v-slot:header="props">
                        <q-tr :props="props">
                            <q-th
                                v-for="col in props.cols"
                                :key="col.name"
                                :props="props"
                            >
                                {{ col.label }}
                            </q-th>
                            <q-th class="text-center">
                                Actions
                            </q-th>
                        </q-tr>
                    </template>
                    <template v-slot:body="props">
                        <q-tr :props="props">
                            <q-td
                                v-for="col in props.cols"
                                :key="col.name"
                                :props="props"
                            >
                                <div v-if="col.label === 'Application Status'" class="text-center">
                                    <q-stepper
                                        :model-value="checkStepProcess(col.value)"
                                        ref="stepper"
                                        contracted
                                        color="orange"
                                        :id="`evaluatorProcess-${col.value.id}`"
                                        flat
                                        active-icon="ti-reload"
                                        active-color="orange"
                                        done-icon="mdi-check-all"
                                        done-color="green"
                                        class="customStepper"
                                    >
                                        <q-step
                                            class="no-content"
                                            :name="0"
                                            :done="true"
                                        >
                                            
                                        </q-step>
                                        <q-step
                                            class="no-content"
                                            header-nav
                                            :name="1"
                                            :done="Number(col.value.evaluatedBy) > 0"
                                        >
                                        </q-step>

                                        <q-step
                                            class="no-content"
                                            id="approverProcess"
                                            :name="2"
                                            icon="mdi-check-decagram"
                                            :done="Number(col.value.approvedBy) > 0"
                                        >
                                        </q-step>
                                    </q-stepper>
                                    <q-tooltip
                                        anchor="center middle" self="top middle"
                                        :target="`#evaluatorProcess-${col.value.id}`"
                                        class="bg-white text-black q-pa-md"
                                    >
                                        <span class="text-bold">Status:</span> {{props.row.status}} <br/>
                                        <span class="text-bold">Remarks:</span> {{props.row.remarks || 'No Remarks'}} <br/>
                                    </q-tooltip>
                                </div>
                                <div v-if="col.label !== 'Application Status'">
                                    {{ col.value }}
                                </div>
                            </q-td>
                            <q-td class="text-center">
                                <!-- For Approver -->
                                <!-- For Evaluator -->
                                <q-btn 
                                    v-if="Number(user.userType) === 3 && Number(props.row.data.appStatus) === 0"
                                    @click="viewApplication(props.row)"
                                    outline 
                                    rounded 
                                    size="sm"
                                    color="primary" 
                                    label="Evaluate" 
                                />
                                <q-btn 
                                    v-if="(Number(user.userType) === 3 || Number(user.userType) === 4) && Number(props.row.data.appStatus) !== 0"
                                    @click="viewApplication(props.row)"
                                    outline 
                                    rounded 
                                    size="sm"
                                    color="secondary" 
                                    label="View Details" 
                                />
                            </q-td>
                        </q-tr>
                    </template>
                </q-table>
            </div>
        </div>
        <div class="row q-mt-lg">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-separator />
            </div>
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
        
        <!-- Drawer application preview -->
        <!-- Application Submit -->
        
        <q-drawer
            side="right"
            v-model="drawerRight"
            bordered
            overlay
            :width="900"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div v-if="selectedProgram.data !== undefined">
                            <div class="text-h5 text-weight-bold">{{selectedProgram.studentName}}</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Cancel" @click="drawerRight = !drawerRight" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section >
                        <div v-if="selectedProgram.data !== undefined" class="row">
                            <div class="col-12 col-md-12 q-pa-xs q-mt-md">
                                <span class="text-title text-bold">Requirement Checklist</span>
                            </div>
                            <div class="col-12 col-md-12">
                                <q-list>
                                    <q-item 
                                        v-for="(item, index) in selectedProgram.data.scholarship.requirements" 
                                        :key="item.name" 
                                        tag="label" 
                                        v-ripple
                                    >
                                        <q-item-section side>
                                            <q-icon :color="item.color" name="task_alt" />
                                        </q-item-section>

                                        <q-item-section>
                                            <q-item-label>{{ item.label }}</q-item-label>
                                        </q-item-section>

                                        <q-item-section side>
                                            <div v-if="item.fileUploaded === undefined">
                                                <q-btn  @click="requestUpdate(item)" outline size="sm" rounded color="red" label="Request For Upload" />
                                            </div>
                                            <div v-if="item.fileUploaded !== undefined">
                                                <q-btn
                                                    @click="previewDocs(item.uploadFile, item.name, item)"
                                                    outline 
                                                    size="sm" 
                                                    class="q-mr-xs" 
                                                    rounded 
                                                    color="primary" 
                                                    label="View"
                                                />
                                                <q-btn 
                                                    @click="requestUpdate(item)"
                                                    outline 
                                                    rounded 
                                                    size="sm"
                                                    class="q-mr-xs" 
                                                    no-caps
                                                    color="red" 
                                                    label="Request Update" 
                                                />
                                                <q-btn
                                                    v-if="item.verified === undefined"
                                                    @click="verifyDocument(item)"
                                                    outline size="sm" 
                                                    class="q-mr-xs" 
                                                    rounded color="green" 
                                                    label="Verify" 
                                                />
                                            </div>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                            <div class="col-12 col-md-12">
                                <q-separator />
                            </div>
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Personal Information</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.civilStatus || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Civil Status</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${moment(selectedProgram.data.student.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Date of Birth</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.email || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Email</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Student Information</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.yrLvl || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Year Level</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${convertCourses(selectedProgram.data.student.courseId) || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Course</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.username || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Student Number</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.schoolAttended || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Attended</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.student.schoolAddress || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Address</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                        
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Family Background</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.name || '--'} (${selectedProgram.data.familyBackground.father.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Fathers Name</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.father.educAttainment || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Educational Attainement</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.name || '--'} (${selectedProgram.data.familyBackground.mother.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Mothers Name</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                                <br/>
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.mother.educAttainment || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Educational Attainement</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.totalIncome || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Parents Total Annual Income</span>
                            </div>
                            <div class="col-6 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.noOfSiblings || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">No. of Siblings</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">If not living with parents: </span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.name || '--'} (${selectedProgram.data.familyBackground.mother.livingStatus})`}}</span><br/>
                                <span class="text-caption text-grey">Guardian Name</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.relation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Relationship</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${selectedProgram.data.familyBackground.guardian.occupation || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Occupation</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            
                            <div class="col-12 col-md-12 q-pa-xs q-mt-md">
                                <span class="text-title text-bold">Qualifications</span>
                            </div>
                            <div class="col-12 col-md-12">
                                <q-list>
                                    <q-item 
                                        v-for="(itm, indx) in selectedProgram.data.scholarship.qualification"
                                        :key="indx"
                                    >
                                        <q-item-section avatar>
                                            <q-avatar>
                                                <q-icon name="mdi-asterisk-circle-outline" size="sm" />
                                            </q-avatar>
                                        </q-item-section>

                                        <q-item-section>
                                            <q-item-label>
                                                {{itm.description}}
                                            </q-item-label>
                                        </q-item-section>
                                    </q-item>
                                </q-list>
                            </div>
                        </div>
                    </q-card-section>
                    <q-separator />
                    <q-card-actions v-if="selectedProgram.data !== undefined">
                        <q-btn 
                            v-if="Number(user.userType) === 3 && Number(selectedProgram.data.evaluatedBy) === 0"
                            @click="updateApplicationData('evaluate')"
                            :disable="checkFormData"
                            outline 
                            rounded
                            no-caps
                            size="md"
                            color="primary" 
                            label="Send for Approval" 
                        />
                        <q-btn 
                            @click="updateApplicationData('reject')"
                            outline 
                            rounded 
                            size="md"
                            no-caps
                            color="red" 
                            label="Reject Application" 
                        />
                        <q-btn 
                            v-if="Number(user.userType) === 4 && Number(selectedProgram.data.approvedBy) === 0"
                            @click="updateApplicationData('approve')"
                            outline 
                            rounded 
                            no-caps
                            size="md"
                            color="primary" 
                            label="Approve Application" 
                        />
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>


        <!-- Preview Modals -->
        <previewModal 
            :modalStatus="previewModalStatus"
            :appData="selectedFile"
            @updatePrintStatus="closeFormModal"
        />
    </div>
</template>

<script>
import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import previewModal from '../../components/Modals/PreviewDocument.vue';

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    components:{
        previewModal
    },
    data(){
        return {
            providerList: [],
            selectedProvider: "",
            search: "",
            drawerRight: false,
            selectedProgram: {},
            tableLoading: false,
            itemsList: [],
            courseOpt: [],
            previewModalStatus: false,
            selectedFile: "",
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
    watch:{
        drawerRight(newVal){
            if(newVal){
                this.getFileStatus()
            }
        }
    },
    computed: {
        checkFormData(){
            let res = false
            
            if(Number(this.user.userType) === 3){
                console.log('eval')
                let filterFiles = this.selectedProgram?.data?.scholarship?.requirements?.filter(el => el.verified === undefined)
                res = filterFiles?.length > 0
            }
            

            return res
        },
        filteredList(){
            if(this.selectedProvider === "All"){
                return this.itemsList
            } else {
                return this.itemsList.filter(el => this.selectedProvider === el.title)
            }
			
		},
        user: function(){
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
        tableColumns: function(){
            return [
                {
                    name: 'sname',
                    required: true,
                    label: 'Student Number',
                    align: 'left',
                    field: row => row.studentNumber,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'name',
                    required: true,
                    label: 'Student Name',
                    align: 'left',
                    field: row => row.studentName,
                    format: val => `${val}`,
                    sortable: true
                },
                {
                    name: 'title',
                    required: true,
                    label: 'Scholarship Applied',
                    align: 'left',
                    field: row => row.title,
                    format: val => `${val}`,
                    sortable: true
                },
                { name: 'provider', label: 'Type', field: 'provider', align: 'left' },
                { name: 'processFlow', label: 'Application Status', field: 'data', align: 'center',},
                // { name: 'status', label: 'Status', field: 'status', align: 'left' },
            ]
        }
    },
    created(){
        this.getList()
        this.getCourses()
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
        viewApplicationList(item){
            this.selectedProvider = item.description
        },
        backToProviderList(){
            this.selectedProvider = ""
        },
        async updateApplicationData(type){
            // Confirm
            this.$q.dialog({
                title: 'Update Application Status',
                message: 'Would you like to proceed with this transaction?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.$q.loading.show();
                let payload = {}

                if(type === "evaluate"){
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        actionType: type,
                        uid: this.user.userId,
                        scholarId: Number(this.selectedProgram.data.scholarId),
                        updateDetails: {
                            appStatus: 1,
                            evaluatedBy: this.user.userId,
                            status: "Application has been moved for Approval",
                            dateEvaluated: moment().format("l LT"),
                        },
                        notification: {
                            applicationId: Number(this.selectedProgram.data.id),
                            toUser: this.selectedProgram.data.studentId,
                            fromUser: this.user.userId,
                            message: 'Your application has been evaluated and move to the next step.',
                            notifType: 'green',
                            seen: 0,
                        }
                    }
                } else if(type === "reject"){
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        actionType: type,
                        uid: this.user.userId,
                        scholarId: Number(this.selectedProgram.data.scholarId),
                        updateDetails: {
                            appStatus: 3,
                            evaluatedBy: 0,
                            approvedBy: 0,
                            rejectedBy: this.user.userId,
                            status: "Rejected",
                            remarks: "Application has been Rejected",
                            dateRejected: moment().format("l LT"),
                        },
                        notification: {
                            applicationId: Number(this.selectedProgram.data.id),
                            toUser: this.selectedProgram.data.studentId,
                            fromUser: this.user.userId,
                            message: 'Your application has been rejected.',
                            notifType: 'red',
                            seen: 0,
                        }
                    }
                } else {
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        studentId: Number(this.selectedProgram.data.studentId),
                        actionType: type,
                        uid: this.user.userId,
                        scholarId: Number(this.selectedProgram.data.scholarId),
                        email: this.selectedProgram.data.student.email,
                        updateDetails: {
                            appStatus: 2,
                            approvedBy: this.user.userId,
                            status: "Application has been Approved",
                            dateApproved: moment().format("l LT"),
                        },
                        notification: {
                            applicationId: Number(this.selectedProgram.data.id),
                            toUser: this.selectedProgram.data.studentId,
                            fromUser: this.user.userId,
                            message: 'Your application has been approved, you can now use the scholarship benefit',
                            notifType: 'green',
                            seen: 0,
                        }
                    }
                }

                this.$api.post('scholarship/update/application', payload).then(async (response) => {
                    const data = {...response.data};

                    if(!data.error){
                        this.$q.notify({
                            position: 'top-left',
                            type: 'success',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-check-all'
                        })

                        this.getList()
                        this.drawerRight = false
                    } else {
                        this.$q.notify({
                            position: 'top-left',
                            type: 'negative',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-alert-circle-outline'
                        })
                    }
                })

                this.$q.loading.hide();
            })
            
        },
        async requestUpdate(item){
            this.$q.dialog({
                title: 'Request Update on Document',
                message: 'Remarks: ',
                prompt: {
                    model: '',
                    type: 'text' // optional
                },
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(data => {
                this.$q.loading.show();
                let payload = {
                    fileId: item.fileId,
                    notify: true,
                    isStudent: false,
                    notification: {
                        applicationId: Number(this.selectedProgram.data.id),
                        toUser: Number(this.selectedProgram.data.studentId),
                        fromUser: this.user.userId,
                        message: data,
                        notifType: 'red',
                        seen: 0,
                    },
                    updateDetails: {
                        status: 2,
                        remarks: data,
                    }
                }
                let index = this.selectedProgram.data.scholarship.requirements.indexOf(item)
                let requirements = this.selectedProgram.data.scholarship.requirements[index];

                
                this.$api.post('document/update/attachment', payload).then(async (response) => {
                    const data = {...response.data};

                    if(!data.error){
                        this.$q.notify({
                            position: 'top-left',
                            type: 'success',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-check-all'
                        })

                        requirements.verified = true
                        requirements.color = 'red-9'
                    } else {
                        this.$q.notify({
                            position: 'top-left',
                            type: 'negative',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-alert-circle-outline'
                        })
                    }
                })

                this.$q.loading.hide();
            })
        },
        async verifyDocument(item){
            this.$q.dialog({
                title: 'Verify Document',
                message: 'Would you like to proceed with this action?',
                ok: {
                    label: 'Yes'
                },
                cancel: {
                    label: 'No',
                    color: 'negative'
                },
                persistent: true
            }).onOk(() => {
                this.$q.loading.show();
                let payload = {
                    fileId: item.fileId,
                    notify: false,
                    isStudent: false,
                    updateDetails: {
                        status: 1,
                        remarks: "Evaluator verify and checked the Document is Valid",
                    }
                }

                let index = this.selectedProgram.data.scholarship.requirements.indexOf(item)
                let requirements = this.selectedProgram.data.scholarship.requirements[index];

                
                this.$api.post('document/update/attachment', payload).then(async (response) => {
                    const data = {...response.data};

                    if(!data.error){
                        this.$q.notify({
                            position: 'top-left',
                            type: 'success',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-check-all'
                        })

                        requirements.verified = true
                        requirements.color = 'blue-9'
                    } else {
                        this.$q.notify({
                            position: 'top-left',
                            type: 'negative',
                            message: data.title,
                            caption: data.message,
                            icon: 'mdi-alert-circle-outline'
                        })
                    }
                })

                this.$q.loading.hide();
            })
            
        },
        previewDocs(file, reqType, item){
            this.previewModalStatus = true
            this.selectedFile = {
                url: file,
                fileName: item.file,
                type: reqType
            }
        },
        closeFormModal(){
            this.previewModalStatus = false
        },
        viewApplication(val){
            this.drawerRight = true
            this.selectedProgram = val
        },
        checkStepProcess(data){
            let res = 1;

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = 2
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = 3
            }
            return res
        },
        async getList(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/applied/list', {uType: Number(this.user.userType)}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list
                    let providers = data.list.filter((e, i, self) => i === self.findIndex((t) => t.title === e.title))
                    this.providerList = providers.map(el => {
                        let obj = {
                            provider: el.provider,
                            description: el.title,
                            scholarId: el.data.scholarId
                        }

                        return obj
                    });
                }

                this.tableLoading = false
            })
        },
        getFileStatus(){
            let payload = {
                uid: this.selectedProgram.data.studentId,
            }

            this.$api.post('document/get/attachments', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    // fill the already uploaded document
                    data.list.forEach(el => {
                        let filterMatch = this.selectedProgram.data.scholarship.requirements.filter((elr) => {return elr.name === el.reqType})
                        
                        if(filterMatch.length > 0){
                            let val = filterMatch[0]
                            let index = this.selectedProgram.data.scholarship.requirements.indexOf(val)
                            let requirements = this.selectedProgram.data.scholarship.requirements[index];
                            if(Number(el.status) !== 1){
                                requirements.fileId = Number(el.id)
                                requirements.file = el.fileName
                                requirements.fileUploaded = true
                                requirements.uploadFile = el.uploadFile
                                requirements.color = 'green'
                            } else {
                                requirements.fileId = Number(el.id)
                                requirements.file = el.fileName
                                requirements.fileUploaded = true
                                requirements.uploadFile = el.uploadFile
                                requirements.verified = true
                                requirements.color = 'blue-9'
                            }
                        }
                        
                        
                    });
                } else {
                    // error
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        convertCourses(val){
            const res = this.courseOpt.filter(el => el.value === val)
            return res[0].label 
        },
        async getCourses(){
            this.courseOpt = [];
            this.$api.get('misc/courseList').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    let opt = data.list.map((el) => {
                        return {
                            label: el.title,
                            value: el.id
                        }
                    })
                    
                    this.courseOpt = opt
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }
            })
        },
    }
}
</script>
<style scoped>
.no-content{
    display: none;
}
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
