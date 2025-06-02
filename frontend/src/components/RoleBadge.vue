<template>
  <span class="role-badge" :class="getClass(roleName)">
    {{ roleName }}
  </span>
</template>

<script>
export default {
  name: 'RoleBadge',
  props: {
    roleName: {
      type: String,
      required: true
    },
    getClass: {
      type: Function,
      required: true
    }
  }
}
</script>

<style scoped>
.role-badge {
  display: inline-block;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
}

.role-admin {
  background-color: #dc3545;
  color: white;
}

.role-editor {
  background-color: #28a745;
  color: white;
}

.role-default {
  background-color: #f8f9fa;
  color: #333;
}
</style>