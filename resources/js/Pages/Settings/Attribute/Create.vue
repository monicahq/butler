<style lang="scss" scoped>
</style>

<template>
  <layout title="Settings">
    <!-- main content -->
    <section class="w-100 pa3 ph5-ns">
      <div class="db cf mw8 center">
        <!-- left column -->
        <div class="fl w-50-l w-100 pr2-l pr0 mb3 mb0-l">
          <h2>Add a new attribute</h2>

          <!-- Form Errors -->
          <errors :errors="$page.errors" />

          <form @submit.prevent="submit">
            <text-input v-model="form.name"
                        :name="'name'"
                        :errors="$page.errors.name"
                        :label="$t('auth.register_firstname')"
                        :required="true"
            />

            <p>Type of data</p>
            <div class="db relative">
              <input id="administrator" v-model="form.type" type="radio" class="mr1 relative" name="permission_level"
                     value="text"
              />
              <label for="administrator" class="pointer">
                Text field
              </label>
              <p class="ma0 lh-copy f6 mb3">
                {{ $t('account.employee_new_administrator_desc') }}
              </p>
            </div>
            <div class="db relative">
              <input id="administrator" v-model="form.type" type="radio" class="mr1 relative" name="permission_level"
                     value="date"
              />
              <label for="administrator" class="pointer">
                Date
              </label>
              <p class="ma0 lh-copy f6 mb3">
                {{ $t('account.employee_new_administrator_desc') }}
              </p>
            </div>
            <div class="db relative">
              <input id="administrator" v-model="form.type" type="radio" class="mr1 relative" name="permission_level"
                     value="dropdown"
              />
              <label for="administrator" class="pointer">
                List of choices
              </label>
              <p class="ma0 lh-copy f6 mb3">
                {{ $t('account.employee_new_administrator_desc') }}
              </p>
            </div>

            <p>Options</p>
          </form>
        </div>

        <!-- right column -->
        <div class="fl w-80-l w-100 pl2-l pl0">
          sdfasdfa
        </div>
      </div>
    </section>
  </layout>
</template>

<script>

import Layout from '@/Shared/Layout';
import TextInput from '@/Shared/TextInput';
import Errors from '@/Shared/Errors';

export default {
  components: {
    Layout,
    TextInput,
    Errors,
  },

  data() {
    return {
      form: {
        name: null,
        type: null,
        errors: [],
      },
      loadingState: '',
      errorTemplate: Error,
    };
  },
};
</script>
