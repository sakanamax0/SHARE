<template>
  <div class="tweet">

    
    <div class="tweet-top">
      <strong>{{ post.user }}</strong>

      <!-- いいね -->
      <span class="like" @click="toggleLike">
        ♥ {{ post.likes_count || 0 }}
      </span>

      <!-- 削除 -->
      <span
        class="delete"
        @click="$emit('request-delete', post)"
      >
        ✖
      </span>

      <!-- 矢印 -->
      <span class="arrow" @click="goDetail"></span>
    </div>

    <!-- 本文 -->
    <div class="text">
      {{ post.content }}
    </div>

  </div>
</template>

<script>
export default {
  props: {
    post: Object,
    currentUser: Object
  },

  methods: {
    toggleLike() {
      this.$emit('toggle-like', this.post.id)
    },

    goDetail() {
      this.$router.push(`/posts/${this.post.id}`)
    }
  }
}
</script>