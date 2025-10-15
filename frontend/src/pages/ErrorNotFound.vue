<template>
  <div class="fullscreen bg-blue text-white text-center q-pa-md flex flex-center">
    <div>
      <div style="font-size: 30vh">
        404 / 401
      </div>

      <div class="text-h2" style="opacity:.4">
        Oops. Nothing here...
      </div>
      <div class="text-h2" style="opacity:.4">
        Maybe you're not authorized to view this page or the page does not exist.
      </div>

      <q-btn
        class="q-mt-xl"
        color="white"
        text-color="blue"
        unelevated
        to="/"
        label="Go Home"
        no-caps
      />
    </div>
  </div>
</template>

<script setup>
defineOptions({
  name: 'ErrorNotFound'
})
</script>
