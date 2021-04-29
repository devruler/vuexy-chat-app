<!-- =========================================================================================
	File Name: ChatNavbar.vue
	Description: Chat Application - Chat navbar
	----------------------------------------------------------------------------------------
	Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
	  Author: Pixinvent
	Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
    <div v-if="userId || groupId" class="chat__header">
        <vs-navbar
            class="p-4 flex navbar-custom"
            color="white"
            type="flat"
            v-if="userId"
        >
            <div class="relative flex mr-4">
                <feather-icon
                    icon="MenuIcon"
                    class="mr-4 cursor-pointer"
                    v-if="isSidebarCollapsed"
                    @click.stop="$emit('openContactsSidebar')"
                />
                <vs-avatar
                    class="m-0 border-2 border-solid border-white"
                    size="40px"
                    :src="userDetails.photo"
                    @click.stop="$emit('showProfileSidebar', userId)"
                />
                <div
                    class="h-3 w-3 border-white border border-solid rounded-full absolute right-0 bottom-0"
                    :class="'bg-' + getStatusColor(false)"
                ></div>
            </div>
            <h6>{{ userDetails.name }}</h6>
            <vs-spacer></vs-spacer>

            <feather-icon
                icon="VideoIcon"
                class="cursor-pointer mr-3"
                :svgClasses="['w-6', 'h-6']"
                @click="popupMeeting = true"
            ></feather-icon>

            <vs-dropdown class="cursor-pointer" vs-custom-content vs-trigger-click>
                <feather-icon
                        icon="SearchIcon"
                        class="cursor-pointer mr-3 flex items-center"
                        href.prevent
                        :svgClasses="['w-6', 'h-6']"
                    ></feather-icon>
                <vs-dropdown-menu class="dropdown-login">
                    <vs-input icon-pack="feather" icon="icon-search" placeholder="Search" />
                </vs-dropdown-menu>
            </vs-dropdown>

            <feather-icon
                icon="StarIcon"
                class="cursor-pointer"
                :svgClasses="[
                    { 'text-warning stroke-current': isPinnedLocal },
                    'w-6',
                    'h-6',
                ]"
                @click.stop="isPinnedLocal = !isPinnedLocal"
            ></feather-icon>
        </vs-navbar>

        <vs-navbar
            class="p-4 flex navbar-custom"
            color="white"
            type="flat"
            v-if="groupId"
        >
            <div class="relative flex mr-4">
                <feather-icon
                    icon="MenuIcon"
                    class="mr-4 cursor-pointer"
                    v-if="isSidebarCollapsed"
                    @click.stop="$emit('openContactsSidebar')"
                />
                <vs-avatar
                    class="m-0 border-2 border-solid border-white"
                    size="40px"
                    :src="'https://via.placeholder.com/150?text=G'"
                />
                <div
                    class="h-3 w-3 border-white border border-solid rounded-full absolute right-0 bottom-0"
                ></div>
            </div>
            <h6>{{ groupDetails.name }}</h6>
            <vs-spacer></vs-spacer>

            <feather-icon
                icon="VideoIcon"
                class="cursor-pointer mr-3"
                :svgClasses="['w-6', 'h-6']"
                @click="popupMeeting = true"
            ></feather-icon>


            <vs-dropdown class="cursor-pointer" vs-custom-content vs-trigger-click>
                    <feather-icon
                        icon="SearchIcon"
                        class="cursor-pointer mr-3 flex items-center"
                        href.prevent
                        :svgClasses="['w-6', 'h-6']"
                    ></feather-icon>
                <vs-dropdown-menu class="dropdown-login">
                    <vs-input icon-pack="feather" icon="icon-search" placeholder="Search" />
                </vs-dropdown-menu>
            </vs-dropdown>

            <feather-icon
                icon="StarIcon"
                class="cursor-pointer"
                :svgClasses="[
                    { 'text-warning stroke-current': isPinnedLocal },
                    'w-6',
                    'h-6',
                ]"
                @click.stop="isPinnedLocal = !isPinnedLocal"
            ></feather-icon>

        </vs-navbar>

        <vs-popup
            class="holamundo"
            title="Start new meeting"
            :active.sync="popupMeeting"
        >
            <form @submit.prevent="createMeeting">
                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <vs-input
                            class="w-full"
                            type="text"
                            label="Topic"
                            placeholder="Topic"
                            max="100"
                            v-model="meeting.topic"
                            required
                        />
                    </div>
                </div>

                <!-- <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <vs-input
                            class="w-full"
                            type="datetime-local"
                            :min="minDateTime"
                            label="Start time"
                            v-model="meeting.startTime"
                            required
                        />
                    </div>
                </div> -->

                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <vs-input
                            class="w-full"
                            type="number"
                            min="5"
                            label="Duration"
                            v-model="meeting.duration"
                            required
                        />
                    </div>
                </div>
                <div class="vx-row mb-6">
                    <div class="vx-col w-full">
                        <vs-input
                            class="w-full"
                            type="password"
                            min="4"
                            max="255"
                            placeholder="Password"
                            label="Password"
                            v-model="meeting.password"
                            required
                        />
                    </div>
                </div>

                <vs-alert
                    color="danger"
                    title="Error"
                    :active="meeting.error ? true : false"
                    class="my-3"
                >
                    {{ meeting.error }}
                </vs-alert>

                <div class="vx-row">
                    <div class="vx-col w-full">
                        <vs-button class="mr-3 mb-2" button="submit"
                            >Submit</vs-button
                        >
                    </div>
                </div>
            </form>
        </vs-popup>
    </div>
