<!-- =========================================================================================
    File Name: Chat.vue
    Description: Chat Application - Stay connected
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->


<template>
    <div
        id="chat-app"
        class="border border-solid d-theme-border-grey-light rounded relative overflow-hidden"
    >
        <vs-sidebar
            class="items-no-padding"
            parent="#chat-app"
            :click-not-close="clickNotClose"
            :hidden-background="clickNotClose"
            v-model="isChatSidebarActive"
            id="chat-list-sidebar"
        >
            <!-- USER PROFILE SIDEBAR -->
            <user-profile
                :active="activeProfileSidebar"
                :userId="userProfileId"
                class="user-profile-container"
                :isLoggedInUser="isLoggedInUserProfileView"
                @closeProfileSidebar="closeProfileSidebar"
            ></user-profile>

            <div class="chat__profile-search flex p-4">
                <div class="relative inline-flex">
                    <vs-avatar
                        class="m-0 border-2 border-solid border-white"
                        :src="
                            activeUser.photo ||
                            'https://ui-avatars.com/api/?name=' +
                                activeUser.name
                        "
                        size="40px"
                    />
                    <div
                        class="h-3 w-3 border-white border border-solid rounded-full absolute right-0 bottom-0"
                        :class="'bg-' + getStatusColor(true)"
                    ></div>
                </div>
                <vs-input
                    icon-no-border
                    icon="icon-search"
                    icon-pack="feather"
                    class="w-full mx-5 input-rounded-full"
                    placeholder="Search or start a new chat"
                    v-model="searchQuery"
                />

                <feather-icon
                    class="md:inline-flex lg:hidden -ml-3 cursor-pointer"
                    icon="XIcon"
                    @click="toggleChatSidebar(false)"
                />
            </div>

            <vs-divider class="d-theme-border-grey-light m-0" />
            <component
                :is="scrollbarTag"
                class="chat-scroll-area pt-4"
                :settings="settings"
                :key="$vs.rtl"
            >
                <!-- ACTIVE CHATS LIST -->
                <div class="chat__chats-list mb-8">
                    <h3 class="text-primary mb-5 px-4">Chats</h3>
                    <ul class="chat__active-chats bordered-items">
                        <li
                            class="cursor-pointer"
                            v-for="(contact, index) in chatContacts"
                            :key="index"
                            @click="updateActiveChatUser(contact.id)"
                        >
                            <chat-contact
                                showLastMsg
                                :contact="contact"
                                :lastMessaged="
                                    chatLastMessaged(contact.id).time
                                "
                                :unseenMsg="chatUnseenMessages(contact.id)"
                                :isActiveChatUser="isActiveChatUser(contact.id)"
                            ></chat-contact>
                        </li>
                    </ul>
                </div>

                <!-- ACTIVE GROUP LIST -->
                <div class="chat__chats-list mb-8">
                    <div class="flex justify-between">
                        <h3 class="text-primary mb-5 px-4">Groups</h3>
                        <vs-button
                            @click="popupActive = true"
                            color="primary"
                            type="filled"
                            size="small"
                            class="mb-5 mx-2"
                            >Create</vs-button
                        >
                    </div>

                    <vs-popup
                        class="holamundo"
                        title="Create new group"
                        :active.sync="popupActive"
                    >
                        <form @submit.prevent="createGroup">
                            <div class="demo-alignment">
                                <vs-input
                                    label="Group name"
                                    type="group"
                                    placeholder="Group name"
                                    v-model="newGroup.name"
                                    class="w-full"
                                />

                                <v-select
                                    class="w-full"
                                    placeholder="Add contacts"
                                    multiple
                                    :closeOnSelect="false"
                                    v-model="newGroup.contacts"
                                    :options="contacts"
                                    label="name"
                                    :dir="$vs.rtl ? 'rtl' : 'ltr'"
                                />

                                <!-- <loading-spinner v-if="isLoading" /> -->

                                <vs-button
                                    color="primary"
                                    type="filled"
                                    button="submit"
                                >
                                    <span>Submit</span>
                                </vs-button>
                            </div>
                        </form>
                    </vs-popup>

                    <ul class="chat__contacts bordered-items">
                        <li
                            class="cursor-pointer"
                            v-for="(group, index) in groups"
                            :key="index"
                            @click="updateActiveChatGroup(group.id)"
                        >
                            <chat-group
                                :group="group"
                                :showLastMsg="true"
                                :lastMessaged="
                                    groupChatLastMessaged(group.id)
                                        ? groupChatLastMessaged(group.id)
                                              .created_at
                                        : ''
                                "
                                :unseenMsg="groupChatUnseenMessages(group.id)"
                                :isActiveChatGroup="isActiveChatGroup(group.id)"
                            ></chat-group>
                        </li>
                    </ul>
                </div>

                <!-- CONTACTS LIST -->
                <div class="chat__contacts">
                    <h3 class="text-primary mb-5 px-4">Contacts</h3>
                    <ul class="chat__contacts bordered-items">
                        <li
                            class="cursor-pointer"
                            v-for="contact in contacts"
                            :key="contact.id"
                            @click="updateActiveChatUser(contact.id)"
                        >
                            <chat-contact :contact="contact"></chat-contact>
                        </li>
                    </ul>
                </div>
            </component>
        </vs-sidebar>

        <!-- RIGHT COLUMN -->
        <div
            class="chat__bg no-scroll-content chat-content-area border border-solid d-theme-border-grey-light border-t-0 border-r-0 border-b-0"
            :class="{
                'sidebar-spacer--wide': clickNotClose,
                'flex items-center justify-center':
                    activeChatUser === null && activeChatGroup === null,
            }"
        >
            <template v-if="activeChatUser || activeChatGroup">
                <div class="chat__navbar">
                    <chat-navbar
                        :isSidebarCollapsed="!clickNotClose"
                        :user-id="activeChatUser"
                        :group-id="activeChatGroup"
                        :isPinnedProp="isChatPinned"
                        @openContactsSidebar="toggleChatSidebar(true)"
                        @showProfileSidebar="showProfileSidebar"
                        @toggleIsChatPinned="toggleIsChatPinned"
                    ></chat-navbar>
                </div>
                <component
                    :is="scrollbarTag"
                    class="chat-content-scroll-area border border-solid d-theme-border-grey-light"
                    :settings="settings"
                    ref="chatLogPS"
                    :key="$vs.rtl"
                >
                    <div class="chat__log" ref="chatLog">
                        <chat-log
                            :userId="activeChatUser"
                            v-if="activeChatUser"
                        ></chat-log>

                        <chat-group-log
                            :groupId="activeChatGroup"
                            :activeUser="activeUser"
                            v-if="activeChatGroup"
                        ></chat-group-log>
                    </div>
                </component>
                <div class="chat__input flex flex-wrap p-4 bg-white">
                    <vs-input
                        class="flex-1"
                        placeholder="Type Your Message"
                        v-model="typedMessage"
                        maxlength="1000"
                        @keyup.enter="sendMsg"
                    />

                    <vs-dropdown
                        class="cursor-pointer"
                        vs-custom-content
                        vs-trigger-click
                    >
                        <feather-icon
                            icon="MicIcon"
                            class="cursor-pointer mx-2"
                            :svgClasses="['w-6', 'h-6']"
                        ></feather-icon>
                        <vs-dropdown-menu
                            class="dropdown-record-audio flex justify-center"
                        >
                            <!-- <h3>Record audio</h3> -->
                            <!-- <audio id="recordedAudio" src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"></audio> -->
                            <div
                                class="flex mb-3 justify-center items-center w-full"
                            >
                                <vs-button
                                    radius
                                    color="success"
                                    type="gradient"
                                    class="mx-2"
                                    id="record"
                                    icon-pack="feather" icon="icon-play"
                                    @click.prevent="startAudioRecording"
                                    ></vs-button
                                >
                                <vs-button
                                    radius
                                    color="danger"
                                    type="gradient"
                                    class="mx-2"
                                    id="stop-record"
                                    icon-pack="feather" icon="icon-stop-circle"
                                    @click.prevent="stopAudioRecording"
                                    ></vs-button
                                >
                                <audio id="recorded-audio" :src="recordedAudio.src" :controls="recordedAudio.controls" :autoplay="recordedAudio.autoplay"></audio>
                                <!-- <audio controls class="mx-2">
                                    <source
                                        src="https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3"
                                        type="audio/mpeg"
                                    />
                                    <p>
                                        Your browser doesn't support HTML5
                                        audio. Here is a
                                        <a href="myAudio.mp4"
                                            >link to the audio</a
                                        >
                                        instead.
                                    </p>
                                </audio> -->
                                <vs-button
                                    radius
                                    color="primary"
                                    type="gradient"
                                    class="mx-2"
                                    icon-pack="feather" icon="icon-send"
                                    @click.prevent="sendMsg"
                                    ></vs-button
                                >
                            </div>
                        </vs-dropdown-menu>
                    </vs-dropdown>

                    <feather-icon
                        icon="PaperclipIcon"
                        class="cursor-pointer mx-2"
                        :title="attachmentName ? attachmentName : ''"
                        :svgClasses="['w-6', 'h-6']"
                        @click.prevent="popupUpload = !popupUpload"
                    ></feather-icon>

                    <vs-popup
                        class="holamundo"
                        title="Upload file"
                        :active.sync="popupUpload"
                    >
                        <input
                            type="file"
                            class="w-full my-3"
                            id="file-upload"
                            @change="handleFile"
                        />

                        <vs-alert
                            color="danger"
                            title="Error"
                            :active="errors.hasOwnProperty('attachment')"
                            class="my-3"
                        >
                            {{ this.errors.attachment }}
                        </vs-alert>

                        <vs-button
                            color="primary"
                            type="relief"
                            class="my-3"
                            @click="popupUpload = !popupUpload"
                        >
                            <span>Ok</span>
                        </vs-button>
                    </vs-popup>

                    <vs-button
                        class="bg-primary-gradient ml-4"
                        type="filled"
                        @click="sendMsg"
                        v-if="!isLoading"
                        >Send</vs-button
                    >

                    <loading-spinner v-else />
                </div>
            </template>
            <template v-else>
                <div class="flex flex-col items-center">
                    <feather-icon
                        icon="MessageSquareIcon"
                        class="mb-4 bg-white p-8 shadow-md rounded-full"
                        svgClasses="w-16 h-16"
                    ></feather-icon>

                    <h4
                        class="py-2 px-4 bg-white shadow-md rounded-full cursor-pointer"
                        @click.stop="toggleChatSidebar(true)"
                    >
                        Start Conversation
                    </h4>
                </div>
            </template>
        </div>
    </div>
