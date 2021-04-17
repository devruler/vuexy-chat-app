/*=========================================================================================
  File Name: moduleChatActions.js
  Description: Chat Module Actions
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

import axios from '@/axios.js'

export default {
    setChatSearchQuery({
        commit
    }, query) {
        commit('SET_CHAT_SEARCH_QUERY', query)
    },
    // updateAboutChat({
    //     commit,
    //     rootState
    // }, value) {
    //     commit('UPDATE_ABOUT_CHAT', {
    //         rootState,
    //         value
    //     })
    // },
    // updateStatusChat({
    //     commit,
    //     rootState
    // }, value) {
    //     commit('UPDATE_STATUS_CHAT', {
    //         rootState,
    //         value
    //     })
    // },

    // API CALLS
    sendChatMessage({
        getters,
        commit,
        dispatch
    }, payload) {
        return new Promise((resolve, reject) => {
            axios.post('/api/apps/chat/msg', payload)
                .then((response) => {

                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    sendGroupChatMessage({
        getters,
        commit,
        dispatch
    }, payload) {
        return new Promise((resolve, reject) => {
            axios.post('/api/apps/chat/groups/msg', payload)
                .then((response) => {
                    console.log(response)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    // Get contacts from server. Also change in store
    fetchContacts({
        commit
    }) {
        return new Promise((resolve, reject) => {
            axios.get('/api/apps/chat/contacts', {
                    params: {
                        q: ''
                    }
                })
                .then((response) => {
                    commit('UPDATE_CONTACTS', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    // Get chat-contacts from server. Also change in store
    fetchChatContacts({
        commit
    }) {
        return new Promise((resolve, reject) => {
            axios.get('/api/apps/chat/chat-contacts', {
                    params: {
                        q: ''
                    }
                })
                .then((response) => {
                    commit('UPDATE_CHAT_CONTACTS', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    // Get chats from server. Also change in store
    fetchChats({
        commit
    }) {
        return new Promise((resolve, reject) => {
            axios.get('/api/apps/chat/chats')
                .then((response) => {
                    commit('UPDATE_CHATS', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    // Get group chats from server. Also change in store
    fetchGroups({
        commit
    }) {
        return new Promise((resolve, reject) => {
            axios.get('/api/apps/chat/groups')
                .then((response) => {
                    commit('UPDATE_GROUPS', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    // Get group chat messages from server. Also change in store
    fetchGroupMessages({
        commit
    }, payload) {
        return new Promise((resolve, reject) => {
            axios.get('/api/apps/chat/groups/' + payload)
                .then((response) => {
                    commit('UPDATE_GROUP_MESSAGES', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    // Get chats from server. Also change in store
    storeGroup({
        commit
    }, payload) {

        return new Promise((resolve, reject) => {
            axios.post('/api/apps/chat/groups', payload)
                .then((response) => {
                    commit('UPDATE_GROUPS', response.data)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    markSeenAllMessages({
        getters,
        commit
    }, id) {

        return new Promise((resolve, reject) => {
            axios.post('/api/apps/chat/mark-all-seen', {
                    id
                })
                .then((response) => {
                    commit('MARK_SEEN_ALL_MESSAGES', {
                        id,
                        chatData: getters.chatDataOfUser(id)
                    })
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    toggleIsPinned({
        commit
    }, payload) {
        return new Promise((resolve, reject) => {
            axios.post('/api/apps/chat/set-pinned', {
                    contactId: payload.id,
                    value: payload.value
                })
                .then((response) => {
                    commit('TOGGLE_IS_PINNED', payload)
                    resolve(response)
                })
                .catch((error) => {
                    reject(error)
                })
        })
    },

    updateUserStatus({
        commit
    }, payload){
        return new Promise((resolve, reject) => {
            axios.put('/api/users/current-user', payload)
            .then((response) => {
                commit('UPDATE_USER_INFO', payload, { root: true })
                console.log(response)
                resolve(response)
            })
            .catch((error) => {
                reject(error)
            })
          })
    },

    addNewChatMessage({state, commit, rootState}, payload){
        let msg = {
            textContent: payload.content,
            isSeen: payload.is_seen,
            isSent: rootState.AppActiveUser.id === payload.user_id ? payload.is_sent : !payload.is_sent,
            time: payload.created_at,
            attachment: payload.attachment,
        }
        const chatUser = payload.chat.sender_id === rootState.AppActiveUser.id ? payload.chat.recipient_id : payload.chat.sender_id
        commit('ADD_NEW_CHAT_MESSAGE', {msg, chatUser})
    },

    addNewGroupChatMessage({state, commit, rootState}, payload){
        commit('ADD_NEW_GROUP_CHAT_MESSAGE', payload)
    }
}