</template>

<script>
import axios from "axios";
export default {
    props: {
        userId: {
            type: Number,
            default: null,
        },
        groupId: {
            type: Number,
            default: null,
        },
        isPinnedProp: {
            type: Boolean,
            required: true,
        },
        isSidebarCollapsed: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            popupMeeting: false,
            meeting: {
                topic: "",
                startTime: null,
                duration: 40,
                password: "",
                invited: [],
                error: "",
            },
        };
    },
    computed: {
        isPinnedLocal: {
            get() {
                return this.isPinnedProp;
            },
            set(val) {
                const chatData = this.$store.getters["chat/chatDataOfUser"](
                    this.userId
                );
                if (chatData) {
                    const payload = { id: this.userId, value: val };
                    this.$store
                        .dispatch("chat/toggleIsPinned", payload)
                        .then(() => {
                            this.$emit("toggleIsChatPinned", val);
                        })
                        .catch((err) => {
                            console.error(err);
                        });
                } else {
                    this.$emit("toggleIsChatPinned", val);
                }
            },
        },
        userDetails() {
            return this.$store.getters["chat/contact"](this.userId);
        },
        groupDetails() {
            return this.$store.getters["chat/chatGroup"](this.groupId);
        },
        getStatusColor() {
            return (isActiveUser) => {
                const userStatus = this.getUserStatus(isActiveUser);

                if (userStatus === "online") {
                    return "success";
                } else if (userStatus === "offline") {
                    return "danger";
                } else {
                    return "grey";
                }
            };
        },
        othersInGroupChat() {
            return this.groupDetails
                ? this.groupDetails.users
                      .filter(
                          (user) =>
                              user.id !== this.$store.state.AppActiveUser.id
                      )
                      .map((user) => user.id)
                : [];
        },
        // minDateTime() {
        //     var now = new Date()
        //     now.setMinutes((now.getMinutes() + 30) - now.getTimezoneOffset())
        //     return now.toISOString().slice(0,16);
        // },
    },
    methods: {
        getUserStatus(isActiveUser) {
            return isActiveUser
                ? this.$store.state.AppActiveUser.status
                : this.userDetails.status;
        },
        async createMeeting() {
            this.meeting.error = "";
            this.$vs.loading();
            try {
                const res = await axios.post(
                    "/api/zoom/meetings",
                    this.meeting
                );
                console.log(res);
                this.meeting = {
                    topic: "",
                    startTime: null,
                    duration: 40,
                    invited: this.meeting.invited,
                    password: "",
                    error: "",
                };
                this.popupMeeting = false;
                await this.$router.push({
                    name: "meeting",
                    query: {
                        nickname: this.$store.state.AppActiveUser.name,
                        meetingId: res.data.id,
                        password: res.data.password,
                    },
                });
            } catch (err) {
                console.log(err);
                if (err.response.status >= 400 && err.response.status < 500) {
                    this.meeting.error = err.response.data.message;
                }
            }
            this.$vs.loading.close();
        },
    },
    watch: {
        userId() {
            if (this.userId) {
                this.meeting.invited = [];
                this.meeting.invited.push(this.userDetails.id);
            }
        },
        groupId() {
            if (this.groupId) {
                this.meeting.invited = [];
                this.meeting.invited = [...new Set(this.othersInGroupChat)];
            }
        },
    },
    mounted() {
        if (this.userId) {
            this.meeting.invited = [];
            this.meeting.invited.push(this.userDetails.id);
        } else if (this.groupId) {
            this.meeting.invited = [];
            this.meeting.invited = [...new Set(this.othersInGroupChat)];
        }
    },
};
</script>

