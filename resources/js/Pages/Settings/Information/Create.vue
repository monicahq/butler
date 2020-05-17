<style lang="scss" scoped>
</style>

<template>
  <layout title="Settings">
    <!-- main content -->
    <section class="flex max-w-6xl mx-auto py-6 sm:px-6 lg:px-8">

      <!-- left column -->
      <div class="w-3/4 mr-3 rounded overflow-hidden shadow bg-white">
        <h2 class="text-lg leading-6 font-medium text-gray-900 p-3 border-b border-gray-300 text-center mb-3">Add a new information</h2>

        <form @submit.prevent="submit">
          <div class="flex border-b border-gray-300">
            <p class="w-1/3 leading-6 font-medium text-gray-900 p-3">Name</p>

            <div class="p-3 w-2/3">
              <text-input v-model="form.name"
                          :name="'name'"
                          :errors="$page.errors.name"
                          :label="'How should the information be named?'"
                          :required="true"
              />
            </div>
          </div>

          <!-- attributes -->
          <div class="flex border-b border-gray-300">
            <p class="w-1/3 leading-6 font-medium text-gray-900 p-3">Attributes</p>

            <div class="p-3 w-2/3">
              <!-- List of attributes -->
              <div v-for="attribute in form.attributes" :key="attribute.realId" class="border border-solid border-gray-400 mb-2 relative p-2">
                <p>Attribute #{{ attribute.id }} <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs font-semibold text-gray-700 mr-2">{{ attribute.type }}</span></p>
                <a href="#" @click.prevent="removeAttribute(attribute)" class="absolute right-0 top-0">Delete</a>

                <!-- Text attribute -->
                <template v-if="attribute.type == 'text'">
                  <type-text v-on:update-label="updateAttributeLabel(attribute, $event)" />
                </template>

                <!-- Textarea attribute -->
                <template v-if="attribute.type == 'textarea'">
                  <type-textarea v-on:update-label="updateAttributeLabel(attribute, $event)" />
                </template>

                <!-- Dropdown attribute -->
                <template v-if="attribute.type == 'dropdown'">
                  <type-dropdown v-on:update-label="updateAttributeLabel(attribute, $event)" />
                </template>

                <!-- Date attribute -->
                <template v-if="attribute.type == 'date'">
                  <type-date v-on:update-label="updateAttributeLabel(attribute, $event)" />
                </template>
              </div>

              <!-- CTA Add a new attribute -->
              <a href="#" class="border-dotted border-b border-gray-400 cursor-pointer text-sm" @click.prevent="showNewForm = true" v-if="!showNewForm">+ Add a new attribute</a>

              <!-- Add a new attribute window -->
              <new
                v-if="showNewForm"
                v-on:cancel="showNewForm = false"
                v-on:value-selected="addAttribute($event)"
              />
            </div>
          </div>

          <!-- options -->
          <div class="flex">
            <p class="w-1/3 leading-6 font-medium text-gray-900 p-3">Options</p>

            <div class="p-3 w-2/3">
              <div class="flex items-center">
                <input v-model="form.type" id="dropdown" type="checkbox" name="new_attribute_type" class="form-checkbox border-gray-400 h-4 w-4 text-indigo-600" value="dropdown" />
                <label for="dropdown" class="ml-3 font-medium text-gray-700">Allow multiple entries</label>
              </div>
              <p class="mt-1 ml-4 pl-3 block text-sm text-gray-600">
                Useful if you need to let the user chooses between several choices.
              </p>
            </div>
          </div>
        </form>

        <!-- main actions -->
        <div class="border-solid border-t border-gray-300 flex p-2">
          <span class="hidden sm:block shadow-sm rounded-md mr-2">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out">
              Edit
            </button>
          </span>
          <span class="hidden sm:block shadow-sm rounded-md">
            <button type="button" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm leading-5 font-medium rounded-md text-gray-700 bg-white hover:text-gray-500 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 active:text-gray-800 active:bg-gray-50 transition duration-150 ease-in-out">
              Cancel
            </button>
          </span>
        </div>
      </div>

      <!-- right column: preview -->
      <div class="w-1/4 rounded overflow-hidden shadow bg-white p-3">
        <p class="font-semibold mb-3 text-center">{{ form.name }}</p>

        <div :key="refreshPreviewKey">
          <div v-for="attribute in form.attributes" :key="attribute.realId">
            <template v-if="attribute.type == 'text'">
              <label class="block font-normal text-sm mb-2">
                {{ attribute.label }}
              </label>
              <input type="text" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:leading-5 relative rounded-md shadow-sm" />
            </template>

            <template v-if="attribute.type == 'textarea'">
              <label class="block font-normal text-sm mb-2">
                {{ attribute.label }}
              </label>
              <textarea class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:leading-5 relative rounded-md shadow-sm"></textarea>
            </template>

            <template v-if="attribute.type == 'date'">
              <label class="block font-normal text-sm mb-2">
                {{ attribute.label }}
              </label>
              <input type="date" class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-md focus:outline-none focus:shadow-outline-blue focus:border-blue-300 focus:z-10 sm:leading-5 relative rounded-md shadow-sm" />
            </template>
          </div>
        </div>
      </div>
    </section>
  </layout>
</template>

<script>

import Layout from '@/Shared/Layout';
import TextInput from '@/Shared/TextInput';
import TextArea from '@/Shared/TextArea';
import Errors from '@/Shared/Errors';
import New from '@/Pages/Settings/Information/Attributes/New';
import TypeText from '@/Pages/Settings/Information/Attributes/TypeText';
import TypeTextarea from '@/Pages/Settings/Information/Attributes/TypeTextarea';
import TypeDropdown from '@/Pages/Settings/Information/Attributes/TypeDropdown';
import TypeDate from '@/Pages/Settings/Information/Attributes/TypeDate';

export default {
  components: {
    Layout,
    TextInput,
    TextArea,
    TypeText,
    TypeTextarea,
    TypeDropdown,
    TypeDate,
    New,
  },

  data() {
    return {
      refreshPreviewKey: 0,
      relativeId: 0,
      realId: 0, // real id doesn't get updated when array is reordered. this is used to uniquely identify the item in the array.
      form: {
        name: null,
        attributes: [],
        errors: [],
      },
      showNewForm: false,
      loadingState: '',
      errorTemplate: Error,
    };
  },

  methods: {
    /** Add a new attribute */
    addAttribute(object) {
      this.showNewForm = false;
      this.relativeId = this.relativeId + 1;
      this.realId = this.realId + 1;

      this.form.attributes.push({
        id: this.relativeId,
        realId: this.realId,
        type: object,
      });
    },

    /** Remove an attribute in the list of attributes */
    removeAttribute(object) {
      this.form.attributes.splice(this.form.attributes.findIndex(i => i.realId === object.realId), 1);
      this.updateAttributeOrder();
    },

    /** Change ids of all the attributes once the list of attributes changed */
    updateAttributeOrder() {
      var id = 1;
      this.form.attributes.forEach(function (item, index) {
        item.id = id;
        id++;
      });

      this.relativeId--;
    },

    /** Update the label of the attribute */
    updateAttributeLabel(attribute, text) {
      var index = this.form.attributes.findIndex(i => i.realId === attribute.realId);
      this.form.attributes[index].label = text;
      this.refreshPreviewKey += 1;
    }
  }
};
</script>
