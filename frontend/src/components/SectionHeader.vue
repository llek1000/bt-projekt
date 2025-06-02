<template>
  <div class="section-header">
    <h2>{{ title }}</h2>
    <div class="actions-container">
      <button @click="$emit('create-user')" class="btn-primary">
        Create New User
      </button>
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: 'SectionHeader',
  props: {
    title: {
      type: String,
      required: true
    }
  },
  emits: ['create-user']
}
</script>

<style scoped>
.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 20px;
}

.section-header h2 {
  font-size: 20px;
  color: #333;
}

.actions-container {
  display: flex;
  gap: 15px;
  align-items: center;
}

.btn-primary {
  background-color: #007bff;
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
}

.btn-primary:hover {
  background-color: #0069d9;
}
</style>