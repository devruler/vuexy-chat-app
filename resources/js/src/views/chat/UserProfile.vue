<!-- =========================================================================================
    File Name: UserProfile.vue
    Description: user profile sidebar
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
    <div id="parentx-demo-2">
        <vs-sidebar
            parent="#chat-app"
            :position-right="!isLoggedInUser"
            :hidden-background="false"
            v-model="activeLocal"
            id="chat-profile-sidebar"
            class="items-no-padding"
        >
            <div
                class="header-sidebar relative flex flex-col p-0"
                slot="header"
            >
                <feather-icon
                    icon="XIcon"
                    svgClasses="m-2 cursor-pointer absolute top-0 right-0"
                    @click="$emit('closeProfileSidebar', false)"
                ></feather-icon>

                <div class="relative inline-flex mx-auto mb-5 mt-6">
                    <vs-avatar
                        class="m-0 border-white border-2 border-solid shadow-md"
                        :src="
                            chatUser.photo ||
                            'https://ui-avatars.com/api/?name=' + chatUser.name
                        "
                        size="70px"
                    />
                    <div
                        class="h-5 w-5 border-white border-2 border-solid rounded-full absolute right-0 bottom-0"
                        :class="'bg-' + statusColor"
                    ></div>
                </div>

                <h4 class="mr-2 self-center">{{ chatUser.name }}</h4>
            </div>

            <component
                :is="scrollbarTag"
                class="scroll-area"
                :settings="settings"
                :key="$vs.rtl"
            >
                <div class="p-8">
                    <form @submit.prevent="updateUserStatus">
                        <h6 class="mb-2" :class="{ 'ml-4': isLoggedInUser }">
                            About
                        </h6>
                        <vs-textarea
                            class="mb-10"
                            counter="120"
                            maxlength="120"
                            :counter-danger.sync="counterDanger"
                            v-model="about"
                            rows="5"
                            v-if="isLoggedInUser"
                        />
                        <p v-else>{{ chatUser.about }}</p>

                        <div class="userProfile__status" v-if="isLoggedInUser">
                            <h6 class="mb-4">Status</h6>
                            <ul>
                                <li class="mb-2">
                                    <vs-radio
                                        v-model="status"
                                        vs-value="online"
                                        color="success"
                                        >Active</vs-radio
                                    >
                                </li>
                                <li class="mb-2">
                                    <vs-radio
                                        v-model="status"
                                        vs-value="do not disturb"
                                        color="danger"
                                        >Do Not Disturb</vs-radio
                                    >
                                </li>
                                <li class="mb-2">
                                    <vs-radio
                                        v-model="status"
                                        vs-value="away"
                                        color="warning"
                                        >Away</vs-radio
                                    >
                                </li>
                                <li class="mb-2">
                                    <vs-radio
                                        v-model="status"
                                        vs-value="offline"
                                        color="grey"
                                        >Offline</vs-radio
                                    >
                                </li>
                            </ul>
                        </div>
                        <vs-button
                            button="submit"
                            type="filled"
                            color="primary"
                            class="my-3"
                            >Update</vs-button
                        >
                    </form>
                </div>
            </component>
        </vs-sidebar>
    </div>
</template>

<script>
import VuePerfectScrollbar from "vue-perfect-scrollbar";

export default {
    props: {
        userId: {
            type: Number,
            required: true,
        },
        active: {
            type: Boolean,
            required: true,
        },
        isLoggedInUser: {
            type: Boolean,
            required: true,
        },
    },
    data() {
        return {
            counterDanger: false,
            settings: {
                // perfectscrollbar settings
                maxScrollbarLength: 60,
                wheelSpeed: 0.6,
            },
        };
    },
    computed: {
        chatUser() {
            return this.$store.getters["chat/chatUser"](this.userId);
        },
        activeLocal: {
            get() {
                return this.active;
            },
            set(value) {
                this.$emit("closeProfileSidebar", value);
            },
        },
        about: {
            get() {
                return this.chatUser.about;
            },
            set(value) {
                this.$store.dispatch("updateUserInfo", { about: value });
            },
        },
        status: {
            get() {
                return this.chatUser.status;
            },
            set(value) {
                this.$store.dispatch("updateUserInfo", { status: value });
            },
        },

        statusColor() {
            const userStatus = this.chatUser.status;

            if (userStatus === "online") {
                return "success";
            } else if (userStatus === "offline") {
                return "danger";
            } else {
                return "grey";
            }
        },
        scrollbarTag() {
            return this.$store.getters.scrollbarTag;
        },
    },
    methods: {
        async updateUserStatus() {
            this.$vs.loading();

            try {
                await this.$store.dispatch(
                    "chat/updateUserStatus",
                    this.chatUser
                );
            } catch (err) {
                if (err.response.status >= 400 && err.response.status < 500) {
                    this.$vs.notify({
                        title: "Message not sent",
                        text:
                            err.response.data.errors[
                                Object.keys(err.response.data.errors)[0]
                            ][0],
                        color: "danger",
                        iconPack: "feather",
                        icon: "icon-message-square",
                    });
                }
            }
            this.$vs.loading.close();
        },
    },
    components: {
        VuePerfectScrollbar,
    },
};
</script>