</template>

<script>
import ChatContact from "./ChatContact.vue";
import ChatLog from "./ChatLog.vue";
import ChatNavbar from "./ChatNavbar.vue";
import UserProfile from "./UserProfile.vue";
import VuePerfectScrollbar from "vue-perfect-scrollbar";
// import moduleChat from "@/store/chat/moduleChat.js";
import PopupDefault from "@/components/popup/PopupDefault.vue";
import vSelect from "vue-select";

import ChatGroup from "./ChatGroup.vue";

import ChatGroupLog from "./ChatGroupLog.vue";

import LoadingSpinner from "@/components/Spinner.vue";

export default {
    data() {
        return {
            active: true,
            isHidden: false,
            searchContact: "",
            activeProfileSidebar: false,
            activeChatUser: null,
            activeChatGroup: null,
            popupActive: false,
            userProfileId: -1,
            typedMessage: "",
            attachment: null,
            errors: {},
            isChatPinned: false,
            settings: {
                maxScrollbarLength: 60,
                wheelSpeed: 0.7,
            },
            clickNotClose: true,
            isChatSidebarActive: true,
            isLoggedInUserProfileView: false,

            newGroup: { name: "", contacts: [] },
            popupUpload: false,
            rec: null,
            audioChunks: [],
            recordedAudio: {
                src: '',
                controls: true,
                autoplay: true
            }
        };
    },
    watch: {
        windowWidth() {
            this.setSidebarWidth();
        },
        popupUpload() {
            if (this.attachment) {
                if (this.attachment.size > 5000000) {
                    this.attachment = null;
                    document.querySelector("#file-upload").value = "";
                }
            }
        },
    },
    computed: {
        chatLastMessaged() {
            return (userId) =>
                this.$store.getters["chat/chatLastMessaged"](userId);
        },
        chatUnseenMessages() {
            return (userId) => {
                const unseenMsg = this.$store.getters[
                    "chat/chatUnseenMessages"
                ](userId);
                if (unseenMsg) return unseenMsg;
            };
        },
        activeUser() {
            return this.$store.state.AppActiveUser;
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
        chatContacts() {
            return this.$store.getters["chat/chatContacts"];
        },
        contacts() {
            return this.$store.getters["chat/contacts"];
        },
        chats() {
            return this.$store.getters["chat/chats"];
        },
        searchQuery: {
            get() {
                return this.$store.state.chat.chatSearchQuery;
            },
            set(val) {
                this.$store.dispatch("chat/setChatSearchQuery", val);
            },
        },
        scrollbarTag() {
            return this.$store.getters.scrollbarTag;
        },
        isActiveChatUser() {
            return (userId) => userId === this.activeChatUser;
        },

        windowWidth() {
            return this.$store.state.windowWidth;
        },
        attachmentName() {
            return this.attachment ? this.attachment.name : "";
        },
        isLoading() {
            return this.$store.state.isLoading;
        },
        groupChatLastMessaged() {
            return (groupId) =>
                this.$store.getters["chat/groupChatLastMessaged"](groupId);
        },
        groupChatUnseenMessages() {
            return (groupId) => {
                const unseenMsg = this.$store.getters[
                    "chat/groupChatUnseenMessages"
                ](groupId);
                if (unseenMsg) return unseenMsg;
            };
        },
        isActiveChatGroup() {
            return (groupId) => groupId === this.activeChatGroup;
        },

        groups() {
            return this.$store.getters["chat/groups"];
        },
    },

    methods: {
        getUserStatus(isActiveUser) {
            return isActiveUser
                ? this.$store.state.AppActiveUser.status
                : this.contacts[this.activeChatUser].status;
        },
        closeProfileSidebar(value) {
            this.activeProfileSidebar = value;
        },
        updateActiveChatUser(contactId) {
            this.activeChatUser = contactId;
            this.activeChatGroup = null;
            if (
                this.$store.getters["chat/chatDataOfUser"](this.activeChatUser)
            ) {
                this.$store.dispatch("chat/markSeenAllMessages", contactId);
            }
            const chatData = this.$store.getters["chat/chatDataOfUser"](
                this.activeChatUser
            );
            if (chatData) this.isChatPinned = chatData.isPinned;
            else this.isChatPinned = false;
            this.toggleChatSidebar();
            this.typedMessage = "";
        },
        updateActiveChatGroup(groupId) {
            this.activeChatGroup = groupId;
            this.activeChatUser = null;

            this.toggleChatSidebar();
            this.typedMessage = "";
        },
        showProfileSidebar(userId, openOnLeft = false) {
            this.userProfileId = userId;
            this.isLoggedInUserProfileView = openOnLeft;
            this.activeProfileSidebar = !this.activeProfileSidebar;
        },
        handleFile(e) {
            this.attachment = e.target.files[0];

            const allowedExtensions = [
                "txt",
                "pdf",
                "doc",
                "ppt",
                "xls",
                "docx",
                "pptx",
                "xlsx",
                "rar",
                "zip",
                "jpg",
                "jpeg",
                "png",
                "gif",
            ];

            if (
                !allowedExtensions.includes(
                    this.attachment.name.toLowerCase().split(".").pop()
                )
            ) {
                this.errors.attachment = "File type is not allowed.";
            } else if (this.attachment.size > 5000000) {
                this.errors.attachment =
                    "File size must be lower than 5 megabytes.";
            } else {
                delete this.errors.attachment;
            }
        },
        async sendMsg() {
            if (!this.typedMessage && !this.attachment) return;
            if (this.attachment) if (this.attachment.size > 5000000) return;

            this.$store.commit("SET_IS_LOADING", true);

            console.log(this.attachment);

            const attachment = this.attachment;
            const chatMsg = {
                isPinned: this.isChatPinned,
                msg: {
                    textContent: this.typedMessage,
                    time: String(new Date()),
                    isSent: true,
                    isSeen: false,
                },
                id: this.activeChatUser,
                group_chat_id: this.activeChatGroup,
            };
            const payload = JSON.stringify(chatMsg);

            const formData = new FormData();

            formData.append("payload", payload);

            if (this.attachment) formData.append("attachment", attachment);

            // Clean inputs
            this.typedMessage = "";
            this.attachment = null;

            const fileInput = document.querySelector("#file-upload");
            if (fileInput) fileInput.value = "";

            try {
                if (this.activeChatUser && !this.activeChatGroup) {
                    await this.$store.dispatch(
                        "chat/sendChatMessage",
                        formData
                    );
                }

                if (!this.activeChatUser && this.activeChatGroup) {
                    await this.$store.dispatch(
                        "chat/sendGroupChatMessage",
                        formData
                    );
                }
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

            this.$store.commit("SET_IS_LOADING", false);

            const scroll_el = this.$refs.chatLogPS.$el || this.$refs.chatLogPS;
            scroll_el.scrollTop = this.$refs.chatLog.scrollHeight;
        },
        toggleIsChatPinned(value) {
            this.isChatPinned = value;
        },
        setSidebarWidth() {
            if (this.windowWidth < 1200) {
                this.isChatSidebarActive = this.clickNotClose = false;
            } else {
                this.isChatSidebarActive = this.clickNotClose = true;
            }
        },
        toggleChatSidebar(value = false) {
            if (!value && this.clickNotClose) return;
            this.isChatSidebarActive = value;
        },
        listenNewChats() {
            window.Echo.private("chats").listen("NewChatStarted", async (e) => {
                // console.log('new chat started', e)
                await this.$store.dispatch("chat/fetchChatContacts");
                await this.$store.dispatch("chat/fetchChats");
                this.listenNewChatMessages(e.chat);
            });
        },
        listenNewGroupChats() {
            window.Echo.private("group-chats").listen(
                "NewGroupChatCreated",
                async (e) => {
                    // console.log('new group chat started:', e)
                    await this.$store.dispatch("chat/fetchGroups");
                    this.listenNewGroupChatMessages(e.groupChat);
                }
            );
        },

        listenNewChatMessages(chat = null) {
            if (chat) {
                window.Echo.private("chat." + chat.id).listen(
                    "NewChatMessage",
                    (e) => {
                        // console.log('new message sent:', e);
                        this.$store.dispatch(
                            "chat/addNewChatMessage",
                            e.chatMessage
                        );
                    }
                );
                return;
            }
            for (const prop in this.chats) {
                // console.log(this.chats[prop].chat_id);
                window.Echo.private("chat." + this.chats[prop].chat_id).listen(
                    "NewChatMessage",
                    (e) => {
                        // console.log('new message sent:', e);
                        this.$store.dispatch(
                            "chat/addNewChatMessage",
                            e.chatMessage
                        );
                    }
                );
            }
        },

        listenNewGroupChatMessages(group = null) {
            // check if group exists and listen to it.
            if (group) {
                window.Echo.private("group-chat." + group.id).listen(
                    "NewGroupChatMessage",
                    (e) => {
                        // console.log('new group chat messages:', e)
                        this.$store.dispatch(
                            "chat/addNewGroupChatMessage",
                            e.groupChatMessage
                        );
                    }
                );
                return;
            }

            // Otherwise listen to the available list of groups in store state
            for (const group of this.groups) {
                window.Echo.private("group-chat." + group.id).listen(
                    "NewGroupChatMessage",
                    (e) => {
                        // console.log('new group chat messages:', e)
                        this.$store.dispatch(
                            "chat/addNewGroupChatMessage",
                            e.groupChatMessage
                        );
                    }
                );
            }
        },
        async createGroup() {
            // this.$store.commit('SET_IS_LOADING', true)
            this.$vs.loading();

            if (this.newGroup.name == "" || this.newGroup.contacts.length < 1) {
                return;
            }

            try {
                await this.$store.dispatch("chat/storeGroup", this.newGroup);
                this.popupActive = !this.popupActive;
            } catch (err) {
                console.log(err);
                if (err.response.status >= 400 && err.response.status < 500) {
                    this.newGroup.errors = err.response.data.errors;
                }
            }

            // this.$store.commit('SET_IS_LOADING', false)
            this.$vs.loading.close();
        },
        handleAudioRecording(stream){
                this.rec = new MediaRecorder(stream);

                this.rec.ondataavailable = (e) => {
                    this.audioChunks = [e.data];
                    if (this.rec.state == "inactive") {
                        console.log(this.audioChunks)
                        let blob = new Blob(this.audioChunks, {
                            type: "audio/mpeg",
                        });
                        this.attachment = new File(this.audioChunks, "recorded-audio.mp3");
                        this.recordedAudio.src = URL.createObjectURL(blob);
                        // sendData(blob);
                    }
                };
        },
        startAudioRecording(){
            this.rec.start();
        },
        stopAudioRecording(){
            this.rec.stop();
        },
        recordAudio() {

            navigator.mediaDevices
                .getUserMedia({ audio: true })
                .then((stream) => {
                    this.handleAudioRecording(stream);
                });
        },
    },
    components: {
        VuePerfectScrollbar,
        ChatContact,
        UserProfile,
        ChatNavbar,
        ChatLog,
        PopupDefault,
        vSelect,
        ChatGroup,
        ChatGroupLog,
        LoadingSpinner,
    },
    async created() {
        // this.$store.registerModule("chat", moduleChat);
        await this.$store.dispatch("chat/fetchContacts");
        await this.$store.dispatch("chat/fetchChatContacts");
        await this.$store.dispatch("chat/fetchGroups");
        await this.$store.dispatch("chat/fetchChats");
        this.setSidebarWidth();

        this.listenNewChats();
        this.listenNewChatMessages();

        this.listenNewGroupChats();
        this.listenNewGroupChatMessages();
    },
    beforeDestroy() {
        this.$store.unregisterModule("chat");
    },
    mounted() {
        this.$store.dispatch("chat/setChatSearchQuery", "");

        this.recordAudio();
    },
};
</script>


<style lang="scss">
@import "@sass/vuexy/apps/chat.scss";

.dropdown-record-audio {
    min-width: 350px;
}
</style>
