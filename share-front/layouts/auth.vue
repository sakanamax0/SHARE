<template>
  <div>
    <AuthHeader />
    <Nuxt />
  </div>
</template>

<script>
import AuthHeader from '@/components/AuthHeader.vue'

export default {
  components: {
    AuthHeader
  }
}
</script>