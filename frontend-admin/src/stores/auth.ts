import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { User } from '@/types/models'

export const useAuthStore = defineStore('auth', () => {
  const user = ref<User | null>(null)
  const isAuthenticated = ref(false)
  const token = ref<string | null>(null)

  function setUser(userData: User) {
    user.value = userData
    isAuthenticated.value = true
  }

  function setToken(tokenValue: string) {
    token.value = tokenValue
  }

  function logout() {
    user.value = null
    isAuthenticated.value = false
    token.value = null
  }

  return {
    user,
    isAuthenticated,
    token,
    setUser,
    setToken,
    logout
  }
})
