<template>
  <div class="meeting">
    <div class="container-zoom">
      <ZoomFrame :nickname="nickname" :meetingId="meetingId" :password="password" />
    </div>
  </div>
</template>

<script>
import ZoomFrame from "./ZoomFrame.vue";
export default {
  name: "meeting",
  props: [ 'password' ],
  data: function () {
      return {
          nickname: '',
          meetingId: '',
      }
  },
  components: {
    ZoomFrame,
  },
  created : function (){
      this.nickname = this.$route.query.nickname;
      this.meetingId = this.$route.query.meetingId;
  }
};
</script>

<style>
.container-zoom {
  width: 70%;
  height: 100%;
  /* margin: auto; */
  text-align: center;
}
/* #zmmtg-root {
  display: none;
} */
</style>
