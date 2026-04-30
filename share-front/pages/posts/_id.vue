<template>
  <div class="layout">
    <!-- サイドバー -->
    <aside class="sidebar">
      <div class="logo">SHARE</div>

      <nuxt-link to="/" class="nav-item">
        🏠 ホーム
      </nuxt-link>

      <button class="logout-btn" @click="logout">
        🚪 ログアウト
      </button>

      <div class="share-box">
        <div class="share-title">シェア</div>

        <textarea placeholder="いまどうしてる？"></textarea>

        <button>シェアする</button>
      </div>
    </aside>

    <!-- メイン -->
    <main class="main">
      <div class="page-title">コメント</div>

      
      <div class="tweet parent-post">

        
        <div class="tweet-top">
          <strong>{{ post.user }}</strong>

          <span class="like">
            ♥ {{ post.likes_count || 0 }}
          </span>

          <span
            class="delete"
            @click="deletePost"
          >
            ✖
          </span>
        </div>

        <div class="text">
          {{ post.content }}
        </div>
      </div>

      
      <div class="comment-header">
        <span>コメント</span>
      </div>

      
      <div
        v-for="comment in post.comments"
        :key="comment.id"
        class="comment-item"
      >
        <strong>{{ comment.user }}</strong>
        <div class="comment-text">
          {{ comment.content }}
        </div>
      </div>

      
      <div class="comment-form">
        <input
          v-model="commentText"
          type="text"
          placeholder="コメントを入力してください"
        />

        <button @click="submitComment">
          コメント
        </button>
      </div>
    </main>
  </div>
</template>

<script>
export default {
  head() {
    return {
      link: [
        {
          rel: 'stylesheet',
          href: '/css/post-detail.css'
        }
      ]
    }
  },

  data() {
    return {
      post: {},
      commentText: '',
      currentUser: null
    }
  },

  async mounted() {
    const user = localStorage.getItem('user')
    if (user) {
      this.currentUser = JSON.parse(user)
    }

    await this.fetchPost()
  },

  methods: {
    async fetchPost() {
      const id = this.$route.params.id

      const res = await fetch(`http://127.0.0.1:8000/api/posts/${id}`)
      const data = await res.json()

      this.post = {
        ...data,
        user: data.user?.name || '不明',
        likes_count: data.likes_count || 0,
        comments: data.comments
          ? data.comments.map(c => ({
              ...c,
              user: c.user?.name || '不明'
            }))
          : []
      }
    },

    async submitComment() {
      if (!this.commentText.trim()) return

      await fetch('http://127.0.0.1:8000/api/comments', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          post_id: this.post.id,
          user_id: this.currentUser.id,
          content: this.commentText
        })
      })

      this.commentText = ''
      this.fetchPost()
    },

    async deletePost() {
      await fetch(`http://127.0.0.1:8000/api/posts/${this.post.id}`, {
        method: 'DELETE',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          user_id: this.currentUser.id
        })
      })

      this.$router.push('/')
    },

    logout() {
      localStorage.removeItem('isLoggedIn')
      localStorage.removeItem('user')
      this.$router.push('/login')
    }
  }
}
</script>