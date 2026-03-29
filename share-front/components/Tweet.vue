<template>
  <div class="tweet">
    <div class="tweet-header">
      <strong>{{ post.user }}</strong>

      <div class="icons">
        <span class="like" @click="toggleLike">
          ♥ {{ post.likes_count || 0 }}
        </span>

        <span
          v-if="currentUser && post.user_id === currentUser.id"
          class="delete"
          @click="$emit('request-delete', post)"
        >
          ✖
        </span>
      </div>
    </div>

    <div class="text">
      {{ post.content }}
    </div>

    <div class="comment-section">
      <div class="comment-title">コメント</div>

      <div
        v-for="comment in post.comments"
        :key="comment.id"
        class="comment-item"
      >
        <strong>{{ comment.user }}</strong>
        <div class="comment-text">{{ comment.content }}</div>
      </div>

      <div class="comment-form">
        <input
          v-model="commentText"
          type="text"
          placeholder="コメントを入力"
        />

        <button @click="submitComment">
          コメント
        </button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    post: {
      type: Object,
      required: true
    },
    currentUser: {
      type: Object,
      default: null
    }
  },

  data() {
    return {
      commentText: ''
    }
  },

  methods: {
    toggleLike() {
      this.$emit('toggle-like', this.post.id)
    },

    submitComment() {
      if (!this.commentText.trim()) return

      this.$emit('add-comment', {
        postId: this.post.id,
        content: this.commentText
      })

      this.commentText = ''
    }
  }
}
</script>