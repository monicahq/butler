<style scoped>
.home-box {
  color: #4d4d4f;
  height: 300px;
  width: 300px;
}

@media (max-width: 480px) {
  .home-box {
    width: 100%;
  }
}

.home-company {
  left: -20px;
  bottom: -20px;
}

@media (max-width: 480px) {
  .home-company img {
    bottom: 0;
  }
}

.home-join {
  left: 14px;
  bottom: 11px;
}

@media (max-width: 480px) {
  .home-join img {
    bottom: 0;
  }
}
</style>

<template>
  <layout title="Home" :no-menu="true" :notifications="notifications">
    <div class="ph2 ph0-ns">
      <p>hi</p>
    </div>
  </layout>
</template>

<script>
import Layout from '@/Shared/Layout';

export default {
  components: {
    Layout,
  },

  props: {
    employees: {
      type: Array,
      default: null,
    },
    notifications: {
      type: Array,
      default: null,
    },
  },
};
</script>
