
<template>
  <q-layout view="lHh LpR lFf">

    <q-header reveal class="bg-primary">
      <q-toolbar>
        
        <!-- <q-btn 
          dense 
          flat 
          round 
          icon="menu"
          @click="toggleLeftDrawer" 
        /> -->
        
        <q-btn
          color="primary"
          round
          unelevated
          :icon="miniState ? 'ti-menu' : 'ti-menu-alt'"
          class="drawerBtn"
          @click="toggleLeftDrawer"
        />
        <q-item>
          <!-- <q-item-section side>
            <q-avatar round size="32px">
              <img src="https://cdn.quasar.dev/img/avatar.png">
            </q-avatar>
          </q-item-section> -->
          <!-- <q-item-section>
            <q-item-label>{{`${userDetails.fullName}`}}</q-item-label>
          </q-item-section> -->
        </q-item>
        
        
        <q-space />
        <q-btn class="q-mr-sm" round dense flat icon="ti-bell" @click="drawerRight = !drawerRight">
          <q-badge floating color="red" rounded transparent>
            {{ notifCount }}
          </q-badge>
        </q-btn>
        <!-- <q-btn class="q-mr-sm" round dense flat icon="ti-help" /> -->
        
        
      </q-toolbar>
    </q-header>

    <q-drawer 
      show-if-above 
      v-model="leftDrawerOpen"
      :mini="miniState"
      side="left" 
      bordered
    >
      <div class="row q-pa-md q-mt-lg">
        <div class="col-12 text-bold text-h5 text-orange">
          <q-img v-if="miniState" src="/imgs/ASCT_logo.png" />
          <q-img v-if="!miniState" src="/imgs/ASCT_logo2.png" />
          <!-- <q-icon name="ti-pie-chart" /> <span v-if="!miniState">Accounting IS</span> -->
        </div>
      </div>
      <!-- drawer content -->
      <!-- <Profile v-bind="userProfile" /> -->
      <q-separator dark />
      <SideNav 
        v-for="link in filteredMenus"
        :key="link.title"
        v-bind="link"
      />

      <div class="fixed-bottom q-pa-sm q-mb-md">
        <!-- <q-btn v-if="miniState" color="primary" icon="ti-layout-grid2" size="md" round>
          <q-menu
          style="min-width: 200px"
            anchor="bottom right"
            self="bottom left"
            :offset="[20, 20]"
          >
            <q-list style="min-width: 200px">
              <q-item  @click="logout">
                <q-item-section clickable v-ripple>
                  <q-item-section avatar>
                    <q-icon color="primary" name="ti-share-alt" />
                  </q-item-section>
                  <q-item-section>Logout</q-item-section>
                </q-item-section>
              </q-item>
            </q-list>
          </q-menu>
        </q-btn> -->

        
        <q-card v-if="!miniState" class="myMenuBar">
          <q-card-section class="fit row wrap justify-center items-center content-start myMenuBarSection">
            <q-btn-group flat dense spread>
              <q-btn @click="goToProfile" flat size="sm" rounded color="primary" icon="ti-user"/>
              <q-btn @click="modalStatus = true" flat rounded size="sm" color="secondary" icon="ti-lock"/>
              <q-btn flat rounded color="red" icon="ti-power-off" size="sm" @click="logout" />
            </q-btn-group>
          </q-card-section>
        </q-card>
      </div>
    </q-drawer>

    <q-page-container>
      <q-page class="q-pa-sm">
        <div style="height: 88vh;">
          <router-view />
        </div>
      </q-page>
    </q-page-container>

    <!-- <q-footer reveal class="bg-text-weight-thin text-blue-white-9 text-center q-pt-lg q-pb-lg">
      <div>{{ `Centralize Distribution and Sales Inventory System ©2023 Created by FWDS` }}</div>
    </q-footer> -->
    <q-dialog persistent v-model="modalStatus">
      <q-card style="width: 500px; max-width: 80vw;">
          <q-card-section>
              <div class="text-h6">Change Password</div>
          </q-card-section>

          <q-separator />

          <q-card-section style="max-height: 60vh" class="scroll">
             <q-form ref="passForm" class="q-gutter-md">
                  <q-input 
                      class="q-pt-md" 
                      bottom-slots
                      outlined
                      v-model="newPassword"
                      v-bind="formRules.matchPass"
                      :type="isPwd ? 'password' : 'text'"
                      label="New Password" 
                      :dense="true"
                  >
                      <template v-slot:prepend>
                          <q-icon name="password" />
                      </template>
                      <template v-slot:append>
                          <q-icon
                              :name="isPwd ? 'visibility_off' : 'visibility'"
                              class="cursor-pointer"
                              @click="isPwd = !isPwd"
                          />
                      </template>
                  </q-input>

                  <q-input 
                      class="q-pt-md" 
                      bottom-slots 
                      v-model="retypePass"
                      outlined
                      v-bind="formRules.matchPass"
                      :type="isPwd ? 'password' : 'text'"
                      label="Re-type Password" 
                      :dense="true"
                  >
                      <template v-slot:prepend>
                          <q-icon name="password" />
                      </template>
                      <!-- <template v-slot:append>
                          <q-icon
                              :name="isPwd ? 'visibility_off' : 'visibility'"
                              class="cursor-pointer"
                              @click="isPwd = !isPwd"
                          />
                      </template> -->
                  </q-input>
             </q-form>
          </q-card-section>

          <q-separator />

          <q-card-actions align="right">
              <q-btn flat label="Cancel" :loading="btnLoading" color="negative" @click="cancelChange" />
              <q-btn flat label="Submit" :loading="btnLoading" color="positive" @click="submitChangePass" />
          </q-card-actions>
      </q-card>
    </q-dialog>


    <q-drawer
      side="right"
      v-model="drawerRight"
      bordered
      overlay
      :width="400"
    >
      <q-scroll-area class="fit">
        <q-card
          flat
          class=" bg-white"
        >
            <q-card-section class="row items-center no-wrap">
                <div>
                  <div class="text-h5 text-weight-bold">Notifications</div>
                </div>
            </q-card-section>
            <q-separator />
            <q-card-section >
              <q-list>
                <q-item 
                  v-for="(notif, index) in notifList"
                  :key="index"
                  clickable
                  @click="getScholarshipDetails(notif.applicationId)"
                >
                <q-item-section avatar>
                  <q-icon v-if="notif.notifType === 'red'" name="mdi-bell-alert" :color="notif.notifType" />
                  <q-icon v-if="notif.notifType === 'green'" name="mdi-check-decagram" :color="notif.notifType" />
                </q-item-section>
                  <q-item-section>
                    <q-item-label>{{ `${notif.sender.firstName} ${notif.sender.lastName} `}}</q-item-label>
                    <q-item-label caption lines="2">{{ notif.message }}</q-item-label>
                    <q-item-label caption>{{ moment(notif.createdDate).format("LL LT") }}</q-item-label>
                  </q-item-section>
                  <!-- <q-separator inset /> -->
                </q-item>

                
              </q-list>
            </q-card-section>
        </q-card>
      </q-scroll-area>
    </q-drawer>


    <q-drawer
        side="right"
        v-model="drawerNotifClickRight"
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
                    <q-btn size="sm" rounded color="red" icon="ti-close" label="Close" @click="drawerNotifClickRight = !drawerNotifClickRight" />
                </q-card-section>
                <q-separator />
                <q-card-section >
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <span class="text-title text-bold">Personal Information</span>
                        </div>
                        <div class="col-3 q-mb-sm">
                            <span class="text-title text-bold">{{`${selectedProgram.data.student.firstName || '--'}`}}</span><br/>
                            <span class="text-caption text-grey">First Name</span>
                        </div>
                        <div class="col-3 q-mb-sm">
                            <span class="text-title text-bold">{{`${selectedProgram.data.student.middleName || '--'}`}}</span><br/>
                            <span class="text-caption text-grey">Middle Name</span>
                        </div>
                        <div class="col-3 q-mb-sm">
                            <span class="text-title text-bold">{{`${selectedProgram.data.student.lastName || '--'}`}}</span><br/>
                            <span class="text-caption text-grey">Last Name</span>
                        </div>
                        <div class="col-3 q-mb-sm">
                            <span class="text-title text-bold">{{`${selectedProgram.data.student.suffix || '--'}`}}</span><br/>
                            <span class="text-caption text-grey">suffix</span>
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
                                        <div v-if="(Number(userDetails.userType) === 3 || Number(userDetails.userType) === 4) && item.fileUploaded === undefined">
                                          <q-btn  @click="requestUpdate(item)" outline size="sm" rounded color="red" label="Request For Upload" />
                                        </div>
                                        <div v-if="(Number(userDetails.userType) === 3 || Number(userDetails.userType) === 4) && item.fileUploaded !== undefined">
                                            <q-btn
                                                @click="previewDocs(item.uploadFile, item.name, item)"
                                                outline 
                                                size="sm" 
                                                class="q-mr-xs" 
                                                rounded 
                                                color="primary" 
                                                label="View"
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
                    <q-btn  @click="openPrint" class="q-mr-sm" color="positive" label="Preview Form" />
                    <q-btn 
                        v-if="Number(userDetails.userType) === 3 && Number(selectedProgram.data.evaluatedBy) === 0"
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
                      v-if="(Number(userDetails.userType) === 3 || Number(userDetails.userType) === 4)"
                        @click="updateApplicationData('reject')"
                        outline 
                        rounded 
                        size="md"
                        no-caps
                        color="red" 
                        label="Reject Application" 
                    />
                    <q-btn 
                        v-if="Number(userDetails.userType) === 4 && Number(selectedProgram.data.approvedBy) === 0"
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
  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import SideNav from '../components/Templates/Sidenav.vue';
