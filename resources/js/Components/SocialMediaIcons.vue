<template>
  <div class="flex flex-col items-center p-6 space-y-6 w-full max-w-lg mx-auto">
    <!-- Button to Show Icons -->
    <button 
      @click="toggleIcons" 
      :disabled="availableIcons.length === 0"
      class="px-5 py-3 bg-green-600 text-white rounded-xl shadow-lg text-lg flex items-center gap-3 transition-all disabled:bg-gray-400 disabled:cursor-not-allowed"
    >
      <span class="text-2xl">+</span> Add Social Media
    </button>

    <!-- Available Icons for Selection -->
    <transition name="fade">
      <div v-if="showIcons" class="grid grid-cols-2 gap-4 w-full max-w-md bg-white p-4 rounded-xl shadow-md">
        <div 
          v-for="icon in availableIcons" 
          :key="icon" 
          @click="addTextField(icon)"
          class="cursor-pointer flex items-center gap-3 px-4 py-3 border border-gray-300 rounded-lg hover:bg-gray-100 transition"
        >
          <font-awesome-icon :icon="icons[icon]" class="w-6 h-6 text-gray-600" />
          <span class="font-medium text-gray-700">{{ capitalize(icon) }}</span>
        </div>
      </div>
    </transition>

    <!-- Selected Social Media Input Fields -->
    <div class="w-full space-y-4">
      <transition-group name="list" tag="div">
        <div v-for="(values, icon) in selectedPlatforms" :key="icon" class="flex items-center gap-3 p-4 border border-gray-300 rounded-lg shadow-sm bg-white">
          <font-awesome-icon :icon="icons[icon]" class="w-7 h-7 text-gray-600" />
          
          <div class="flex-1 space-y-2">
            <!-- Name Input Field -->
            <input 
              type="text" 
              :name="`name[${capitalize(icon)}]`"
              v-model="values.name"
              :placeholder="`Enter ${capitalize(icon)} name`" 
              @input="updateField(icon, 'name', values.name)"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />

            <!-- Conditionally Show Link Field -->
            <input 
              v-show="showLinkField"
              type="text" 
              :name="`link[${capitalize(icon)}]`"
              v-model="values.link"
              :placeholder="`Enter ${capitalize(icon)} link`" 
              @input="updateField(icon, 'link', values.link)"
              class="w-full p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
          </div>

          <!-- Remove Icon -->
          <button @click="removeTextField(icon)" class="p-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition">
            <font-awesome-icon :icon="faTrash" class="w-5 h-5" />
          </button>
        </div>
      </transition-group>
    </div>
  </div>
</template>

<script setup>
import { ref, defineProps, defineEmits, onMounted } from "vue";
import { FontAwesomeIcon } from "@fortawesome/vue-fontawesome";
import { faTiktok, faFacebookF, faInstagram, faXTwitter } from "@fortawesome/free-brands-svg-icons";
import { faTrash } from "@fortawesome/free-solid-svg-icons";

// Props
const props = defineProps({
  showLinkField: {
    type: Boolean,
    default: true
  },
  existingData: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(["update:selected"]);

// Icons
const icons = {
  tiktok: faTiktok,
  facebook: faFacebookF,
  instagram: faInstagram,
  x: faXTwitter
};

// Available icons (platforms not yet selected)
const availableIcons = ref(["tiktok", "facebook", "instagram", "x"]);

// Selected platforms (pre-filled or added by the user)
const selectedPlatforms = ref({});

// Show/hide available icons
const showIcons = ref(false);

// Initialize with existing data
onMounted(() => {
  if (props.existingData && Object.keys(props.existingData).length > 0) {
    for (const [platform, data] of Object.entries(props.existingData)) {
      selectedPlatforms.value[platform] = {
        name: data.name || "", // Ensure name exists
        link: data.link || "" // Ensure link exists
      };
    }
    // Remove existing platforms from availableIcons
    availableIcons.value = availableIcons.value.filter(icon => !props.existingData[icon]);
  }
});

// Toggle available icons
const toggleIcons = () => {
  showIcons.value = !showIcons.value;
};

// Add a new platform
const addTextField = (icon) => {
  selectedPlatforms.value[icon] = { name: "", link: "" };
  availableIcons.value = availableIcons.value.filter((i) => i !== icon);
  showIcons.value = false;
  emit("update:selected", selectedPlatforms.value);
};

// Remove a platform
const removeTextField = (icon) => {
  delete selectedPlatforms.value[icon];
  availableIcons.value.push(icon);
  emit("update:selected", selectedPlatforms.value);
};

// Update a field (name or link)
const updateField = (icon, field, value) => {
  selectedPlatforms.value[icon][field] = value;
  emit("update:selected", selectedPlatforms.value);
};

// Capitalize the first letter of a word
const capitalize = (word) => word.charAt(0).toUpperCase() + word.slice(1);
</script>