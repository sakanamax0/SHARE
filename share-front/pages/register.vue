<template>
  <div class="register-page">
    <div class="register-card">
      <h2>新規登録</h2>

      <form @submit.prevent="register">
        <input
          type="text"
          placeholder="ユーザーネーム"
          v-model="name"
        />

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
          新規登録
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
      name: '',
      email: '',
      password: ''
    }
  },

  head () {
    return {
      link: [
        {
          rel: 'stylesheet',
          href: '/css/register.css'
        }
      ]
    }
  },

  methods: {
  async register () {
    try {
      const response = await fetch('http://127.0.0.1:8000/api/register', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json'
        },
        body: JSON.stringify({
          name: this.name,
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

        alert(data.message || '新規登録に失敗しました')
        return
      }

      localStorage.setItem('isLoggedIn', 'true')
      localStorage.setItem('user', JSON.stringify(data.user))

      this.$router.push('/')
    } catch (error) {
      console.error('register error:', error)
      alert('サーバーに接続できませんでした')
    }
  }
}
}
</script>