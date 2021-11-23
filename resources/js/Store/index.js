import { createStore, createLogger } from 'vuex';

export const store = createStore({
    state: {
        onlineFriends: [],
        onlineFriendsId: [],
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
        },
        onlineFriendsId(state, user){
            let users =  state.onlineFriends
            let newUser = [];
            for (let i=0; i < users.length; i++){
                newUser[i.id] = true
            }
            this.state.onlineFriendsId = newUser
        }
    },
    modules: {

    },
    actions: {
        onlineFriendsAsync({ commit, state }, users){
           this.state.onlineFriends = users

            let newUser = [];
            for (let i=0; i < users.length; i++){
                newUser[users[i].id] = true
            }
            state.onlineFriendsId = newUser
        },
        addOnlineFriends({ commit, state }, user){
            state.onlineFriends.pushIfNotExist(user, function (e){
                return e.id === user.id
            })
            if ( state.onlineFriendsId[user.id] === undefined){
                state.onlineFriendsId[user.id] = true
            }


        },
        removeOfflineFriend({ commit, state }, user){
            state.onlineFriends = state.onlineFriends.filter(online => {
                return online.id != user.id;
            })
            if ( state.onlineFriendsId[user.id] !== undefined){
                state.onlineFriendsId.slice(user.id, 1)
            }
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
