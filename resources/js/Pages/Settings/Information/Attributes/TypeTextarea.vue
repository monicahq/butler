<style lang="scss" scoped>
</style>

<template>
  <div>
    <text-input :name="'name'"
                :errors="$page.errors.name"
                :label="'Name of the paragraph'"
                :required="true"
                @input="$emit('update-label', $event)"
    />
  </div>
</template>

<script>
import TextInput from '@/Shared/TextInput';

export default {
  components: {
    TextInput,
  },

  data() {
  },
};
</script>
