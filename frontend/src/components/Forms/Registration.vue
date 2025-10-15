<template>
    <div class="row">
        <div class="col-12 text-grey-8 q-mb-md">Embrace the new year with a fresh opportunity! Join with us to create you future.</div>

        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
            <q-input
                v-model="form.username"
                label="Student Number"
                v-bind="formRules.username"
                placeholder="Student Number"
                mask="##-##-####NNNN"
                outlined 
                stack-label
            >
                <template v-slot:prepend>
                <q-icon name="ti-user" />
                </template>
            </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
            <q-input 
                v-model="form.password"
                v-bind="formRules.password"
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

        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.lastName"
            label="Last Name"
            placeholder="Enter Last Name"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.firstName"
            label="First Name"
            placeholder="Enter First Name"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.middleName"
            label="Middle Name"
            placeholder="Enter Middle Name"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.suffix"
            label="Suffix"
            placeholder="Ex. JR, II, III, IV, V"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-select 
            outlined 
            v-model="form.sex" 
            :options="sexOpt" 
            label="Gender" 
            stack-label 
            options-dense
          >
          </q-select>
        </div>
        <div class="col-12 col-md-4 text-grey-8 q-pa-xs">
          <q-select 
            outlined 
            v-model="form.civilStatus" 
            :options="statusOpt" 
            label="Civil Status" 
            stack-label 
            options-dense
          >
          </q-select>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.email"
            label="Email"
            placeholder="Enter Email Address"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.contact"
            label="Contact Number/Telephone"
            placeholder="Enter Contact Number"
            outlined 
            stack-label
            mask="###########"
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            type="date"
            v-model="form.dateOfBirth"
            label="Birth Date"
            placeholder="MM/DD/YYYY"
            outlined 
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.placeOfBirth"
            label="Birth Place"
            placeholder="Enter Place of Birth"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.address"
            type="textarea"
            label="Address"
            placeholder="Enter Permanent Address"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
          <q-select 
            outlined 
            v-model="form.course" 
            :options="courseOpt" 
            label="Course" 
            stack-label 
            options-dense
          >
          </q-select>
        </div>
        <div class="col-12 col-md-6 text-grey-8 q-pa-xs">
           <q-select 
            outlined 
            v-model="form.yrLvl" 
            :options="yrLvlOpt" 
            label="Year Level" 
            stack-label 
            options-dense
          >
          </q-select>
        </div>
        <div class="col-12 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.schoolAttended"
            label="Name of School"
            placeholder="Enter Last School Attended"
            outlined
            stack-label
          >
          </q-input>
        </div>
        <div class="col-12 text-grey-8 q-pa-xs">
          <q-input
            v-model="form.schoolAddress"
            type="textarea"
            label="School Address"
            placeholder="Enter School Address"
            outlined
            stack-label
          >
          </q-input>
        </div>


        <div class="col-12 q-mt-lg">
            <q-btn
                type="submit"
                class="full-width q-pa-md btn-custom-border" 
                unelevated
                :loading="loginLoad"
                :disable="enableRegister"
                no-caps 
                color="primary" 
                label="Register"
                @click="submitRegister"
            />
        </div>
    </div>
</template>

<script>
import { LocalStorage } from 'quasar'

export default {
  name:"RegisterComponent",
  data() {
    return {
      tab: "login",
      isPwd: true,
      keepLogin: false,
      loginLoad: false,
      sexOpt: [
        "Male",
        "Female",
        "Prefer Not To Say"
      ],
      statusOpt: [
        "Single",
        "Married",
        "Widowed",
        "Separated",
      ],
      yrLvlOpt: [
        "1st",
        "2nd",
        "3rd",
        "4th",
      ],
      courseOpt: [],
      form: {
        username: "",
        password: "",
        firstName: "",
        lastName: "",
        middleName: "",
        suffix: "",
        sex: "Male",
        civilStatus: "Single",
        email: "",
        contact: "",
        dateOfBirth: "",
        placeOfBirth: "",
        address: "",
        schoolAttended: "",
        schoolAddress: "",
        userType: 2,
        course: "",
        yrLvl: "",
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
  computed: {
    enableRegister(){
      
      let checkItemVal = 0;
      let unvalidate = "middleName,suffix,email,userType"
      for(const obj in this.form){
        if(
          this.form[obj] === "" &&
          !unvalidate.includes(obj)
        ){
          checkItemVal += 1
        }
      }

      return checkItemVal > 1
    }
  },
  created(){
    this.getCourses()
  },
  methods: {
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
    async submitRegister(){
      this.$q.loading.show();
      this.loginLoad = true;
      let vm = this;
      let payload = {
        ...vm.form,
        courseId: Number(this.form.course.value)
      };

      this.$api.post('users/register', payload).then(async (response) => {
        const data = {...response.data};
        if(!data.error){

          this.$q.notify({
            position: 'top-left',
            type: 'positive',
            message: data.title,
            caption: data.message,
            icon: 'mdi_information'
          })
          this.clearForm();
          this.$q.loading.hide();
          this.loginLoad = false;
        } else {
          this.$q.loading.hide();
          this.loginLoad = false;
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

    clearForm(){
      this.form = {
        username: "",
        password: "",
        firstName: "",
        lastName: "",
        middleName: "",
        suffix: "",
        sex: "Male",
        civilStatus: "Single",
        email: "",
        contact: "",
        dateOfBirth: "",
        placeOfBirth: "",
        address: "",
        schoolAttended: "",
        schoolAddress: "",
        userType: 2
      }
    }
  }
}
</script>
