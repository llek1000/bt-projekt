<template>
  <div class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Vytvoriť nového používateľa</h3>
        <button @click="$emit('close')" class="close-btn">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="createUser">
          <div class="form-group">
            <label for="userName">Celé meno</label>
            <input type="text" id="userName" v-model="userForm.name" required />
          </div>
          
          <div class="form-group">
            <label for="userEmail">E-mailová adresa</label>
            <input type="email" id="userEmail" v-model="userForm.email" required />
          </div>
          
          <div class="form-group">
            <label for="userPassword">Heslo</label>
            <input
              type="password"
              id="userPassword"
              v-model="userForm.password"
              required
            />
          </div>
          
          <div class="form-group">
            <label for="userPasswordConfirm">Potvrdiť heslo</label>
            <input
              type="password"
              id="userPasswordConfirm"
              v-model="userForm.password_confirmation"
              required
            />
            <small v-if="passwordError" class="error-text">{{ passwordError }}</small>
          </div>
          
          <div class="form-group">
            <label for="userRole">Priradiť rolu</label>
            <select id="userRole" v-model="userForm.roleId" required>
              <option value="">Vyberte rolu</option>
              <option v-for="role in roles()" :key="role.id" :value="role.id">
                {{ role.name }}
              </option>
            </select>
          </div>
          
          <div class="form-actions">
            <button type="button" @click="$emit('close')" class="btn-cancel">
              Zrušiť
            </button>
            <button type="submit" class="btn-save">Vytvoriť používateľa</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'CreateUserModal',
  inject: ['roles', 'refreshUsers', 'adminPanel'],
  emits: ['close', 'user-created'],
  
  data() {
    return {
      userForm: {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        roleId: "",
      },
      passwordError: ""
    };
  },
  
  methods: {
    resetForm() {
      this.userForm = {
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        roleId: "",
      };
      this.passwordError = "";
    },
    
    async createUser() {
      try {
        if (this.userForm.password !== this.userForm.password_confirmation) {
          this.passwordError = "Heslá sa nezhodujú";
          return;
        }

        if (!this.userForm.roleId) {
          alert("Prosím vyberte rolu");
          return;
        }

        // Určenie názvu role pre zobrazenie
        let roleName = "Bez role";
        if (this.userForm.roleId) {
          const selectedRole = this.roles().find(
            role => role.id == this.userForm.roleId
          );
          if (selectedRole) {
            roleName = selectedRole.name;
          }
        }

        // Vytvorenie objektu používateľských údajov podľa UserData rozhrania
        const userData = {
          username: this.userForm.name,
          email: this.userForm.email,
          password: this.userForm.password,
          password_confirmation: this.userForm.password_confirmation,
          roles: [roleName]
        };

        console.log("Odosielané údaje používateľa:", JSON.stringify(userData));

        // Volanie createUser API
        const response = await this.adminPanel.createUser(userData);
        console.log("Odpoveď vytvorenia používateľa:", response);

        // Reset formulára a odoslanie úspechu
        this.resetForm();
        this.$emit('user-created');
        
      } catch (error) {
        console.error("Chyba pri vytváraní používateľa:", error);
        // Podrobnejšie zobrazenie chyby:
        if (error.response && error.response.data) {
          if (error.response.data.errors) {
            const errorMessages = Object.values(error.response.data.errors).flat().join(', ');
            alert("Chyby validácie: " + errorMessages);
          } else {
            alert("Nepodarilo sa vytvoriť používateľa: " + (error.response.data.message || "Neznáma chyba"));
          }
        } else {
          alert("Nepodarilo sa vytvoriť používateľa: " + (error.response?.data?.message || error.message || "Neznáma chyba"));
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
input[type="password"],
select {
  width: 100%;
  padding: 8px 12px;
  border: 1px solid #ced4da;
  border-radius: 4px;
  font-size: 14px;
}

.error-text {
  color: #dc3545;
  font-size: 12px;
  margin-top: 5px;
  display: block;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn-save, .btn-cancel {
  padding: 8px 12px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
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
</style>