<style lang="scss" scoped>
</style>

<template>
  <layout title="Settings">
    <!-- main content -->
    <section class="w-100 pa3 ph5-ns">
      <div class="db cf mw8 center">
        <!-- left column -->
        <div class="fl w-20-l w-100 pr2-l pr0 mb3 mb0-l">
          <ul>
            <li>Account</li>
            <li>Contact templates</li>
          </ul>
        </div>

        <!-- right column -->
        <div class="fl w-80-l w-100 pl2-l pl0">
          <!-- templates -->
          <div class="ba mb3 bg-white br2 box bc-gray">
            <ul>
              <li v-for="template in templates" :key="template.id">{{ template.name }} ({{ template.number_of_information }} pieces of information)</li>
            </ul>
          </div>

          <!-- informations -->
          <div class="ba mb3 bg-white br2 box bc-gray">
            <!-- looping over information -->
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

            <p><a href="" class="btn">Edit</a></p>
          </div>
        </div>
      </div>
    </section>
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
