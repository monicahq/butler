<template>
  <div class="px-2">
    <div class="flex items-center justify-center">
      <div class="max-w-md w-full">
        {{ $t('auth.register_title') }}

        <!-- Form Errors -->
        <errors :errors="$page.errors" />

        <form @submit.prevent="submit">
          <text-input v-model="form.firstname"
                      :name="'firstname'"
                      :errors="$page.errors.firstname"
                      :label="$t('auth.register_firstname')"
                      :required="true"
          />

          <text-input v-model="form.lastname"
                      :name="'lastname'"
                      :errors="$page.errors.lastname"
                      :label="$t('auth.register_lastname')"
                      :required="true"
          />

          <text-input v-model="form.email"
                      :name="'email'"
                      :errors="$page.errors.email"
                      :label="$t('auth.register_email')"
                      :help="$t('auth.register_email_help')"
                      :required="true"
                      :type="'email'"
          />

          <text-input v-model="form.password"
                      :name="'password'"
                      :errors="$page.errors.password"
                      :label="$t('auth.register_password')"
                      :help="$t('auth.register_password_help')"
                      :required="true"
                      :type="'password'"
          />
          <!-- Actions -->
          <div class="flex-ns justify-between">
            <loading-button :classes="'btn add w-auto-ns w-full mb2 pv2 ph3'" :state="loadingState" :text="$t('auth.register_cta')" />
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import TextInput from '@/Shared/TextInput';
import Errors from '@/Shared/Errors';
import LoadingButton from '@/Shared/LoadingButton';

export default {
  components: {
    TextInput,
    Errors,
    LoadingButton,
  },

  data() {
    return {
      form: {
        firstname: null,
        lastname: null,
        email: null,
        password: null,
        errors: [],
      },
      loadingState: '',
      errorTemplate: Error,
    };
  },

  methods: {
    submit() {
      this.loadingState = 'loading';

      this.$inertia.post(this.route('signup.attempt'), this.form)
        .then(() =>
          this.loadingState = null
        );
    },
  }
};
</script>
