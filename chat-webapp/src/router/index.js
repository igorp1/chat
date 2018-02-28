import Vue from 'vue'
import Router from 'vue-router'
import Home from '@/components/Home'
import User from '@/components/User'
import Chat from '@/components/Chat'
import ChatSettings from '@/components/ChatSettings'

Vue.use(Router)

export default new Router({
  mode: 'history',
  routes: [
    {
      path: '/',
      name: 'Home',
      component: Home
    },
    {
      path: '/user',
      name: 'User',
      component: User
    },
    {
      path: '/chat/:token',
      name: 'Chat',
      component: Chat
    },
    {
      path: '/chat/:token/settings',
      name: 'ChatSettings',
      component: ChatSettings
    }
  ]
})
