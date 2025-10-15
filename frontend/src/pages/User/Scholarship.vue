<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <div class="col-12 col-xs-12 col-sm-12 col-md-12 q-pa-sm">
                <q-card
                    flat
                    class="bg-white"
                >
                    <q-card-section>
                        My Application List
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
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
                            v-if="itemsList.length > 0"
                            flat
                            bordered
                            :rows="itemsList"
                            wrap-cells
                            :columns="tableColumns"
                            row-key="name"
                            :filter="search"
                        >  
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
                                                flat
                                                :id="`evaluatorProcess-${col.value.id}`"
                                                active-icon="mdi-cog-clockwise"
                                                active-color="orange"
                                                done-icon="mdi-check-all"
                                                done-color="green"
                                                error-icon="mdi-close-circle"
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
                                                    :name="1"
                                                    :error="Number(col.value.appStatus) === 3"
                                                    :done="Number(col.value.evaluatedBy) > 0 && Number(col.value.appstatus) !== 3"
                                                >  
                                                </q-step>

                                                <q-step
                                                    class="no-content"
                                                    :name="2"
                                                    title="Create an ad group"
                                                    icon="mdi-check-decagram"
                                                    :error="Number(col.value.appStatus) === 3"
                                                    :done="Number(col.value.approvedBy) > 0 && Number(col.value.appstatus) !== 3"
                                                >
                                                </q-step>
                                            </q-stepper>
                                            <q-tooltip
                                                anchor="center middle" self="top middle"
                                                :target="`#evaluatorProcess-${col.value.id}`"
                                                class="bg-white text-black q-pa-md"
                                            >
                                                <span class="text-bold">Submit Application:</span> Done <br/>
                                                <span class="text-bold">Evaluate:</span> -- <br/>
                                                <span class="text-bold">Approve:</span> -- <br/>
                                                <span class="text-bold">Remarks:</span> {{props.row.status || 'No Remarks'}} <br/>
                                            </q-tooltip>
                                        </div>
                                        <div v-if="col.label !== 'Application Status'">
                                            {{ col.value }}
                                        </div>
                                    </q-td>
                                    <q-td class="text-center">
                                        <q-btn 
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
                    </q-card-section>
                </q-card>
            </div>
        </div>


        <!-- Application Submit -->
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
                    v-if="selectedProgram.data !== undefined" 
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div>
                            <div class="text-h5 text-weight-bold">{{selectedProgram.title}}</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Close" @click="drawerRight = !drawerRight" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section >
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <span class="text-title text-bold">Personal Information</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.firstName || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">First Name</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.middleName || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Middle Name</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.lastName || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Last Name</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.suffix || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">suffix</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.civilStatus || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Civil Status</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${moment(myInfo.dateOfBirth).format("MMMM DD, YYYY") || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Date of Birth</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.contact || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Contact</span>
                            </div>
                            <div class="col-4 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.email || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Email</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.address || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Address</span>
                            </div>
                            <div class="col-12">
                                <q-separator />
                            </div>
                            <div class="col-12 col-md-12 q-pa-xs">
                                <span class="text-title text-bold">Student Information</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.yrLvl || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Year Level</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${convertCourses(myInfo.courseId) || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Course</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.username || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">Student Number</span>
                            </div>
                            <div class="col-3 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.schoolAttended || '--'}`}}</span><br/>
                                <span class="text-caption text-grey">School Attended</span>
                            </div>
                            <div class="col-12 q-mb-sm">
                                <span class="text-title text-bold">{{`${myInfo.schoolAddress || '--'}`}}</span><br/>
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
                                            
                                        </q-item-section>
                                    </q-item>
                                </q-list>
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
                    <q-card-actions>
                        <q-btn  @click="openPrint" class="q-mr-sm" color="positive" label="Preview Form" />
                    </q-card-actions>
                </q-card>
            </q-scroll-area>
        </q-drawer>
        <!-- End of Application Submit Drawer -->
        <printFormModal 
            :modalStatus="printModal"
            :appData="appData"
            @updatePrintStatus="closeFormModal"
        />
        <previewModal 
            :modalStatus="previewModalStatus"
            :appData="selectedFile"
            @updatePrintStatus="closePreviewModal"
        />
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import printFormModal from '../../components/Modals/PrintFormModel.vue';
import previewModal from '../../components/Modals/PreviewDocument.vue';


const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    components: {
        printFormModal,
        previewModal
    },
    data(){
        return {
            step: 1,
            printModal: false,
            appData: {},
            itemsList: [],

            drawerRight: false,
            selectedProgram: {},
            myInfo: {},
            courseOpt: [],
            requirementTab: "",
            selectedFile: "",
            previewModalStatus: false,
            form: {
                father:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                mother:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                totalIncome: 0,
                noOfSiblings: 0,
                notWithParents: true,
                guardian: {
                    name:"",
                    occupation:"",
                    relation:"",
                },
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
        user: function(){
            let profile = LocalStorage.getItem('userData');
            return jwtDecode(profile);
        },
        tableColumns: function(){
            return [
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
                // { name: 'remarks', label: 'Remarks', field: 'remarks', align: 'left' },
            ]
        }
    },
    created(){
        this.getCourses()
        this.getApplied()
        this.getMyDetails()
    },
    methods: {
        moment,
        viewApplication(val){
            this.drawerRight = true
            this.selectedProgram = val
        },
        previewDocs(file, reqType){
            this.previewModalStatus = true
            this.selectedFile = {
                url: file,
                type: reqType
            }
        },
        async getApplied(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/applied/status', {sId: this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list

                    let checkApproved = data.list.filter(el => {return Number(el.data.appStatus) === 2})
                    this.approvedApplication = checkApproved.length > 0 ? checkApproved[0] : null
                } else {
                    this.$q.notify({
                        position: 'top-left',
                        type: 'negative',
                        message: data.title,
                        caption: data.message,
                        icon: 'report_problem'
                    })
                }

                this.tableLoading = false
            })
        },
        checkStepProcess(data){
            let res = 1;

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = 2
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = 3
            } else if(Number(data.rejectedBy) > 0){
                res = 4
            }
            return res
        },
        displayStatus(data){
            let res = "";

            if(Number(data.evaluatedBy) !== 0 && Number(data.approvedBy) === 0){
                res = ""
            } else if(Number(data.evaluatedBy) > 0 && Number(data.approvedBy) > 0){
                res = ""
            } else if(Number(data.rejectedBy) > 0){
                res = "Rejected Application"
            }
            return res
        },
        async submitApplicationFormData(){
            this.$q.loading.show();
            this.loginLoad = true;
            let vm = this;
            let payload = {
                status: "Submitted application for evaluation",
                familyBackground: this.form,
                studentId: Number(this.user.userId),
                scholarId: Number(this.selectedProgram.id),
            };

            this.$api.post('scholarship/submit/application', payload).then(async (response) => {
                const data = {...response.data};
                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'positive',
                        message: data.title,
                        caption: data.message,
                        icon: 'verified'
                    })
                    this.resetForm()
                    this.drawerRight = false
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

            this.$q.loading.hide();
        },
        resetForm(){
            this.form = {
                father:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                mother:{
                    name: "",
                    livingStatus: "living",
                    address: "",
                    occupation: "",
                    educAttainment: "",
                    contact: "",
                },
                totalIncome: 0,
                noOfSiblings: 0,
                notWithParents: true,
                guardian: {
                    name:"",
                    occupation:"",
                    relation:"",
                },
            }
        },
        async checkFile(fileVal, index){
            this.$q.loading.show();
            let convertedFile = await this.getBase64(fileVal)
            let payload = {
                userId: this.user.userId,
                reqType: this.selectedProgram.requirements[index].name,
                fileName: fileVal.name,
                fileSize: fileVal.size,
                uploadFile: convertedFile,
                remarks: 'Waiting for validation of the scholarship unit',
                status: 0
            }

            this.$api.post('document/upload', payload).then(async (response) => {
                const data = {...response.data};

                if(!data.error){
                    this.$q.notify({
                        position: 'top-left',
                        type: 'success',
                        message: data.title,
                        caption: data.message,
                        icon: 'mdi-check-all'
                    })
                    this.selectedProgram.requirements[index].fileUploaded = true
                    this.selectedProgram.requirements[index].color = 'green'
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
        },
        async getBase64(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = () => resolve(reader.result);
                reader.onerror = error => reject(error);
            });
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
                        
                    });
                } else {
                    // error
                }
            })

            this.loginLoad = false;
            this.$q.loading.hide();
        },
        applyForScholarship(val){
            this.drawerRight = true
            this.selectedProgram = val
        },
        openPrint(){
            let data = {
                student: {...this.myInfo},
                scholar: {...this.selectedProgram.data.scholarship},
                others: {...this.selectedProgram.data.familyBackground}
            }
            console.log(data)
            this.appData = data
            this.printModal = true
        },
        closeFormModal(){
            this.printModal = false
        },
        closePreviewModal(){
            this.previewModalStatus = false
        },
        async getList(){
            this.itemsList = []
            this.$api.get('scholarship/list').then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.itemsList = data.list
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
        async getMyDetails(){
            this.$api.post('users/getUserById', {id:this.user.userId}).then((response) => {
                const data = {...response.data};
                if(!data.error){
                    this.myInfo = data
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
        convertCourses(courseArray){
            let result = ""
            let list = []

            if(courseArray === undefined){
                return "--"
            }

            
            courseArray = courseArray.split(",")
            list = courseArray.map((val, indx) => {
                const res = this.courseOpt.filter(el => el.value === val)
                return res[0].label
            });
            // console.log(list)
            // result = list.join(" , ")

            return list 
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
.customStepper{
    
    padding: 0px !important;
}
.itemBorder{
    border-left: 3px solid rgb(0,177,250);
}
.q-stepper__header--contracted {
    min-height: 32px!important;
}
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
