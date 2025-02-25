<template>
  <div class="relative">
    <input
        :type="type"
        v-model="localValue"
        :disabled="disabled"
        :readonly="disabled"
        placeholder=" "
        :class="{'input-disabled': disabled}"
        @input="handleInput"
        class="input-primary block rounded-t-lg px-2.5 pb-2.5 pt-5 w-full text-sm border-0 border-b-2 border-gray-300 appearance-none dark:text-white dark:border-gray-600 focus:outline-none focus:ring-0 peer"
    />
    <label
        for="floating_filled"
        class="pl-4 pointer-events-none absolute text-sm text-gray-500 dark:text-gray-400 duration-300 transform -translate-y-3 scale-75 top-4 z-10 origin-[0] start-2.5 peer-focus:text-indigo-600 peer-focus:dark:text-indigo-500 peer-placeholder-shown:scale-100 peer-placeholder-shown:translate-y-0 peer-focus:scale-75 peer-focus:-translate-y-3 rtl:peer-focus:translate-x-1/4 rtl:peer-focus:left-auto"
    >
      {{ label }}
    </label>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  disabled: {
    type: Boolean,
    default: false
  },
  type: {
    type: String,
    default: 'text'
  },
  label: {
    type: String,
    required: true
  }
});

const emit = defineEmits(['update:modelValue', 'input']);
const localValue = ref(props.modelValue);

// Emit both update:modelValue (for v-model) and input event
const handleInput = (event) => {
  const newVal = event.target.value;
  localValue.value = newVal;
  emit('input', newVal);
};

// Watch local value to keep parent in sync
watch(localValue, (newVal) => {
  emit('update:modelValue', newVal);
});

// Watch for external changes to update the local value
watch(
    () => props.modelValue,
    (newVal) => {
      localValue.value = newVal;
    }
);
</script>
