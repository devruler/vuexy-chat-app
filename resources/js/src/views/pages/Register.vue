<!-- =========================================================================================
File Name: RegisterJWT.vue
Description: Register Page for JWT
----------------------------------------------------------------------------------------
Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
  <div class="clearfix p-10">
    <vs-input

      label-placeholder="Name"
      name="displayName"
      placeholder="Name"
      v-model="displayName"
      class="w-full" />
    <!-- <span class="text-danger text-sm">{{ errors.first('displayName') }}</span> -->

    <vs-input

      name="email"
      type="email"
      label-placeholder="Email"
      placeholder="Email"
      v-model="email"
      class="w-full mt-6" />
    <!-- <span class="text-danger text-sm">{{ errors.first('email') }}</span> -->

    <vs-input
      ref="password"
      type="password"
      name="password"
      label-placeholder="Password"
      placeholder="Password"
      v-model="password"
      class="w-full mt-6" />
    <!-- <span class="text-danger text-sm">{{ errors.first('password') }}</span> -->

    <vs-input
      type="password"
      data-vv-as="password"
      name="confirm_password"
      label-placeholder="Confirm Password"
      placeholder="Confirm Password"
      v-model="confirm_password"
      class="w-full mt-6" />
    <!-- <span class="text-danger text-sm">{{ errors.first('confirm_password') }}</span> -->

    <vs-checkbox v-model="isTermsConditionAccepted" class="mt-6">I accept the terms & conditions.</vs-checkbox>
    <vs-button  type="border" to="/pages/login" class="mt-6">Login</vs-button>
    <vs-button class="float-right mt-6" @click="registerUserJWt">Register</vs-button>
  </div>
</template>

<script>
import axios from 'axios'
export default {
  data () {
    return {
      displayName: '',
      email: '',
      password: '',
      confirm_password: '',
      isTermsConditionAccepted: true
    }
  },
  computed: {
    // validateForm () {
    //   return !this.errors.any() && this.displayName !== '' && this.email !== '' && this.password !== '' && this.confirm_password !== '' && this.isTermsConditionAccepted === true
    // }
  },
  methods: {
    checkLogin () {
      // If user is already logged in notify
      if (this.$store.state.auth.isUserLoggedIn()) {

        // Close animation if passed as payload
        // this.$vs.loading.close()

        this.$vs.notify({
          title: 'Login Attempt',
          text: 'You are already logged in!',
          iconPack: 'feather',
          icon: 'icon-alert-circle',
          color: 'warning'
        })

        return false
      }
      return true
    },
    registerUserJWt () {
      // If form is not validated or user is already login return
    //   if (!this.checkLogin()) return

      const payload = {
        userDetails: {
          name: this.displayName,
          email: this.email,
          password: this.password,
          password_confirmation: this.confirm_password
        },
        notify: this.$vs.notify
      }

      axios.post('/register', payload.userDetails)
      .then(() => window.location.href = '/pages/login')
      .catch((err) => console.log(err))
    }
  }
}
</script>
