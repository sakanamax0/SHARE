<template>
  <div class="login-page">
    <div class="login-card">
      <h2>ログイン</h2>

      <form @submit.prevent="login">
        <input
          type="email"
          placeholder="メールアドレス"
          v-model="email"
        />

        <input
          type="password"
          placeholder="パスワード"
          v-model="password"
        />

        <button type="submit">
          ログイン
        </button>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  layout: 'auth',

  data () {
    return {
      email: '',
      password: ''
    }
  },

  head () {
    return {
      link: [
        {
          rel: 'stylesheet',
          href: '/css/login.css'
        }
      ]
    }
  },

  methods: {
  async login () {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          email: this.email,
          password: this.password
        })
      })

      const data = await response.json()

      if (!response.ok) {
        if (data.errors) {
          const firstError = Object.values(data.errors)[0][0]
          alert(firstError)
          return
        }

        alert(data.message || 'ログインに失敗しました')
        return
      }

      localStorage.setItem('isLoggedIn', 'true')
      localStorage.setItem('user', JSON.stringify(data.user))

      this.$router.push('/')
    } catch (error) {
      console.error('login error:', error)
      alert('サーバーに接続できませんでした')
    }
  }
}
}
</script>