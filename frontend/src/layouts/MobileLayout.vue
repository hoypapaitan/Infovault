
<template>
  <q-layout view="lHh LpR lFf">

    <q-header reveal class="bg-amber-7">
      <q-toolbar>
        
        <!-- <q-btn 
          dense 
          flat 
          round 
          icon="menu"
          @click="toggleLeftDrawer" 
        /> -->
        
        <q-btn
          color="amber-7"
          round
          unelevated
          :icon="miniState ? 'ti-menu' : 'ti-menu-alt'"
          @click="toggleLeftDrawer"
        />
        <q-item>
          <q-item-section side>
            <q-avatar round size="32px">
              <img src="https://cdn.quasar.dev/img/avatar.png">
            </q-avatar>
          </q-item-section>
          <q-item-section>
            <q-item-label>{{`${userDetails.fullName}`}}</q-item-label>
            <q-item-label caption class="capetalize">{{userDetails.userType}}</q-item-label>
          </q-item-section>
        </q-item>
        
        
        <q-space />
        <q-btn class="q-mr-sm" round dense flat icon="ti-bell">
          <q-badge floating color="red" rounded transparent>
            0
          </q-badge>
        </q-btn>
        <q-btn class="q-mr-sm" round dense flat icon="ti-help" />
        
      </q-toolbar>
    </q-header>

    <q-drawer 
      show-if-above 
      v-model="leftDrawerOpen"
      side="left" 
      bordered
    >
      <div class="row q-pa-md q-mt-lg">
        <div class="col-12 text-center text-bold text-h5 text-orange">
          <q-img v-if="!miniState" src="/imgs/bagamalogo.jpg" width="70%" />
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
        <q-card v-if="!miniState" class="myMenuBar">
          <q-card-section class="fit row wrap justify-center items-center content-start">
            <q-btn-group flat dense spread>
              <q-btn flat rounded color="primary" icon="ti-user"/>
              <q-btn flat rounded color="secondary" icon="ti-lock"/>
              <q-btn flat rounded color="positive" icon="ti-headphone-alt"/>
              <q-btn flat rounded color="warning" icon="ti-settings"/>
              <q-btn flat rounded color="red" icon="ti-power-off" @click="logout" />
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

  </q-layout>
</template>

<script>
import { LocalStorage, SessionStorage } from 'quasar'
import SideNav from '../components/Templates/Sidenav.vue';
import Profile from '../components/Templates/Profile.vue';
// import Crumbs from '../components/Templates/Breadcrumbs.vue';
import moment from 'moment';
import MenuJson from './menus.json'

const dateNow = moment().format('YYYY-MM-DD');

export default {
  name:"UserLoggedPage",
  data(){
    return {
      // linksList,
      userProfile: null,
      menuCrumbs: [
        {label: '', icon: 'home', link: '/'},
        {label: 'Dashboard', icon: 'dashboard', link: 'dashboard'}
      ],
      leftDrawerOpen: false,
      miniState: false,
      cutOffDate: "",
      notifStartDay: false
    }
  },
  mounted(){},
  components:{
    SideNav,
    Profile,
  },
  computed: {
    filteredMenus: function(){
      return MenuJson;
    },
    userDetails(){
      const user = LocalStorage.getItem('userData')
      return user;
    },
  },
  methods: {
    toggleLeftDrawer () {
      this.leftDrawerOpen = !this.leftDrawerOpen
    },
    setCrumbsItem(val){
      this.menuCrumbs = val;
    },
    logout(){
      localStorage.removeItem('userData');
      this.$router.push('/')
    }
  }
}
</script>

<style scoped>
.myMenuBar{
  padding: 0% !important;
  border-radius: 20px;
}
.drawerBtn{
  position: absolute;
  left: -20px;
}
.bg-header-custom{
  background: #003978;
}
</style>