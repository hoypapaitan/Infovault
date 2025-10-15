<template>
  <q-layout view="lHh LpR lFf" class="bottega-layout">

    <q-header class="bottega-header text-white" reveal>
      <q-toolbar class="text-center">
        <q-toolbar-title >
          <img class="q-mt-sm" width="20%" src="/imgs/ASCT_logo3.png">
        </q-toolbar-title>
      </q-toolbar>
      <q-toolbar class="text-center">
        <q-toolbar-title >
          <q-tabs
            v-model="tab"
            class="text-white"
            inline-label
          >
            <q-tab name="home" icon="home" label="Home" />
            <q-tab name="about" icon="info" label="About Us" />
            <q-tab name="contact" icon="email" label="Contact Us" />
            <q-tab name="login" icon="supervised_user_circle" label="Login" />
          </q-tabs>
        </q-toolbar-title>
      </q-toolbar>

    </q-header>

    <q-page-container>
      <q-page>
        <div class="bottega-hero-section row">
          <div v-if="tab === 'home'" class="col-xs-12 col-md-12 bottega-image-content relative-position q-pa-xl">
            <h1 class="bottega-main-headline text-white q-my-md">
              Graduates
              <br>
              School Year Book
              <br>
              Records
            </h1>

            <p class="text-white text-subtitle1 q-mt-lg">
              We empower students through quality education and real-world skills to help them thrive in life and career. <br/>
              Ready to grow, lead, and succeed? Take the first step and become an ASCOTian today!
            </p>

            <div class="bottega-live-events-btn-wrapper">
              <q-btn unelevated rounded size="lg" class="bg-teal-5 text-white">
                Request Year Book Access
              </q-btn>
            </div>
          </div>
          <div v-if="tab === 'login'" class="col-xs-12 col-md-12 bottega-image-content relative-position q-pa-xl">
            <div class="row">
              <div class="col-12 col-sm-12 col-md-4">
                <LoginForm />
              </div>
            </div>

          </div>
        </div>

        <q-page-sticky position="bottom-right" :offset="[18, 18]">
          <q-btn fab icon="question_mark" color="teal" class="bottega-agent-btn">
            <q-tooltip anchor="top middle" self="bottom middle">Help</q-tooltip>
          </q-btn>
        </q-page-sticky>
      </q-page>
    </q-page-container>
  </q-layout>
</template>

<script>
import { LocalStorage } from 'quasar'
import { jwtDecode } from 'jwt-decode';
import login from '../firebase/firebase-login'
import getDetailsDocument from '../firebase/firebase-get';
import LoginForm from '../components/Forms/Login.vue'

