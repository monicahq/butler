<style scoped>
input[type=checkbox] {
  top: 5px;
}
input[type=radio] {
  top: -2px;
}
</style>

<template>
  <layout title="Home" :notifications="notifications">
    <div class="ph2 ph0-ns">
      <!-- BREADCRUMB -->
      <div class="mt4-l mt1 mw6 br3 bg-white box center breadcrumb relative z-0 f6 pb2">
        <ul class="list ph0 tc-l tl">
          <li class="di">
            <inertia-link :href="'/' + $page.auth.company.id + '/dashboard'">{{ $t('app.breadcrumb_dashboard') }}</inertia-link>
          </li>
          <li class="di">
            ...
          </li>
          <li class="di">
            <inertia-link :href="'/' + $page.auth.company.id + '/account/hardware'">{{ $t('app.breadcrumb_account_manage_hardware') }}</inertia-link>
          </li>
          <li class="di">
            {{ $t('app.breadcrumb_account_add_hardware') }}
          </li>
        </ul>
      </div>

      <!-- BODY -->
      <div class="mw7 center br3 mb5 bg-white box restricted relative z-1">
        <div class="pa3 mt5">
          <h2 class="normal mb4">
            {{ hardware.name }}
          </h2>

          <p>{{ hardware.serial_number }}</p>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>
import TextInput from '@/Shared/TextInput';
import Checkbox from '@/Shared/Checkbox';
import Errors from '@/Shared/Errors';
import LoadingButton from '@/Shared/LoadingButton';
import Layout from '@/Shared/Layout';
import SelectBox from '@/Shared/Select';
import Help from '@/Shared/Help';

export default {
  components: {
    Checkbox,
    Layout,
    TextInput,
    Errors,
    LoadingButton,
    SelectBox,
    Help
  },

  props: {
    hardware: {
      type: Object,
      default: null,
    },
    notifications: {
      type: Array,
      default: null,
    },
  },

  data() {
    return {
      form: {
        name: null,
        serial: null,
        employee_id: null,
        lend_hardware: false,
        errors: [],
      },
      loadingState: '',
      errorTemplate: Error,
    };
  },

  methods: {
    updateStatus(payload) {
      this.form.lend_hardware = payload;
    },

    submit() {
      this.loadingState = 'loading';
      if (this.form.employee_id) {
        this.form.employee_id = this.form.employee_id.value;
      }

      axios.post('/' + this.$page.auth.company.id + '/account/hardware', this.form)
        .then(response => {
          localStorage.success = this.$t('account.hardware_create_success');
          this.$inertia.visit('/' + response.data.data + '/account/hardware');
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = _.flatten(_.toArray(error.response.data));
        });
    },
  }
};

</script>
