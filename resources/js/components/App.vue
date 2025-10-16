<template>
  <HeaderComponent />
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8 text-center">
        <h1 class="display-4 mb-4 text-primary">Bienvenido a Terkkos Billiards Club</h1>
        <p class="lead mb-5">¡Tu proyecto Laravel + Vue está listo para comenzar!<br>Empieza a crear tu sistema moderno desde aquí.</p>
      </div>
    </div>
  </div>
  <FooterComponent />
</template>

<script setup>
import HeaderComponent from './HeaderComponent.vue';
import FooterComponent from './FooterComponent.vue';
</script>

<style scoped>
h1 {
  color: #764ba2;
}
</style>
