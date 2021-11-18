import { createStore, createLogger } from 'vuex';

export const store = createStore({
    state: {
        onlineFriends: [],
        offlineFriends: [],
        messages: [],
        activeChatTarget: null,
        activeConversation: null,
    },
    plugins: [createLogger()],
    mutations: {
        onlineFriends(state, user){
            this.state.onlineFriends.pushIfNotExist(user, function (e){
                return e.id === user.id
            })
        }
    },
    modules: {

    },
    actions: {
        onlineFriendsAsync({ commit, state }, users){
           this.state.onlineFriends = users
        },
        addOnlineFriends({ commit, state }, user){
            state.onlineFriends.pushIfNotExist(user, function (e){
                return e.id === user.id
            })
        },
        removeOfflineFriend({ commit, state }, user){
            state.onlineFriends = state.onlineFriends.filter(online => {
                return online.id != user.id;
            })
        },
        messagesInit({ commit, state }, messages){
            this.state.messages = messages
        },
        sendMessage({ commit, state }, message){
            this.state.messages.push(message)
        },
        setActiveChatTarget({ commit, state }, activeUserId){
            this.state.activeChatTarget = activeUserId
        },
        setActiveConversation({ commit, state }, conversationId){
            this.state.activeConversation = conversationId
        }
    }
})
