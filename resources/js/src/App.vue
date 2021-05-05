<!-- =========================================================================================
  File Name: App.vue
  Description: Main vue file - APP
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div id="app" :class="vueAppClasses">
        <router-view @setAppClasses="setAppClasses" />
    </div>
</template>

<script>
import themeConfig from "@/../themeConfig.js";
import axios from "axios";

// import moduleChat from '@/store/chat/moduleChat'

export default {
    data() {
        return {
            vueAppClasses: [],
            presentUsers: null,
        };
    },
    watch: {
        "$store.state.theme"(val) {
            this.toggleClassInBody(val);
        },
        "$vs.rtl"(val) {
            document.documentElement.setAttribute("dir", val ? "rtl" : "ltr");
        },
    },
    methods: {
        toggleClassInBody(className) {
            if (className === "dark") {
                if (document.body.className.match("theme-semi-dark"))
                    document.body.classList.remove("theme-semi-dark");
                document.body.classList.add("theme-dark");
            } else if (className === "semi-dark") {
                if (document.body.className.match("theme-dark"))
                    document.body.classList.remove("theme-dark");
                document.body.classList.add("theme-semi-dark");
            } else {
                if (document.body.className.match("theme-dark"))
                    document.body.classList.remove("theme-dark");
                if (document.body.className.match("theme-semi-dark"))
                    document.body.classList.remove("theme-semi-dark");
            }
        },
        setAppClasses(classesStr) {
            this.vueAppClasses.push(classesStr);
        },
        handleWindowResize() {
            this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);

            // Set --vh property
            document.documentElement.style.setProperty(
                "--vh",
                `${window.innerHeight * 0.01}px`
            );
        },
        handleScroll() {
            this.$store.commit("UPDATE_WINDOW_SCROLL_Y", window.scrollY);
        },
        getUserInfo() {
            axios
                .get("/api/user")
                .then((res) => {
                    let data = res.data;
                    this.$store.dispatch("updateUserInfo", data);
                })
                .catch((err) => console.log(err));
        },
        listenOnlineOffline() {
            Echo.join("user-status")
                .here(async (users) => {
                    try {
                        const res = await axios.put("/api/users/status", {
                            users,
                        });
                        const updatedUsers = res.data.users;

                        const currentUser = updatedUsers.find(
                            (user) =>
                                user.id === this.$store.state.AppActiveUser.id
                        );
                        const otherUsers = updatedUsers.filter(
                            (user) =>
                                user.id !== this.$store.state.AppActiveUser.id
                        );

                        this.$store.commit("UPDATE_USER_INFO", currentUser);
                        this.$store.commit("chat/UPDATE_CONTACTS", otherUsers);
                    } catch (err) {
                        console.log(err);
                    }
                })
                .joining((user) => {
                    user.status = "online";
                    console.log("hi from joining", user);
                    if (this.$store.state.AppActiveUser.id === user.id)
                        this.$store.commit("UPDATE_USER_INFO", user);
                    else this.$store.commit("chat/UPDATE_CONTACT_STATUS", user);
                })
                .leaving((user) => {
                    user.status = "offline";
                    console.log("hi from leaving", user);
                    if (this.$store.state.AppActiveUser.id === user.id)
                        this.$store.commit("UPDATE_USER_INFO", user);
                    else this.$store.commit("chat/UPDATE_CONTACT_STATUS", user);
                });
        },
        listenNewMeeting() {
            window.Echo.private("meeting").listen(
                "NewMeetingStarted",
                async (e) => {
                    console.log("new meeting:", e);
                    if (
                        e.meeting.invited.includes(
                            this.$store.state.AppActiveUser.id
                        )
                    ) {
                        this.$vs.dialog({
                            type: "confirm",
                            color: "success",
                            title: "Meeting Invitation",
                            text:
                                "You've been invited to a meeting, join meeting?",
                            accept: () =>
                                this.$router.push({
                                    name: "meeting",
                                    query: {
                                        nickname: this.$store.state
                                            .AppActiveUser.name,
                                        meetingId: e.meeting.id,
                                    },
                                    params: { password: e.meeting.password },
                                }),
                        });
                    }
                }
            );
        },
    },
    async mounted() {
        //   this.$store.registerModule("chat", moduleChat);
        this.toggleClassInBody(themeConfig.theme);
        this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);

        const vh = window.innerHeight * 0.01;
        // Then we set the value in the --vh custom property to the root of the document
        document.documentElement.style.setProperty("--vh", `${vh}px`);

        // get user info
        await this.getUserInfo();

        // Listen and update online and offline status
        this.listenOnlineOffline();

        // listen to new meetings
        this.listenNewMeeting();
    },
    async created() {
        const dir = this.$vs.rtl ? "rtl" : "ltr";
        document.documentElement.setAttribute("dir", dir);

        window.addEventListener("resize", this.handleWindowResize);
        window.addEventListener("scroll", this.handleScroll);
    },
    destroyed() {
        window.removeEventListener("resize", this.handleWindowResize);
        window.removeEventListener("scroll", this.handleScroll);
    },
};
</script>
