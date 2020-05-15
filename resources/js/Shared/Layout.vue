<style lang="scss" scoped>
.find-box {
  border: 1px solid rgba(27,31,35,.15);
  box-shadow: 0 3px 12px rgba(27,31,35,.15);
  top: 63px;
  width: 500px;
  left: 0;
  right: 0;
  margin: 0 auto;
}

.bg-modal-find {
  position: fixed;
  z-index: 100;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: rgba(0, 0, 0, 0.3);
  display: flex;
  justify-content: center;
  align-items: center;
}

nav {
  border-bottom: 1px solid #e0e0e0;
  background-color: #fff;

  a {
    color: #4d4d4f;

    &:hover {
      border-bottom-width: 0;
    }

    &.special {
      &:hover {
        border-radius: 11px;
        box-shadow: 1px 0px 1px rgba(43, 45, 80, 0.16), -1px 1px 1px rgba(43, 45, 80, 0.16), 0px 1px 4px rgba(43, 45, 80, 0.18);
      }
    }
  }
}
</style>

<template>
  <div>
    <div class="dn db-m db-l">
      <nav class="flex justify-between bb b--white-10">
        <div class="flex-grow pa2 flex items-center">
          <inertia-link href="/home" class="mr3 no-underline pa2 bb-0">
            <img src="/img/logo.svg" height="30" width="30" alt="logo" />
          </inertia-link>
          <div v-if="!noMenu">
            <inertia-link :href="'/dashboard'" class="mr2 no-underline pa2 bb-0 special">
              🏡 {{ $t('app.header_home') }}
            </inertia-link>
            <inertia-link :href="'/employees'" class="mr2 no-underline pa2 bb-0 special">
              🧑 {{ $t('app.header_employees') }}
            </inertia-link>
            <inertia-link :href="'/teams'" class="mr2 no-underline pa2 bb-0 special" data-cy="header-teams-link">
              👫 {{ $t('app.header_teams') }}
            </inertia-link>
            <inertia-link :href="'/company'" class="mr2 no-underline pa2 bb-0 special" data-cy="header-teams-link">
              ⛺️ {{ $t('app.header_company') }}
            </inertia-link>
            <a data-cy="header-find-link" class="mr2 no-underline pa2 bb-0 special pointer" @click="showFindModal">
              🔍 {{ $t('app.header_find') }}
            </a>
            <inertia-link :href="'/settings'" data-cy="header-adminland-link" class="no-underline pa2 bb-0 special">
              👮‍♂️ Settings
            </inertia-link>
          </div>
        </div>
        <div class="flex-grow pa2 flex items-center">
          <notifications-component :notifications="notifications" />
          <user-menu />
        </div>
      </nav>
    </div>

    <!-- MOBILE MENU -->
    <header class="bg-white mobile dn-ns mb3">
      <div class="ph2 pv2 w-100 relative">
        <div class="pv2 relative menu-toggle">
          <label for="menu-toggle" class="dib b relative">
            Menu
          </label>
          <input id="menu-toggle" type="checkbox" />
          <ul id="mobile-menu" class="list pa0 mt4 mb0">
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                Home
              </a>
            </li>
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                app.main_nav_people
              </a>
            </li>
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                app.main_nav_journal
              </a>
            </li>
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                app.main_nav_find
              </a>
            </li>
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                app.main_nav_changelog
              </a>
            </li>
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                app.main_nav_settings
              </a>
            </li>
            <li class="pv2 bt b--light-gray">
              <a class="no-color b no-underline" href="">
                app.main_nav_signout
              </a>
            </li>
          </ul>
        </div>
        <div class="absolute pa2 header-logo">
          <a href="">
            <img src="/img/logo.svg" width="30" height="27" alt="logo" />
          </a>
        </div>
      </div>
    </header>

    <slot></slot>

    <toaster />
  </div>
</template>

<script>
import vClickOutside from 'v-click-outside';
import UserMenu from '@/Shared/UserMenu';
import LoadingButton from '@/Shared/LoadingButton';
import NotificationsComponent from '@/Shared/Notifications';
import Toaster from '@/Shared/Toaster';

export default {
  components: {
    UserMenu,
    LoadingButton,
    NotificationsComponent,
    Toaster,
  },

  directives: {
    clickOutside: vClickOutside.directive
  },

  props: {
    title: {
      type: String,
      default: '',
    },
    noMenu: {
      type: Boolean,
      default: false,
    },
    notifications: {
      type: Array,
      default: null,
    },
  },

  data() {
    return {
      loadingState: '',
      modalFind: false,
      showModalNotifications: true,
      dataReturnedFromSearch: false,
      form: {
        searchTerm: null,
        errors: {
          type: Array,
          default: null,
        },
      },
      employees: [],
      teams: [],
    };
  },

  watch: {
    title(title) {
      this.updatePageTitle(title);
    }
  },

  mounted() {
    this.updatePageTitle(this.title);
  },


  methods: {
    updatePageTitle(title) {
      document.title = title ? `${title} | Butler` : 'Butler';
    },

    showFindModal() {
      this.dataReturnedFromSearch = false;
      this.form.searchTerm = null;
      this.employees = [];
      this.teams = [];
      this.modalFind = !this.modalFind;

      this.$nextTick(() => {
        this.$refs.search.focus();
      });
    },

    submit() {
      axios.post('/search/employees', this.form)
        .then(response => {
          this.dataReturnedFromSearch = true;
          this.employees = response.data.data;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = _.flatten(_.toArray(error.response.data));
        });

      axios.post('/search/teams', this.form)
        .then(response => {
          this.dataReturnedFromSearch = true;
          this.teams = response.data.data;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = _.flatten(_.toArray(error.response.data));
        });
    },

    toggleHelp() {
      axios.post('/help')
        .then(response => {
          this.$page.auth.user.show_help = response.data.data;
        })
        .catch(error => {
          this.loadingState = null;
          this.form.errors = _.flatten(_.toArray(error.response.data));
        });
    }
  },
};
</script>
