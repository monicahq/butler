<style scoped>
.dummy {
  right: 40px;
  bottom: 20px;
}
</style>

<template>
  <layout title="Home" :notifications="notifications">
    <div class="ph2 ph0-ns">
      <div class="cf mt4 mw7 center">
        <h2 class="tc fw5">
          hi
        </h2>
      </div>
    </div>
  </layout>
</template>

<script>
import Worklogs from '@/Pages/Dashboard/Me/Partials/Worklogs';
import Morale from '@/Pages/Dashboard/Me/Partials/Morale';
import WorkFromHome from '@/Pages/Dashboard/Me/Partials/WorkFromHome';
import Question from '@/Pages/Dashboard/Me/Partials/Question';
import Layout from '@/Shared/Layout';
import DashboardMenu from '@/Pages/Dashboard/Partials/DashboardMenu';

export default {
  components: {
    Layout,
    Worklogs,
    Morale,
    Question,
    WorkFromHome,
    DashboardMenu,
  },

  props: {
    employee: {
      type: Object,
      default: null,
    },
    worklogCount: {
      type: Number,
      default: 0,
    },
    moraleCount: {
      type: Number,
      default: 0,
    },
    notifications: {
      type: Array,
      default: null,
    },
    ownerPermissionLevel: {
      type: Number,
      default: 0,
    },
  },
};
</script>
