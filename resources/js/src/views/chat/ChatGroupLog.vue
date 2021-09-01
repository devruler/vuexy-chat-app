<!-- =========================================================================================
    File Name: ChatLog.vue
    Description: Chat Application - Log of chat
    ----------------------------------------------------------------------------------------
    Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
      Author: Pixinvent
    Author URL: http://www.themeforest.net/group/pixinvent
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
                    v-if="
                        !isSameDay(
                            msg.created_at,
                            messages[index - 1].created_at
                        )
                    "
                    class="msg-time"
                >
                    <span>{{ toDate(msg.created_at) }}</span>
                </vs-divider>
                <div
                    class="spacer mt-8"
                    v-if="
                        !hasSentPreviousMsg(
                            messages[index - 1].user_id ===
                                activeUser.id,
                            msg.user_id === activeUser.id
                        )
                    "
                ></div>
            </template>

            <div
                class="flex items-start"
                :class="[{ 'flex-row-reverse': msg.user_id === activeUser.id }]"
            >
                <template v-if="messages[index - 1]">
                    <template
                        v-if="
                            !hasSentPreviousMsg(
                                messages[index - 1].user_id ===
                                    activeUser.id,
                                msg.user_id === activeUser.id
                            ) ||
                            !isSameDay(
                                msg.time,
                                messages[index - 1].time
                            )
                        "
                    >
                        <vs-avatar
                            size="40px"
                            class="border-2 shadow border-solid border-white m-0 flex-shrink-0"
                            :class="
                                msg.user_id === activeUser.id
                                    ? 'sm:ml-5 ml-3'
                                    : 'sm:mr-5 mr-3'
                            "
                            :src="
                                senderImg(
                                    msg.user_id === activeUser.id
                                        ? activeUser.id
                                        : msg.user_id
                                )
                            "
                        ></vs-avatar>
                    </template>
                </template>

                <template v-if="index == 0">
                    <vs-avatar
                        size="40px"
                        class="border-2 shadow border-solid border-white m-0 flex-shrink-0"
                        :class="
                            msg.user_id === activeUser.id
                                ? 'sm:ml-5 ml-3'
                                : 'sm:mr-5 mr-3'
                        "
                        :src="
                            senderImg(
                                msg.user_id === activeUser.id
                                    ? activeUser.id
                                    : msg.user_id
                            )
                        "
                    ></vs-avatar>
                </template>

                <template v-if="messages[index - 1]">
                    <div
                        class="mr-16"
                        v-if="
                            !(
                                !hasSentPreviousMsg(
                                    messages[index - 1].user_id ===
                                        activeUser.id,
                                    msg.user_id === activeUser.id
                                ) ||
                                !isSameDay(
                                    msg.time,
                                    messages[index - 1].time
                                )
                            )
                        "
                    ></div>
                </template>

                <vx-tooltip :text="msgTime(msg.created_at)">
                    <div
                        class="msg break-words relative shadow-md rounded py-3 px-4 mb-2 rounded-lg max-w-sm"
                        :class="{
                            'bg-primary-gradient text-white':
                                msg.user_id === activeUser.id,
                            'border border-solid border-transparent bg-white':
                                msg.user_id !== activeUser.id,
                        }"
                    >
                        <span class="text-sm font-bold mb-3">{{
                            senderName(msg.user_id)
                        }}</span>
                        <br />
                        <span v-if="msg.content">{{ msg.content }}</span>
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
                                        'text-white':
                                            msg.user_id === activeUser.id,
                                        'text-black text-opacity-75':
                                            !msg.user_id === activeUser.id,
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
        groupId: {
            type: Number,
            required: true,
        },
        activeUser: {
            type: Object,
            required: true,
        },
    },
    computed: {
        chatData() {
            return this.$store.getters["chat/chatGroup"](this.groupId);
        },
        activeUserImg() {
            return this.$store.state.AppActiveUser.photo;
        },
        senderImg() {
            return (userId) => {
                if (userId === this.activeUser.id)
                    return this.$store.state.AppActiveUser.photo;
                else {
                    return this.$store.getters["chat/contact"](userId).photo;
                }
            };
        },
        senderName() {
            return (userId) => {
                if (userId === this.activeUser.id)
                    return this.$store.state.AppActiveUser.name;
                else {
                    return this.$store.getters["chat/contact"](userId).name;
                }
            };
        },
        hasSentPreviousMsg() {
            return (last_sender, current_sender) =>
                last_sender === current_sender;
        },
        messages(){
            return this.chatData.messages.filter(msg => msg.content.toLowerCase().includes(this.$store.state.chat.msgSearchQuery.toLowerCase()))
        }
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
                const chatElements = Array.from(document.querySelector('#component-chat-log').children);
                chatElements[chatElements.length - 1].scrollIntoView({behavior: "smooth", block: "center", inline: "nearest"});
            });
        },
        msgTime(time){
            return new Date(time).toLocaleString();
        }
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
