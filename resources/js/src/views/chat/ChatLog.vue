<!-- =========================================================================================
    File Name: ChatLog.vue
    Description: Chat Application - Log of chat
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/user/pixinvent
========================================================================================== -->

<template>
    <div id="component-chat-log" class="m-8" v-if="chatData">
        <div
            v-for="(msg, index) in messages"
            class="msg-grp-container"
            :key="index"
        >
            <!-- If previous msg is older than current time -->
            <template v-if="messages[index - 1]">
                <vs-divider
                    v-if="!isSameDay(msg.time, messages[index - 1].time)"
                    class="msg-time"
                >
                    <span>{{ toDate(msg.time) }}</span>
                </vs-divider>
                <div
                    class="spacer mt-8"
                    v-if="
                        !hasSentPreviousMsg(
                            messages[index - 1].isSent,
                            msg.isSent
                        )
                    "
                ></div>
            </template>

            <div
                class="flex items-start"
                :class="[{ 'flex-row-reverse': msg.isSent }]"
            >
                <template v-if="messages[index - 1]">
                    <template
                        v-if="
                            !hasSentPreviousMsg(
                                messages[index - 1].isSent,
                                msg.isSent
                            ) || !isSameDay(msg.time, messages[index - 1].time)
                        "
                    >
                        <vs-avatar
                            size="40px"
                            class="border-2 shadow border-solid border-white m-0 flex-shrink-0"
                            :class="
                                msg.isSent ? 'sm:ml-5 ml-3' : 'sm:mr-5 mr-3'
                            "
                            :src="senderImg(msg.isSent)"
                        ></vs-avatar>
                    </template>
                </template>

                <template v-if="index == 0">
                    <vs-avatar
                        size="40px"
                        class="border-2 shadow border-solid border-white m-0 flex-shrink-0"
                        :class="msg.isSent ? 'sm:ml-5 ml-3' : 'sm:mr-5 mr-3'"
                        :src="senderImg(msg.isSent)"
                    ></vs-avatar>
                </template>

                <template v-if="messages[index - 1]">
                    <div
                        class="mr-16"
                        v-if="
                            !(
                                !hasSentPreviousMsg(
                                    messages[index - 1].isSent,
                                    msg.isSent
                                ) ||
                                !isSameDay(msg.time, messages[index - 1].time)
                            )
                        "
                    ></div>
                </template>

                <vx-tooltip :text="msgTime(msg.time)">
                    <div
                        class="msg break-words relative shadow-md rounded py-3 px-4 mb-2 rounded-lg max-w-sm"
                        :class="{
                            'bg-primary-gradient text-white': msg.isSent,
                            'border border-solid border-transparent bg-white': !msg.isSent,
                        }"
                    >
                        <span v-if="msg.textContent">{{
                            msg.textContent
                        }}</span>
                        <div v-if="msg.attachment">
                            <hr
                                class="my-3 border border-grey border-opacity-25"
                            />
                            <audio v-if="msg.attachment.extension == 'webm'" :src="msg.attachment.path" controls></audio>
                            <a
                                :href="msg.attachment.path"
                                class="flex align-items text-sm"
                                target="_blank"
                                v-else
                            >
                                <feather-icon
                                    icon="PaperclipIcon"
                                    class="cursor-pointer text-grey mr-2"
                                    :svgClasses="['w-4', 'h-4']"
                                ></feather-icon>
                                <span
                                    :class="{
                                        'text-white': msg.isSent,
                                        'text-black text-opacity-75': !msg.isSent,
                                    }"
                                    >{{ msg.attachment.name }}</span
                                >
                            </a>
                        </div>
                    </div>
                </vx-tooltip>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        userId: {
            type: Number,
            required: true,
        },
    },
    computed: {
        chatData() {
            return this.$store.getters["chat/chatDataOfUser"](this.userId);
        },
        activeUserImg() {
            return this.$store.state.AppActiveUser.photo;
        },
        // senderImg() {
        //     return (isSentByActiveUser) => {
        //         if (isSentByActiveUser)
        //             return this.$store.state.AppActiveUser.photo;
        //         else
        //             return this.$store.getters["chat/contact"](this.userId)
        //                 .photo;
        //     };

        senderImg() {
            return (isSentByActiveUser) => {
                if (isSentByActiveUser)
                    return this.$store.state.AppActiveUser.photo
                    ? this.$store.state.AppActiveUser.photo
                    : this.avatarFromName(this.$store.state.AppActiveUser.name);
                else
                    return this.$store.getters["chat/contact"](this.userId  ).photo
                        ? this.$store.getters["chat/contact"](this.userId).photo
                        : this.avatarFromName(this.$store.getters["chat/contact"](this.userId).name) ;
            };
        },
        hasSentPreviousMsg() {
            return (last_sender, current_sender) =>
                last_sender === current_sender;
        },
        messages() {
            return this.chatData.msg.filter((msg) =>
                msg.textContent
                    .toLowerCase()
                    .includes(
                        this.$store.state.chat.msgSearchQuery.toLowerCase()
                    )
            );
        },
    },
    methods: {
        isSameDay(time_to, time_from) {
            const date_time_to = new Date(Date.parse(time_to));
            const date_time_from = new Date(Date.parse(time_from));
            return (
                date_time_to.getFullYear() === date_time_from.getFullYear() &&
                date_time_to.getMonth() === date_time_from.getMonth() &&
                date_time_to.getDate() === date_time_from.getDate()
            );
        },
        toDate(time) {
            const locale = "en-us";
            const date_obj = new Date(Date.parse(time));
            const monthName = date_obj.toLocaleString(locale, {
                month: "short",
            });
            return `${date_obj.getDate()} ${monthName}`;
        },
        scrollToBottom() {
            this.$nextTick(() => {
                this.$parent.$el.scrollTop = this.$parent.$el.scrollHeight;
            });
        },
        scrollToMsgOnUpdate() {
            this.$nextTick(() => {
                const chatElements = Array.from(
                    document.querySelector("#component-chat-log").children
                );
                chatElements[chatElements.length - 1].scrollIntoView({
                    behavior: "smooth",
                    block: "center",
                    inline: "nearest",
                });
            });
        },
        msgTime(time) {
            return new Date(time).toLocaleString();
        },
        avatarFromName(name) {
            return (
                "https://ui-avatars.com/api/?name=" +
                name.split(" ").join("+") +
                "&background=random"
            );
        },
    },
    updated() {
        this.scrollToBottom();
        this.scrollToMsgOnUpdate();
    },
    mounted() {
        this.scrollToBottom();
    },
};
</script>
