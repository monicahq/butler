<style lang="scss" scoped>
</style>

<template>
  <layout title="Settings">
    <!-- main content -->
    <div class="max-w-5xl mx-auto py-6 sm:px-6 lg:px-8">
      <div class="flex">
        <!-- left-column -->
        <div class="w-1/4 mr-3">
          <div class="rounded overflow-hidden shadow-lg bg-white p-3">
            <ul>
              <li>Account</li>
              <li>Contact templates</li>
            </ul>
          </div>
        </div>

        <!-- left-column -->
        <div class="w-3/4">
          <div class="rounded overflow-hidden shadow-lg bg-white p-3">
            <ul>
              <li v-for="template in templates" :key="template.id">{{ template.name }} ({{ template.number_of_information }} pieces of information)</li>
            </ul>

            <ul>
              <li v-for="information in informations" :key="information.id">
                {{ information.name }} ({{ information.attributes.length }} attributes)

                <!-- looping over the attributes -->
                <ul v-if="information.attributes" class="db">
                  <li v-for="attribute in information.attributes" :key="attribute.id">
                    {{ attribute.name }}

                    <!-- looping over attribute default values -->
                    <ul v-if="attribute.default_values" class="db">
                      <li v-for="defaultValue in attribute.default_values" :key="defaultValue.id">
                        {{ defaultValue.value }}
                      </li>
                    </ul>
                  </li>
                </ul>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </layout>
</template>

<script>

import Layout from '@/Shared/Layout';

export default {
  components: {
    Layout,
  },

  props: {
    templates: {
      type: Array,
      default: null,
    },
    informations: {
      type: Array,
      default: null,
    },
  },
};
</script>
