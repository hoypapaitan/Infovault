<template>
    <div class="q-pa-md" style="width: 100%;">
        <div class="row">
            <!-- Users Count Overview -->
            <div class="col-12 col-xs-12 col-sm-12 col-md-12  q-pa-sm">
                <q-card
                    flat
                    class="bg-white"
                >
                    <q-card-section class="fit row wrap justify-start items-center content-center">
                        <span class="text-h6 text-bold">
                            Approved Scholars
                            <br/>
                            <span class="text-caption text-grey">List of approved application of scholars in ASCOT</span>
                        </span>
                        <q-space />
                        <q-btn color="primary" @click="drawerPrint = true" label="Print Report" />
                    </q-card-section>
                </q-card>
            </div>
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
                    class="rounded-borders itemBorder "
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
                    row-key="name"
                    :filter="search"
                >  
                    <template v-slot:top>
                        <q-btn @click="backToProviderList" class="gt-xs q-mr-sm" flat color="red" rounded no-caps  dense icon="mdi-arrow-left" />
                        <q-btn @click="filterByCourse = !filterByCourse" class="gt-xs q-mr-sm" flat color="primary" rounded no-caps  dense icon="mdi-filter-cog" />
                        <q-select
                            v-if="filterByCourse"
                            class="col-3 text-grey-8 q-pa-xs" 
                            outlined 
                            dense
                            v-model="selectedCourse" 
                            :options="courseOpt" 
                            label="Course" 
                            stack-label 
                            options-dense
                        >
                        </q-select>
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
                                        <span class="text-bold">Status:</span> {{props.row.status}} <br/>
                                        <span class="text-bold">Remarks:</span> {{props.row.remarks || 'No Remarks'}} <br/>
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
            </div>
        </div>




        <!-- Drawer application preview -->
        <q-drawer
            side="right"
            v-model="drawerPrint"
            bordered
            overlay
            :width="1300"
        >
            <q-scroll-area class="fit">
                <q-card
                    flat
                    class=" bg-white"
                >
                    <q-card-section class="row items-center no-wrap">
                        <div>
                            <div class="text-h5 text-weight-bold">Print Preview</div>
                        </div>
                        <q-space />
                        <q-btn size="sm" rounded color="red" icon="ti-close" label="Close" @click="drawerPrint = !drawerPrint" />
                    </q-card-section>
                    <q-separator />
                    <q-card-section>
                        <iframe id="pdfPrintReport" style="width: 100%; height: 80dvh; border: none;"></iframe>
                    </q-card-section>
                </q-card>
            </q-scroll-area>
        </q-drawer>
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
                                <span class="text-caption text-grey">Fathers Name</span>
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
                                            <div v-if="item.fileUploaded === undefined">
                                                <q-btn outline size="sm" rounded color="red" label="Request For Upload" />
                                            </div>
                                            <div v-if="item.fileUploaded !== undefined">
                                                <q-btn
                                                    @click="previewDocs(item.uploadFile, item.name)"
                                                    outline 
                                                    size="sm" 
                                                    class="q-mr-xs" 
                                                    rounded 
                                                    color="primary" 
                                                    label="View"
                                                />
                                                <q-btn
                                                    v-if="item.verified === undefined"
                                                    @click="verifyDocument(item)"
                                                    outline size="sm" 
                                                    class="q-mr-xs" 
                                                    rounded color="green" 
                                                    label="Verify" 
                                                />
                                                <q-btn 
                                                    v-if="item.verified === undefined"
                                                    outline size="sm" 
                                                    rounded 
                                                    color="red" 
                                                    label="Request For Update" 
                                                />
                                            </div>
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
                        <q-btn class="q-mr-sm" color="positive" label="Preview Form" @click="openPrint" />
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
        <printFormModal 
            :modalStatus="printModal"
            :appData="appData"
            @updatePrintStatus="closePrintFormModal"
        />
        
    </div>
</template>

<script>
import moment from 'moment'
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import previewModal from '../../components/Modals/PreviewDocument.vue';
import printFormModal from '../../components/Modals/PrintFormModel.vue';
import { PDFDocument, StandardFonts, rgb } from 'pdf-lib'

const dateNow = moment().format('YYYY-MM-DD');

