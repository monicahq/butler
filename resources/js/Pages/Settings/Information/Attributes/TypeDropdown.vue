<style lang="scss" scoped>
</style>

<template>
  <div>
    <text-input :name="'name'"
                :errors="$page.errors.name"
                :label="'Name of the text field'"
                :required="true"
                @input="$emit('update-label', $event)"
    />

    <ul>
      <li v-for="possibleValue in possibleValue" :key="possibleValue.id">
        {{ possibleValue.name }}
      </li>
      <li>
      </li>
    </ul>
  </div>
</template>

<script>
import TextInput from '@/Shared/TextInput';

export default {
  components: {
    TextInput,
  },

  data() {
    return {
      possibleValues: [],
      form: {
        name: null,
        attributes: [],
        errors: [],
      },
    };
  },
};
</script>
