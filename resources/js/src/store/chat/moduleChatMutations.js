/*=========================================================================================
  File Name: moduleChatMutations.js
  Description: Chat Module Mutations
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/


import Vue from 'vue'

export default {
  UPDATE_ABOUT_CHAT (state, obj) {
    obj.rootState.AppActiveUser.about = obj.value
  },
  UPDATE_STATUS_CHAT (state, obj) {
    obj.rootState.AppActiveUser.status = obj.value
  },

  // API AFTER
  SEND_CHAT_MESSAGE (state, payload) {
      console.log(payload)
    if (payload.chatData) {
      // If there's already chat. Push msg to existing chat

      console.log('chat already exits and push msg')
      state.chats[Object.keys(state.chats).find(key => Number(key) === payload.id)].msg.push(payload.msg)
    } else {
      // Create New chat and add msg
      console.log('create new chat and ad msg')
      const chatId = payload.id
      Vue.set(state.chats, [chatId], { isPinned: payload.isPinned,
        msg: [payload.msg] })
    }
  },

  ADD_NEW_GROUP_CHAT_MESSAGE (state, payload) {
      // Push msg to existing group chat
      state.groups.find(group => group.id === payload.group_chat_id).messages.push(payload)
  },

  UPDATE_CONTACTS (state, contacts) {
    state.contacts = contacts
  },

  UPDATE_CONTACT_STATUS (state, contact) {
    state.contacts = state.contacts.map((item) => item.id === contact.id ? contact : item)
    state.chatContacts = state.chatContacts.map((item) => item.id === contact.id ? contact : item)
  },

  UPDATE_CHAT_CONTACTS (state, chatContacts) {
    state.chatContacts = chatContacts
  },
  UPDATE_CHATS (state, chats) {
    state.chats = chats
  },
  UPDATE_GROUPS (state, groups) {
    state.groups = groups
  },

  ADD_NEW_CHAT_MESSAGE (state, payload){
    state.chats[payload.chatUser]['msg'].push(payload.msg)
  },

//   ADD_NEW_GROUP_CHAT_MESSAGE (state, payload){
//     state.groups = state.groups.map(group => group.id === payload.group_chat_id ? group.messages.push(payload) : group)
//   },

  UPDATE_GROUP_MESSAGES (state, updatedGroup) {
    state.groups = state.groups.map((group) => group.id === updatedGroup.id ? updatedGroup : group)
  },

  SET_CHAT_SEARCH_QUERY (state, query) {
    state.chatSearchQuery = query
  },
  MARK_SEEN_ALL_MESSAGES (state, payload) {
    payload.chatData.msg.forEach((msg) => {
      msg.isSeen = true
    })
  },
  TOGGLE_IS_PINNED (state, payload) {
    state.chats[Object.keys(state.chats).find(key => Number(key) === payload.id)].isPinned = payload.value
  }
}

