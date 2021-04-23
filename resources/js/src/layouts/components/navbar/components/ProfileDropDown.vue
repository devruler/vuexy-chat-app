<template>
    <div class="the-navbar__user-meta flex items-center">
        <div class="text-right leading-tight hidden sm:block">
            <p class="font-semibold">{{ activeUserInfo.name }}</p>
            <small>Available</small>
        </div>

        <vs-dropdown vs-custom-content vs-trigger-click class="cursor-pointer">
            <div class="con-img ml-3">
                <!-- <img v-if="activeUserInfo.photo" key="onlineImg" :src="activeUserInfo.photo || ('https://ui-avatars.com/api/?name=' + activeUserInfo.name)" alt="user-img" width="40" height="40" class="rounded-full shadow-md cursor-pointer block" /> -->
                <!-- <img key="onlineImg" :src="activeUserInfo.photo || ('https://ui-avatars.com/api/?name=' + activeUserInfo.name)" alt="user-img" width="40" height="40" class="rounded-full shadow-md cursor-pointer block" /> -->
                <div class="relative inline-flex">
                    <!-- <vs-avatar
                        class="m-0 border-2 border-solid border-white"
                        :src="activeUser.photo || ('https://ui-avatars.com/api/?name=' + activeUser.name)"
                        size="40px"
                        @click="showProfileSidebar(Number(activeUser.id), true)"
                    /> -->
                    <vs-avatar
                        key="onlineImg"
                        class="m-0 border-2 border-solid border-white shadow-md cursor-pointer block"
                        :src="
                            activeUserInfo.photo ||
                            'https://ui-avatars.com/api/?name=' +
                                activeUserInfo.name
                        "
                        size="40px"
                    />
                    <div
                        class="h-3 w-3 border-white border border-solid rounded-full absolute right-0 bottom-0"
                        :class="'bg-' + getStatusColor()"
                    ></div>
                </div>
            </div>

            <vs-dropdown-menu class="vx-navbar-dropdown">
                <ul style="min-width: 9rem">
                    <li
                        class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
                    >
                        <feather-icon icon="UserIcon" svgClasses="w-4 h-4" />
                        <span class="ml-2">Profile</span>
                    </li>

                    <li
                        class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
                    >
                        <feather-icon icon="MailIcon" svgClasses="w-4 h-4" />
                        <span class="ml-2">Inbox</span>
                    </li>

                    <li
                        class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
                    >
                        <feather-icon
                            icon="CheckSquareIcon"
                            svgClasses="w-4 h-4"
                        />
                        <span class="ml-2">Tasks</span>
                    </li>

                    <li
                        class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
                    >
                        <feather-icon
                            icon="MessageSquareIcon"
                            svgClasses="w-4 h-4"
                        />
                        <span class="ml-2">Chat</span>
                    </li>

                    <li
                        class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
                    >
                        <feather-icon icon="HeartIcon" svgClasses="w-4 h-4" />
                        <span class="ml-2">Wish List</span>
                    </li>

                    <vs-divider class="m-1" />

                    <li
                        class="flex py-2 px-4 cursor-pointer hover:bg-primary hover:text-white"
                        @click="logout"
                    >
                        <feather-icon icon="LogOutIcon" svgClasses="w-4 h-4" />
                        <span class="ml-2">Logout</span>
                    </li>
                </ul>
            </vs-dropdown-menu>
        </vs-dropdown>
    </div>
</template>

<script>
import axios from "axios";
export default {
    computed: {
        activeUserInfo() {
            return this.$store.state.AppActiveUser;
        },
        getStatusColor() {
            return () => {
                const userStatus = this.activeUserInfo.status;

                if (userStatus === "online") {
                    return "success";
                } else if (userStatus === "offline") {
                    return "danger";
                } else {
                    return "grey";
                }
            };
        },
    },
    methods: {
        logout() {
            localStorage.removeItem("userInfo");

            axios.post("/logout")
            .then(() => (window.location.href = "/pages/login"))
            .catch((err) => console.log(err));
        },
    },
};
</script>
