<template>
  <div class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Upraviť používateľa: {{ selectedUser() ? selectedUser().name : '' }}</h3>
        <button @click="$emit('close')" class="close-btn">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="updateUser">
          <div class="form-group">
            <label for="editUserName">Celé meno</label>
            <input type="text" id="editUserName" v-model="editUserForm.name" required />
          </div>
          
          <div class="form-group">
            <label for="editUserEmail">E-mailová adresa</label>
            <input type="email" id="editUserEmail" v-model="editUserForm.email" required />
          </div>
          
          <div class="form-group">
            <label for="editUserRole">Rola používateľa</label>
            <select id="editUserRole" v-model="editUserForm.roleId" required>
              <option value="">Vyberte rolu</option>
              <option 
                v-for="role in roles()" 
                :key="role.id" 
                :value="role.id"
              >
                {{ role.name }}
              </option>
            </select>
          </div>
          
          <div class="form-actions">
            <div class="right-actions">
              <button type="button" @click="$emit('close')" class="btn-cancel">
                Zrušiť
              </button>
              <button type="submit" class="btn-save">
                Uložiť zmeny
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'EditUserModal',
  inject: ['roles', 'selectedUser', 'adminPanel'],
  emits: ['close', 'user-updated', 'change-password', 'delete'],
  
  data() {
    return {
      editUserForm: {
        id: null,
        name: "",
        email: "",
        roleId: "",
      },
      updateError: ""
    };
  },
  
  watch: {
    selectedUser: {
      immediate: true,
      handler() {
        const user = this.selectedUser();
        if (user) {
          // Nájdi zhodujúcu sa rolu
          let roleId = "";
          if (user.roles && user.roles.length > 0) {
            // Ak má používateľ role ako pole objektov
            const userRole = user.roles[0]; // Vezmi prvú rolu
            const matchingRole = this.roles().find(r => r.name === userRole.name);
            roleId = matchingRole ? matchingRole.id : "";
          } else if (user.role) {
            // Ak má používateľ role ako reťazec
            const matchingRole = this.roles().find(r => r.name === user.role);
            roleId = matchingRole ? matchingRole.id : "";
          }
          
          this.editUserForm = {
            id: user.id,
            name: user.username || user.name || "", // Skús obidve možnosti
            email: user.email || "",
            roleId: roleId
          };
        }
      }
    }
  },
  
  methods: {
    async updateUser() {
      try {
        if (!this.editUserForm.roleId) {
          alert("Prosím vyberte rolu");
          return;
        }

        // Vytvor objekt používateľských údajov
        const userData = {
          username: this.editUserForm.name,
          email: this.editUserForm.email,
        };

        console.log("Aktualizovanie používateľa:", JSON.stringify(userData));

        // Volanie API na aktualizáciu detailov používateľa
        const userResponse = await this.adminPanel.updateUser(this.editUserForm.id, userData);
        console.log("Odpoveď aktualizácie používateľa:", userResponse);

        // Tiež aktualizuj rolu používateľa
        const roleResponse = await this.adminPanel.assignRole(
          this.editUserForm.id,
          parseInt(this.editUserForm.roleId)
        );
        console.log("Odpoveď priradenia role:", roleResponse);

        this.$emit('user-updated');
      } catch (error) {
        console.error("Chyba pri aktualizácii používateľa:", error);
        
        // Spracovanie špecifických chýb pre degradáciu administrátora
        if (error.response && error.response.status === 403 && 
            error.response.data.error === 'admin_protection') {
          alert("Nie je možné degradovať administrátorský účet.");
        } else {
          alert("Nepodarilo sa aktualizovať používateľa: " + (error.response?.data?.message || error.message || "Neznáma chyba"));
        }
      }
    }
  }
}
</script>

<style scoped>
.modal {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal-content {
  background-color: #fff;
  border-radius: 8px;
  width: 500px;
  max-width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #eee;
}

.modal-header h3 {
  margin: 0;
  font-size: 18px;
}

.close-btn {
  background: none;
  border: none;
  font-size: 22px;
  cursor: pointer;
  color: #666;
}

.modal-body {
  padding: 20px;
}

.form-group {
  margin-bottom: 20px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 500;
}

input[type="text"],
input[type="email"],
select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
}

.password-change-container {
  margin: 15px 0;
}

.form-actions {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
}

.right-actions {
  display: flex;
  gap: 10px;
}

.btn-secondary, .btn-save, .btn-cancel, .btn-delete {
  padding: 8px 12px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
}

.btn-secondary {
  background-color: #6c757d;
  color: white;
}

.btn-secondary:hover {
  background-color: #5a6268;
}

.btn-save {
  background-color: #28a745;
  color: white;
}

.btn-save:hover {
  background-color: #218838;
}

.btn-cancel {
  background-color: #6c757d;
  color: white;
}

.btn-cancel:hover {
  background-color: #5a6268;
}

.btn-delete {
  background-color: #dc3545;
  color: white;
}

.btn-delete:hover {
  background-color: #c82333;
}
</style>