export default {
  name: 'MainLayout',
  components:{
    LoginForm
  },
  data: () => {
    return {
      tab: 'home',
      keepLogin: false,
      loginLoad: false,
      form: {
        username: "",
        password: "",
      },
      formRules: {
        username: {
          rules: [
            value => !!value || 'This field must be filled!',
          ]
        },
        password: {
          rules: [
            val => !!val || 'This field must be filled!',
          ]
        },
      },
    }
  },
  created(){
    let profile = LocalStorage.getItem('userData');
    if(profile){
      profile = jwtDecode(profile);
      if(Number(profile.userType) === 2){
        this.$router.push('user/dashboard')
      } else if (Number(profile.userType) === 3 || Number(profile.userType) === 4) {
        this.$router.push('scholar-unit/dashboard')
      } else {
        this.$router.push('admin/dashboard')
      }
    } else {
      this.$router.push('/')
    }
  },
  methods: {
    async submitLogin(){
      this.loginLoad = true;
      let vm = this;
      let payload = {
        email: vm.form.username,
        password: vm.form.password
      };

      await login(payload).then(async (res) => {
        let id = res.uid
        const user = await getDetailsDocument(`userProfile`, id)
        LocalStorage.set('userData', user);

        this.loginLoad = false;
        this.$router.push('user/dashboard')
      })
    },
    async submitLoginFB(){
      this.$q.loading.show();
      this.loginLoad = true;
      let vm = this;
      let payload = vm.form;

      this.$api.post('auth/login', payload).then(async (response) => {
        const data = {...response.data};
        if(!data.error){
          await LocalStorage.set('userData', data.jwt);
          this.$router.push('user/dashboard')
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

      this.loginLoad = false;
      this.$q.loading.hide();
    }
  }
}
</script>

<style lang="scss" scoped>
// Variables for easy color changes
$bottega-dark: #121c32; // Primary dark navy/blue
$bottega-opacity: #121c3299; // Primary dark navy/blue
$bottega-blue-overlay: rgba(0, 100, 180, 0.4); // For the gradient/overlay
$bottega-cta-green: #38b2ac; // Example for the live events button (teal-5)
$bottega-logo-color: #3f90ff; // A bright blue for the logo/accents

// HEADER STYLING
.bottega-header {
  background-color: $bottega-dark;
  // Box shadow to give it depth, if desired
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);

  .q-toolbar {
    min-height: 52px; // Adjust height for a tighter feel
    padding: 0 24px;
  }

  .bottega-logo {
    color: $bottega-logo-color;
    // Adjusting position for visual balance
    position: relative;
    top: -2px;
  }

  .q-toolbar-title {
    // Custom font for bottega university name (replace with actual font if known)
    font-family: 'Arial Black', sans-serif;
    letter-spacing: 0.5px;

    // Styling the 'university' and 'formerly' text
    .text-h6, .text-subtitle1 {
      font-weight: 700;
    }

    .bottega-formerly {
      font-weight: 300;
      opacity: 0.7;
    }
  }

  .q-item {
    padding: 0 12px;
    min-height: 48px;
    font-size: 0.85rem;
    font-weight: 600;
    opacity: 0.85;
    transition: opacity 0.3s;

    &:hover {
      opacity: 1;
      color: $bottega-logo-color; // Highlight on hover
    }
  }

  .bottega-call-btn {
      background-color: transparent !important;
      color: $bottega-logo-color;
      font-weight: 700;
  }
}

// HERO SECTION STYLING
.bottega-hero-section {
  min-height: 100dvh; // Ensure it takes up a good amount of vertical space
  position: relative;
  overflow: hidden;

  @media (max-width: $breakpoint-md-max) {
    min-height: 80vh;
  }
}

.bottega-text-content {
  // Use a dark color for the left side background if no full image background is used
  background-color: $bottega-dark;
  padding-top: 50px !important;
  padding-bottom: 50px !important;

  // Custom headline typography
  .bottega-main-headline {
    font-size: 5rem;
    line-height: 1.1;
    font-weight: 900;
    text-transform: uppercase;

    // Make the smaller screen headline slightly smaller
    @media (max-width: $breakpoint-sm-max) {
      font-size: 3.5rem;
    }
  }

  // CTA button styling for the blue primary button
  .bottega-cta-blue {
    background-color: $bottega-logo-color;
    color: white;
    font-weight: 600;
    text-transform: none;
    padding: 8px 20px;
  }

  // CTA button styling for the white secondary button
  .bottega-cta-white {
    background-color: white;
    color: $bottega-dark;
    font-weight: 600;
    text-transform: none;
    padding: 8px 20px;
    // Optional subtle shadow
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  }
}

.bottega-image-content {
  background-image: linear-gradient(
    to right,
    $bottega-opacity 5%, // Gradient transition from dark on the left
    $bottega-blue-overlay, // Blue overlay in the middle
    rgba(0, 0, 0, 0.1) // Lighter on the right
  ), url('/imgs/ASCOT-WEBSITE.png'); // Replace with the actual image path
  background-size: cover;
  background-position: center center;

  // Ensure the image content section is visible on mobile
  @media (max-width: $breakpoint-md-max) {
    min-height: 350px;
  }

  .bottega-live-events-btn-wrapper {
    position: absolute;
    bottom: 30%; // Adjust to position it like in the image
    left: 10%;
    transform: translateX(-50%); // Center it relative to its parent (optional)
    z-index: 10;
  }
}

.bottega-agent-btn {
  // Styling for the floating agent button to match the design
  background-color: $bottega-cta-green !important; // Teal/green color
  font-weight: 600;
}
</style>

<!-- <template>
  <q-layout view="lHh Lpr lFf">
    <q-header elevated class="bg-primary text-white q-pt-sm q-pb-sm q-pl-xl q-pr-xl">
      <q-toolbar>
        <q-toolbar-title>
          <img class="q-mt-xs" width="20%" src="/imgs/ASCT_logo2.png">
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-page-container>
      <q-page class="q-pl-xl q-pr-xl q-pt-md">
        <router-view />
      </q-page>
    </q-page-container>
  </q-layout>
</template>-->
