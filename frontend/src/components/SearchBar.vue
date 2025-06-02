<template>
  <div class="search-container">
    <input
      type="text"
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value); $emit('input')"
      placeholder="Search users..."
      class="search-input"
    />
  </div>
</template>

<script>
export default {
  name: 'SearchBar',
  props: {
    modelValue: {
      type: String,
      required: true
    }
  },
  emits: ['update:modelValue', 'input']
}
</script>

<style scoped>
.search-container {
  position: relative;
}

.search-input {
  padding: 8px 12px;
  border-radius: 4px;
  border: 1px solid #ced4da;
  min-width: 250px;
}
</style>