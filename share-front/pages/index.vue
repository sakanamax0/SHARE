<template>
  <div class="home-page">
    <div class="layout">
      <!-- 左 -->
      <aside class="sidebar">
        <div class="logo">SHARE</div>

        <nuxt-link to="/" class="nav-item active">
          🏠 ホーム
        </nuxt-link>

        <button class="logout-btn" @click="logout">
          ログアウト
        </button>

        <div class="share-box">
          <div class="share-title">シェア</div>

          <textarea
            v-model="newPost"
            placeholder="いまどうしてる？"
          ></textarea>

          <button @click="addPost">
            シェアする
          </button>
        </div>
      </aside>

      <!-- メイン -->
      <main class="main">
        <div class="page-title">ホーム</div>

        <Tweet
          v-for="post in posts"
          :key="post.id"
          :post="post"
          :current-user="currentUser"
          @request-delete="openDeleteModal"
          @add-comment="addComment"
          @toggle-like="toggleLike"
        />

        <div v-if="posts.length === 0" class="empty-state">
          まだ投稿はありません
        </div>
      </main>
    </div>

    <!-- 削除確認モーダル -->
    <div v-if="showModal" class="modal-overlay">
      <div class="modal">
        <p>この投稿を削除しますか？</p>

        <div class="modal-buttons">
          <button class="cancel" @click="closeModal">
            キャンセル
          </button>

          <button class="danger" @click="confirmDelete">
            削除する
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import Tweet from '@/components/Tweet.vue'

export default {
  middleware: 'auth',

  components: {
    Tweet
  },

  data() {
    return {
      newPost: '',
      posts: [],
      showModal: false,
      targetPost: null,
      currentUser: null
    }
  },

  async mounted() {
    const user = localStorage.getItem('user')

    if (user) {
      this.currentUser = JSON.parse(user)
    }

    await this.fetchPosts()
  },

  methods: {
    async fetchPosts() {
      try {
        const response = await fetch('http://127.0.0.1:8000/api/posts')
        const data = await response.json()

        this.posts = data.map(post => ({
          ...post,
          user: post.user ? post.user.name : '不明',
          likes_count: post.likes_count || 0,
          comments: post.comments
            ? post.comments.map(comment => ({
                ...comment,
                user: comment.user ? comment.user.name : '不明'
              }))
            : []
        }))
      } catch (error) {
        console.error(error)
        alert('投稿一覧の取得に失敗しました')
      }
    },

    async addPost() {
      if (!this.newPost.trim()) return
      if (!this.currentUser) return

      try {
        const response = await fetch('http://127.0.0.1:8000/api/posts', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            user_id: this.currentUser.id,
            content: this.newPost
          })
        })

        const data = await response.json()

        if (!response.ok) {
          alert(data.message || '投稿に失敗しました')
          return
        }

        this.posts.unshift({
          ...data.post,
          user: data.post.user ? data.post.user.name : '不明',
          likes_count: data.post.likes_count || 0,
          comments: data.post.comments
            ? data.post.comments.map(comment => ({
                ...comment,
                user: comment.user ? comment.user.name : '不明'
              }))
            : []
        })

        this.newPost = ''
      } catch (error) {
        console.error(error)
        alert('サーバーに接続できませんでした')
      }
    },

    async addComment({ postId, content }) {
      if (!content.trim()) return
      if (!this.currentUser) return

      try {
        const response = await fetch('http://127.0.0.1:8000/api/comments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            post_id: postId,
            user_id: this.currentUser.id,
            content: content
          })
        })

        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'コメント投稿に失敗しました')
          return
        }

        const targetPost = this.posts.find(post => post.id === postId)

        if (targetPost) {
          if (!targetPost.comments) {
            targetPost.comments = []
          }

          targetPost.comments.push({
            ...data.comment,
            user: data.comment.user ? data.comment.user.name : '不明'
          })
        }
      } catch (error) {
        console.error(error)
        alert('サーバーに接続できませんでした')
      }
    },

    async toggleLike(postId) {
      if (!this.currentUser) return

      try {
        const response = await fetch('http://127.0.0.1:8000/api/likes/toggle', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            post_id: postId,
            user_id: this.currentUser.id
          })
        })

        const data = await response.json()

        if (!response.ok) {
          alert(data.message || 'いいね処理に失敗しました')
          return
        }

        const targetPost = this.posts.find(post => post.id === postId)

        if (targetPost) {
          targetPost.likes_count = data.likes_count
        }
      } catch (error) {
        console.error(error)
        alert('サーバーに接続できませんでした')
      }
    },

    openDeleteModal(post) {
      this.targetPost = post
      this.showModal = true
    },

    closeModal() {
      this.showModal = false
      this.targetPost = null
    },

    async confirmDelete() {
      if (!this.targetPost || !this.currentUser) return

      try {
        const response = await fetch(`http://127.0.0.1:8000/api/posts/${this.targetPost.id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            user_id: this.currentUser.id
          })
        })

        const data = await response.json()

        if (!response.ok) {
          alert(data.message || '投稿削除に失敗しました')
          return
        }

        this.posts = this.posts.filter(
          post => post.id !== this.targetPost.id
        )

        this.closeModal()
      } catch (error) {
        console.error(error)
        alert('サーバーに接続できませんでした')
      }
    },

    logout() {
      localStorage.removeItem('isLoggedIn')
      localStorage.removeItem('user')
      this.$router.push('/login')
    }
  }
}
</script>