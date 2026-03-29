<template>
  <header class="header">
    <div class="logo">SHARE</div>

    <nav class="nav">
      <nuxt-link
        to="/register"
        :class="{ active: $route.path === '/register' }"
      >
        新規登録
      </nuxt-link>

      <nuxt-link
        to="/login"
        :class="{ active: $route.path === '/login' }"
      >
        ログイン
      </nuxt-link>
    </nav>
  </header>
</template>

<script>
export default {
  name: 'AuthHeader'
}
</script>
