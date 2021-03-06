/*=========================================================================================
  File Name: moduleChatGetters.js
  Description: Chat Module Getters
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

export default {
    chatDataOfUser: state => id => {
        return state.chats[Object.keys(state.chats).find(key => Number(key) === id)]
    },


    chatContacts: (state, getters) => {
        const chatContacts = state.chatContacts.filter((contact) => contact.name.toLowerCase().includes(state.chatSearchQuery.toLowerCase()))

        chatContacts.sort((x, y) => {
            const timeX = getters.chatLastMessaged(x.id).time
            const timeY = getters.chatLastMessaged(y.id).time
            return new Date(timeY) - new Date(timeX)
        })

        return chatContacts.sort((x, y) => {
            const chatDataX = getters.chatDataOfUser(x.id)
            const chatDataY = getters.chatDataOfUser(y.id)
            if (chatDataX && chatDataY) return chatDataY.isPinned - chatDataX.isPinned
            else return 0
        })
    },

    // chatMessages: (state, getters) => {
    //     return state.chats
    // },

    contacts: (state) => state.contacts.filter((contact) => contact.name.toLowerCase().includes(state.chatSearchQuery.toLowerCase())),

    contact: (state) => contactId => state.contacts.find((contact) => contact.id === contactId),

    chats: (state) => state.chats,

    chatUser: (state, getters, rootState) => id => state.contacts.find((contact) => contact.id === id) || rootState.AppActiveUser,

    chatLastMessaged: (state, getters) => id => {
        if (getters.chatDataOfUser(id)) return getters.chatDataOfUser(id).msg.slice(-1)[0]
        else return false
    },

    chatUnseenMessages: (state, getters) => id => {
        let unseenMsg = 0
        const chatData = getters.chatDataOfUser(id)
        if (chatData) {
            chatData.msg.map((msg) => {
                if (!msg.isSeen && !msg.isSent) unseenMsg++
            })
        }
        return unseenMsg
    },

    /* --- Group chat --- */

    groups: (state) => state.groups,

    // Get chat group
    chatGroup: (state) => id => {
        let group = state.groups.find((group) => group.id === id)
        return group
    },

    groupChatLastMessaged: (state, getters) => id => {
        if (getters.chatGroup(id)) return getters.chatGroup(id).messages.slice(-1)[0]
        else return false
    },

    groupChatUnseenMessages: (state, getters) => id => {
        let unseenMsg = 0
        const groupData = getters.chatGroup(id)
        if (groupData) {
            groupData.messages.map((msg) => {
                if (!msg.isSeen && !msg.isSent) unseenMsg++
            })
        }
        return unseenMsg
    },
}
