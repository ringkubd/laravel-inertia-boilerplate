import { createStore } from 'vuex';

export const store = createStore({
    state: {
        onlineFriends: [],
        offlineFriends: []
    },
    mutations: {
        onlineFriends(state, user){
            this.state.onlineFriends.push(user)
        }
    },
    modules: {

    },
    actions: {
        onlineFriendsAsync({ commit, state }, users){
           this.state.onlineFriends = users
        },
        addOnlineFriends({ commit, state }, user){
            state.onlineFriends.push(user)
        },
        removeOfflineFriend({ commit, state }, user){
            state.onlineFriends = state.onlineFriends.filter(online => {
                return online.id != user.id;
            })
        }
    }
})
