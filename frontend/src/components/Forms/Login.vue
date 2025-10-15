<template>
  <div>
    <div class="fit row wrap justify-center items-center content-center q-pa-xl form-style">
        <div class="col-12 text-grey-8 q-mb-md">
          <div class="row items-center no-wrap">
            <q-avatar
              size="md"
              color="green"
              text-color="white"
              icon="admin_panel_settings"
            />
            <div class="q-ml-md text-weight-bold">
              Log your Credentials
            </div>
          </div>
        </div>
        <q-separator />
        <div class="col-12 text-grey-8">Encore with full of memories and achievements!</div>

        <div class="col-12 text-grey-8 q-mt-lg">
            <q-input
                v-model="form.username"
                @keypress.enter="submitLogin"
                :label="isStudent === 'student' ? 'Student Number' : 'Username'"
                v-bind="formRules.username"
                :placeholder="isStudent === 'student' ? 'Enter Student Number' : 'Enter Username'"
                :mask="isStudent === 'student' ? '##-##-####NNNN' : ''"
                outlined
                stack-label
            >
                <template v-slot:prepend>
                <q-icon name="ti-user" />
                </template>
            </q-input>
        </div>
        <div class="col-12 text-grey-8 q-mt-sm">
            <q-input
                v-model="form.password"
                v-bind="formRules.password"
                @keypress.enter="submitLogin"
                :type="isPwd ? 'password' : 'text'"
                label="Password"
                placeholder="***********"
                outlined
                stack-label
            >
                <template v-slot:prepend>
                  <q-icon name="ti-lock" />
                </template>
                <template v-slot:append>
                  <q-icon
                    :name="isPwd ? 'visibility_off' : 'visibility'"
                    class="cursor-pointer"
                    @click="isPwd = !isPwd"
                  />
                </template>
            </q-input>
        </div>
        <div class="col-12 q-mt-lg">
            <q-btn
                type="submit"
                class="full-width q-pa-md btn-custom-border"
                unelevated
                :loading="loginLoad"
                no-caps
                color="primary"
                label="Login"
                @click="submitLogin"
            />
        </div>
    </div>
  </div>
</template>

<script>
import { LocalStorage } from 'quasar'

export default {
  name:"LoginComponent",
  data() {
    return {
      tab: "login",
      isPwd: true,
      keepLogin: false,
      loginLoad: false,
      askUserType: false,
      rememberAnswwer: false,
      isStudent: "student",
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
  watch:{
    keepLogin(newVal){
      if(newVal){
        LocalStorage.set('rememberUserName', this.form.username);
      } else {
        LocalStorage.remove('rememberUserName');
      }
    },
    rememberAnswwer(newVal){
      if(newVal){
        LocalStorage.set('rememberUserType', this.isStudent);
      } else {
        LocalStorage.remove('rememberUserType');
      }
    }
  },
  created(){
    this.checkRememberUserType().then(() => {
      this.checkRememberUser()
    })
  },
  methods: {
    changeUserType(){
      LocalStorage.remove('rememberUserType');
      this.askUserType = false
    },
    async checkRememberUserType(){
      let remUser = LocalStorage.getItem('rememberUserType')

      if(remUser){
        this.isStudent = remUser
        this.askUserType = true
      }
    },
    async checkRememberUser(){
      let remUser = LocalStorage.getItem('rememberUserName')

      if(remUser){
        this.form.username = remUser
        this.keepLogin = true
      }
    },
    async submitLogin(){
      this.$q.loading.show();
      this.loginLoad = true;
      let vm = this;
      let payload = vm.form;
      this.$router.push('admin/dashboard')
      // this.$api.post('auth/login', payload).then(async (response) => {
      //   const data = {...response.data};
      //   if(!data.error){
      //     await LocalStorage.set('userData', data.jwt);
      //     if(Number(data.userType) === 2){
      //       this.$router.push('user/dashboard')
      //     } else if (Number(data.userType) === 3 || Number(data.userType) === 4) {
      //       this.$router.push('scholar-unit/dashboard')
      //     } else {
      //       this.$router.push('admin/dashboard')
      //     }

      //   } else {
      //     this.$q.notify({
      //       position: 'top-left',
      //       type: 'negative',
      //       message: data.title,
      //       caption: data.message,
      //       icon: 'report_problem'
      //     })
      //   }
      // })

      this.loginLoad = false;
      this.$q.loading.hide();
    }
  }
}
</script>

<style scoped>
.form-style {
  background: white;
  border-radius: 20px;
}
</style>
