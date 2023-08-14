<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
  <RouterView v-slot="{ Component }">
    <Suspense>
      <div>
        <component :is="Component" />
      </div>
    </Suspense>
  </RouterView>
</template>