export default {
    name: 'UserDashboard',
    components:{
        previewModal,
        printFormModal
    },
    data(){
        return {
            drawerPrint: false,
            selectedProvider: '',
            selectedCourse: null,
            filterByCourse: false,
            providerList: [],
            printModal: false,
            drawerRight: false,
            selectedProgram: {},
            tableLoading: false,
            appData: {},
            itemsList: [],
            courseOpt: [],
            previewModalStatus: false,
            selectedFile: "",
        }
    },
    watch:{
        drawerPrint(newVal){
            if(newVal){
                let data = this.filteredList
                if(this.selectedProvider === ''){
                    data = this.itemsList
                }
                this.generatePdf(data)
            }
        },
        drawerRight(newVal){
            if(newVal){
                this.getFileStatus()
            }
        }
    },
    computed: {
        user: function(){
            const user = LocalStorage.getItem('userData')
            return jwtDecode(user);
        },
        filteredList(){
            if(this.selectedProvider === "All"){
                return this.itemsList
            } else {
                if(this.filterByCourse){
                    return this.itemsList.filter(
                        el => this.selectedProvider === el.title && this.selectedCourse.value === el.data.student.courseId
                    )
                } else {
                    return this.itemsList.filter(el => this.selectedProvider === el.title)
                }
            }
			
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
    },
    methods: {
        moment,
        backToProviderList(){
            this.selectedProvider = ""
        },
        viewApplicationList(item){
            this.selectedProvider = item.description
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
                        updateDetails: {
                            appStatus: 1,
                            evaluatedBy: this.user.userId,
                            status: "Application has been moved for Approval",
                            dateEvaluated: moment().format("l LT"),
                        }
                    }
                } else {
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        actionType: type,
                        updateDetails: {
                            appStatus: 2,
                            approvedBy: this.user.userId,
                            status: "Application has been Approved",
                            dateApproved: moment().format("l LT"),
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
        async verifyDocument(item){
            this.$q.loading.show();
            let payload = {
                fileId: item.fileId,
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
        },
        previewDocs(file, reqType){
            this.previewModalStatus = true
            this.selectedFile = {
                url: file,
                type: reqType
            }
        },
        closeFormModal(){
            this.previewModalStatus = false
        },
        closePrintFormModal(){
            this.printModal = false
        },
        openPrint(){
            let data = {
                student: {...this.selectedProgram.data.student},
                scholar: {...this.selectedProgram.data.scholarship},
                others: {...this.selectedProgram.data.familyBackground}
            }
            console.log(data)
            this.appData = data
            this.printModal = true
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
            } else if(Number(data.rejectedBy) > 0){
                res = 4
            }
            return res
        },
        async getList(){
            this.tableLoading = true
            this.itemsList = []

            this.$api.post('scholarship/declined/list', {uType: Number(this.user.userType)}).then((response) => {
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
                    this.selectedCourse = opt[0]
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

        async generatePdf(data){
            const url = '/docs/legalformat.pdf'
            const existingPdfBytes = await fetch(url).then(res => res.arrayBuffer())
            // Create a new PDFDocument
            const firstDoc = await PDFDocument.load(existingPdfBytes)
            const pdfDoc = await PDFDocument.create();
            const fontBold = await pdfDoc.embedFont(StandardFonts.HelveticaBold)
            let dataCount = data.length
            const itemPerPage = 40

            // loop how many pages
            for (let index = 1; index <= Math.ceil(dataCount / itemPerPage); index++) {
                const [firstDonorPage] = await pdfDoc.copyPages(firstDoc, [0])
                pdfDoc.addPage(firstDonorPage)
            }

            const pages = pdfDoc.getPages()
            pages.forEach((elpage, index) => {
                const { width, height } = elpage.getSize()
                elpage.drawText(`ASCOTS QUALIFIED SCHOLARS`, {
                    x: 20,
                    y: height - 25,
                    size: 14,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`${this.selectedProvider === 'All' ? this.selectedProvider + ' SCHOLARSHIP'  : this.selectedProvider} REPORT`, {
                    x: 20,
                    y: height - 40,
                    size: 12,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                
                elpage.drawText(`Student Number`, {
                    x: 15,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Student Name`, {
                    x: 110,
                    y: height - 80,
                    size: 9,
                    font:fontBold,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Year Lvl`, {
                    x: 190,
                    y: height - 80,
                    lineHeight: 10,
                    maxWidth: 230,
                    font:fontBold,
                    size: 9,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Address`, {
                    x: 230,
                    y: height - 80,
                    lineHeight: 10,
                    font:fontBold,
                    maxWidth: 230,
                    size: 9,
                    color: rgb(0, 0, 0),
                })
                
                elpage.drawText(`Course`, {
                    x: 370,
                    y: height - 80,
                    lineHeight: 10,
                    font:fontBold,
                    maxWidth: 230,
                    size: 9,
                    color: rgb(0, 0, 0),
                })
                elpage.drawText(`Scholarship Granted`, {
                    x: 490,
                    y: height - 80,
                    lineHeight: 10,
                    maxWidth: 230,
                    font:fontBold,
                    size: 9,
                    color: rgb(0, 0, 0),
                })
                

                let paginated = this.getPaginatedData(data, index+1, itemPerPage)
                let stdContentHeight = height - 230;
                for (let idx = 1; idx <= paginated.length; idx++) {
                    let edata = paginated[idx-1]
                    elpage.drawText(`${edata.studentNumber || '--'}`, {
                        x: 15,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.data.student.firstName} ${edata.data.student.lastName}`, {
                        x: 110,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.data.student.yrLvl}`, {
                        x: 190,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.data.student.address}`, {
                        x: 230,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    
                    let sCourse = this.courseOpt.filter(el => el.value === edata.data.student.courseId)
                    elpage.drawText(`${sCourse[0].label}`, {
                        x: 370,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })
                    elpage.drawText(`${edata.data.dateApproved}`, {
                        x: 490,
                        y: stdContentHeight + 135,
                        lineHeight: 10,
                        maxWidth: 230,
                        size: 9,
                        color: rgb(0, 0, 0),
                    })

                    stdContentHeight -= 20
                }
            })

            const pdfDataUri = await pdfDoc.saveAsBase64({ dataUri: true });
            document.getElementById('pdfPrintReport').src = pdfDataUri;
        },

        getPaginatedData(array, page, limit) {
            const offset = limit * (page - 1);
            const paginatedItems = array.slice(offset, limit * page);
            return paginatedItems
        }
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
