<style lang="scss" scoped>
.error-explanation {
  background-color: #fde0de;
  border-color: rgb(226, 171, 167);
}

.error {
  &:focus {
    box-shadow: 0 0 0 1px #fff9f8;
  }
}

.optional-badge {
  border-radius: 4px;
  color: #283e59;
  background-color: #edf2f9;
  padding: 3px 4px;
}

</style>

<template>
  <div :class="extraClassUpperDiv">
    <label v-if="label" class="block font-normal text-sm mb-2" :for="id">
      {{ label }}
      <span v-if="!required" class="optional-badge text-xs">
        {{ $t('app.optional') }}
      </span>
    </label>
    <input :id="id"
           :ref="customRef"
           v-bind="$attrs"
           class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:leading-5 relative rounded-md shadow-sm"
           :required="required"
           :type="type"
           :name="name"
           :autofocus="autofocus"
           :value="value"
           :max="max"
           :min="min"
           :placeholder="placeholder"
           :data-cy="datacy"
           @input="$emit('input', $event.target.value)"
           @keydown.esc="sendEscKey"
    />
    <div v-if="hasError" class="error-explanation pa3 ba br3 mt1">
      {{ errors[0] }}
    </div>
    <p v-if="help" class="f7 mb3 lh-title">
      {{ help }}
    </p>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,

  props: {
    id: {
      type: String,
      default() {
        return `text-input-${this._uid}`;
      },
    },
    type: {
      type: String,
      default: 'text',
    },
    value: {
      type: String,
      default: '',
    },
    customRef: {
      type: String,
      default: 'input',
    },
    name: {
      type: String,
      default: 'input',
    },
    datacy: {
      type: String,
      default: '',
    },
    placeholder: {
      type: String,
      default: '',
    },
    help: {
      type: String,
      default: '',
    },
    label: {
      type: String,
      default: '',
    },
    required: {
      type: Boolean,
      default: false,
    },
    extraClassUpperDiv: {
      type: String,
      default: 'mb3',
    },
    min: {
      type: Number,
      default: 0,
    },
    max: {
      type: Number,
      default: 0,
    },
    errors: {
      type: Array,
      default: () => [],
    },
    autofocus: {
      type: Boolean,
      default: false,
    }
  },

  computed: {
    hasError: function () {
      return this.errors.length > 0 && this.required ? true : false;
    }
  },

  methods: {
    focus() {
      this.$refs.input.focus();
    },

    select() {
      this.$refs.input.select();
    },

    setSelectionRange(start, end) {
      this.$refs.input.setSelectionRange(start, end);
    },

    sendEscKey() {
      this.$emit('esc-key-pressed');
    }
  },
};
</script>