import Profile from '../components/Templates/Profile.vue';
// import Crumbs from '../components/Templates/Breadcrumbs.vue';
import printFormModal from '../components/Modals/PrintFormModel.vue';
import previewModal from '../components/Modals/PreviewDocument.vue';
import moment from 'moment';
import MenuJson from './menus.json'
import { jwtDecode } from 'jwt-decode';

const dateNow = moment().format('YYYY-MM-DD');

export default {
  name:"UserLoggedPage",
  data(){
    return {
      // linksList,
      selectedProgram: {},
      selectedFile: {},
      userProfile: null,
      notifList:[],
      courseOpt:[],
      notifCount:0,
      drawerRight: false,
      previewModalStatus: false,
      drawerNotifClickRight: false,
      menuCrumbs: [
        {label: '', icon: 'home', link: '/'},
        {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
      ],
      leftDrawerOpen: true,
      miniState: false,
      cutOffDate: "",
      notifStartDay: false,
      // Change Password
      modalStatus: false,
      newPassword:'',
      retypePass: '',
      isPwd: true,
      formRules: {
          matchPass: {
              rules: [
                  val => !!val || this.$t('validations_error.empty'),
                  val => val == this.newPassword || this.$t('validations_error.invalid_match'),
              ]
          },
      },
    }
  },
  watch:{
    drawerRight(newVal){
      this.seenNotif()
      this.getNotification()
    },
    drawerNotifClickRight(newVal){
      this.getFileStatus()
    },
    "$router.currentRoute.value": {
      handler: function(){
        this.checkModule();
      },
      deep: true
    }
  },
  components:{
    SideNav,
    Profile,
    printFormModal,
    previewModal
  },
  computed: {
    filteredMenus: function(){
      return MenuJson;
    },
    userDetails(){
      const user = LocalStorage.getItem('userData')
      return jwtDecode(user);
    }
  },
  created(){
    this.getNotificationCount()
    this.checkModule();
    this.getCourses();
  },
  methods: {
    moment,
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
    checkModule() {

      let filterMenuRoutes = MenuJson.filter((el) => {
        if(el.children){
          let childFilter = el.children.filter((child) => {
            return child.link === this.$router.currentRoute.value.name && this.userDetails.modules.includes(child.code)
          })
          return childFilter
        } else {
          return el.link === this.$router.currentRoute.value.name && this.userDetails.modules.includes(el.code)
        }
        
      })

      if(filterMenuRoutes.length === 0){
        this.$router.push('/401')
      }

      // return this.userDetails.modules.includes(id) ? true : false;
    },
    goToProfile(){
      this.$router.push('/user/profile')
    },
    async getNotification(){
      this.notifList = []
      this.$api.post('misc/get/notifications', {uId: this.userDetails.userId}).then((response) => {
        const data = {...response.data};
        if(!data.error){
          let dataList = data.list.sort((a, b) => +(a.createdDate < b.createdDate) || -(a.createdDate > b.createdDate))
          this.notifList = dataList
        }
      })
    },
    async getNotificationCount(){
      this.$api.post('misc/get/notifications/unseen', {uId: this.userDetails.userId}).then((response) => {
        const data = {...response.data};
        if(!data.error){
          this.notifCount = data.list.length
        }
      })
    },
    convertCourses(course){
      const res = this.courseOpt.filter(el => el.value === course)
      return res[0].label
      // console.log(list)
      // result = list.join(" , ")
    },
    async getScholarshipDetails($appId){

      if($appId === "0"){
        return 
      }

      this.$api.post('scholarship/applied/status/details', {applicationId: $appId}).then((response) => {
        console.log(response.data)
        this.selectedProgram = response.data
        this.drawerNotifClickRight = true
      })
    },
    async seenNotif(){
      this.$api.post('misc/update/notification', {uId: this.userDetails.userId, type: 'seen'}).then((response) => {
        const data = {...response.data};
        this.notifCount = 0
      })
    },
    changePassModal(){
      this.modalStatus = true;
    },
    cancelChange(){
      this.newPassword = '',
      this.retypePass = '',
      this.modalStatus = false;
    },
    toggleLeftDrawer () {
      this.miniState = !this.miniState
    },
    setCrumbsItem(val){
      this.menuCrumbs = val;
    },
    logout(){
      this.$q.dialog({
          title: 'Logout',
          message: 'Are you sure you want to logout?',
          ok: {
              label: 'Yes'
          },
          cancel: {
              label: 'No',
              color: 'negative'
          },
          persistent: true
      }).onOk(() => {
        localStorage.removeItem('userData');
        this.$router.push('/')
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
    async requestUpdate(item){
      return
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
                        fromUser: this.userDetails.userId,
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
                        uid: this.userDetails.userId,
                        scholarId: Number(this.selectedProgram.data.scholarId),
                        updateDetails: {
                            appStatus: 1,
                            evaluatedBy: this.userDetails.userId,
                            status: "Application has been moved for Approval",
                            dateEvaluated: moment().format("l LT"),
                        },
                        notification: {
                            applicationId: Number(this.selectedProgram.data.id),
                            toUser: this.selectedProgram.data.studentId,
                            fromUser: this.userDetails.userId,
                            message: 'Your application has been evaluated and move to the next step.',
                            notifType: 'green',
                            seen: 0,
                        }
                    }
                } else if(type === "reject"){
                    payload = {
                        appId: Number(this.selectedProgram.data.id),
                        actionType: type,
                        uid: this.userDetails.userId,
                        scholarId: Number(this.selectedProgram.data.scholarId),
                        updateDetails: {
                            appStatus: 3,
                            evaluatedBy: 0,
                            approvedBy: 0,
                            rejectedBy: this.userDetails.userId,
                            status: "Rejected",
                            remarks: "Application has been Rejected",
                            dateRejected: moment().format("l LT"),
                        },
                        notification: {
                            applicationId: Number(this.selectedProgram.data.id),
                            toUser: this.selectedProgram.data.studentId,
                            fromUser: this.userDetails.userId,
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
                        uid: this.userDetails.userId,
                        scholarId: Number(this.selectedProgram.data.scholarId),
                        email: this.selectedProgram.data.student.email,
                        updateDetails: {
                            appStatus: 2,
                            approvedBy: this.userDetails.userId,
                            status: "Application has been Approved",
                            dateApproved: moment().format("l LT"),
                        },
                        notification: {
                            applicationId: Number(this.selectedProgram.data.id),
                            toUser: this.selectedProgram.data.studentId,
                            fromUser: this.userDetails.userId,
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
    async submitChangePass(){
      let vm = this;
      let payload = {
          id: this.userDetails.userId,
          newPassword: this.retypePass
      };
      let compoDetails = this.$refs.passForm;

      compoDetails.validate().then(success => {

          if(!success){
              this.$q.notify({
                  color: 'negative',
                  position: 'top-right',
                  title: 'Incomplete Form',
                  message: 'Please fill the required fields',
                  icon: 'report_problem'
              })
              return false;
          } else {
              // Confirm
              this.$q.dialog({
                  title: 'Change Password?',
                  message: 'Are you sure you want to change your password?',
                  ok: {
                      label: 'Yes'
                  },
                  cancel: {
                      label: 'No',
                      color: 'negative'
                  },
                  persistent: true
              }).onOk(() => {
                  this.$q.loading.show({
                      message: 'Changing your password. Please wait...'
                  });

                  this.$api.post('users/changePassword', payload).then((response) => {
        
                      const data = {...response.data};
                      if(!data.error){
                          LocalStorage.remove('userData');
                          vm.$router.push('/')
                      } else {
                          vm.$q.notify({
                              color: 'negative',
                              position: 'top-right',
                              title:data.title,
                              message: vm.$t(`errors.${data.error}`),
                              icon: 'report_problem'
                          })
                      }
                  })

                  this.$q.loading.hide();

              })
          }
      })
    }
  }
}
</script>

<style scoped>
.myMenuBar{
  padding: 0% !important;
  border-radius: 20px;
}
.myMenuBarSection{
  padding: 3% !important;
}
.drawerBtn{
  position: absolute;
  left: -20px;
}
.bg-header-custom{
  background: #003978;
}
</style>