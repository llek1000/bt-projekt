<template>
  <div class="table-wrapper">
    <table class="user-table">
      <thead>
        <tr>
          <th>User ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Current Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="user in users" :key="user.id">
          <td>{{ user.id }}</td>
          <td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td>
            <role-badge :role-name="user.role" :get-class="getRoleBadgeClass" />
          </td>
          <td class="actions">
            <button @click="$emit('edit-user', user)" class="btn-edit">
              Edit User
            </button>
          </td>
        </tr>
        <tr v-if="users.length === 0">
          <td colspan="5" class="empty-state">No users found.</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import RoleBadge from './RoleBadge.vue';

export default {
  name: 'UserTable',
  components: { RoleBadge },
  props: {
    users: {
      type: Array,
      required: true
    },
    getRoleBadgeClass: {
      type: Function,
      required: true
    }
  },
  emits: ['edit-user']
}
</script>

<style scoped>
.table-wrapper {
  overflow-x: auto;
}

table {
  width: 100%;
  border-collapse: collapse;
}

th, td {
  text-align: left;
  padding: 12px 15px;
  border-bottom: 1px solid #eee;
}

th {
  background-color: #f8f9fa;
  font-weight: 600;
  color: #333;
}

.empty-state {
  text-align: center;
  color: #666;
  padding: 30px;
}

.actions {
  display: flex;
  gap: 8px;
}

.btn-edit {
  background-color: #17a2b8;
  color: white;
  padding: 8px 12px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
}

.btn-edit:hover {
  background-color: #138496;
}
</style>