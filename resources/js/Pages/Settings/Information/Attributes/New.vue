<style lang="scss" scoped>
</style>

<template>
  <div class="border p-3 rounded bg-orange-100 border-orange-300">
    <h3 class="mb-3 text-lg font-medium leading-tight text-gray-900">
      What kind of attributes would you like to add?
    </h3>

    <!-- Text field -->
    <div class="mb-3">
      <div class="flex items-center">
        <input id="text" v-model="form.type" type="radio" name="new_attribute_type" class="cursor-pointer form-checkbox border-gray-400 h-4 w-4 text-indigo-600"
               value="text"
        />
        <label for="text" class="cursor-pointer ml-3 font-medium text-gray-700">
          Text field
        </label>
      </div>
      <p class="mt-1 ml-4 pl-3 block text-sm text-gray-600">
        A one line field, limited to 255 characters.
      </p>
    </div>

    <!-- Date -->
    <div class="mb-3">
      <div class="flex items-center">
        <input id="date" v-model="form.type" type="radio" name="new_attribute_type" class="cursor-pointer form-checkbox border-gray-400 h-4 w-4 text-indigo-600"
               value="date"
        />
        <label for="date" class="cursor-pointer ml-3 font-medium text-gray-700">
          Date
        </label>
      </div>
      <p class="mt-1 ml-4 pl-3 block text-sm text-gray-600">
        Useful if you need to keep track of a specific date for this information.
      </p>
    </div>

    <!-- Textarea -->
    <div class="mb-3">
      <div class="flex items-center">
        <input id="textarea" v-model="form.type" type="radio" name="new_attribute_type" class="cursor-pointer form-checkbox border-gray-400 h-4 w-4 text-indigo-600"
               value="textarea"
        />
        <label for="textarea" class="cursor-pointer ml-3 font-medium text-gray-700">
          Paragraph
        </label>
      </div>
      <p class="mt-1 ml-4 pl-3 block text-sm text-gray-600">
        One or multiple lines of text.
      </p>
    </div>

    <!-- Dropdown -->
    <div class="mb-3">
      <div class="flex items-center">
        <input id="dropdown" v-model="form.type" type="radio" name="new_attribute_type" class="cursor-pointer form-checkbox border-gray-400 h-4 w-4 text-indigo-600"
               value="dropdown"
        />
        <label for="dropdown" class="cursor-pointer ml-3 font-medium text-gray-700">
          List of choices
        </label>
      </div>
      <p class="mt-1 ml-4 pl-3 block text-sm text-gray-600">
        Useful if you need to let the user chooses between several choices.
      </p>
    </div>

    <span class="hidden sm:block shadow-sm rounded-md mr-2" @click.prevent="$emit('value-selected', form.type)">
      <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out">
        Add
      </button>
    </span>
    <span class="hidden sm:block shadow-sm rounded-md" @click.prevent="$emit('cancel')">
      <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out">
        Cancel
      </button>
    </span>
  </div>
</template>

<script>

export default {
  data() {
    return {
      form: {
        type: 'text',
      },
    };
  },
};
</script>
