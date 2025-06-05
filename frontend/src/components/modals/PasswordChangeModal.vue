<template>
  <div class="modal">
    <div class="modal-content">
      <div class="modal-header">
        <h3>Zmeniť heslo: {{ selectedUser() ? selectedUser().username : '' }}</h3>
        <button @click="$emit('close')" class="close-btn">&times;</button>
      </div>
      <div class="modal-body">
        <form @submit.prevent="updatePassword">
          <div class="form-group">
            <label for="newPassword">Nové heslo</label>
            <input
              id="newPassword"
              type="password"
              v-model="passwordForm.password"
              required
            />
          </div>
          <div class="form-group">
            <label for="confirmNewPassword">Potvrdiť nové heslo</label>
            <input
              id="confirmNewPassword"
              type="password"
              v-model="passwordForm.password_confirmation"
              required
            />
            <small v-if="passwordError" class="error-text">{{ passwordError }}</small>
          </div>
          <div class="form-actions">
            <div class="right-actions">
              <button type="button" @click="$emit('close')" class="btn-cancel">
                Zrušiť
              </button>
              <button type="submit" class="btn-save">
                Uložiť heslo
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
  name: 'PasswordChangeModal',
  inject: ['selectedUser', 'adminPanel'],
  emits: ['close', 'password-updated'],
  
  data() {
    return {
      passwordForm: {
        password: "",
        password_confirmation: ""
      },
      passwordError: ""
    };
  },
  
  methods: {
    async updatePassword() {
      try {
        // Validácia zhody hesiel
        if (this.passwordForm.password !== this.passwordForm.password_confirmation) {
          this.passwordError = "Heslá sa nezhodujú";
          return;
        }

        const user = this.selectedUser();
        if (!user) return;

        // Vytvorenie údajov pre aktualizáciu hesla
        const passwordData = {
          password: this.passwordForm.password,
          password_confirmation: this.passwordForm.password_confirmation
        };

        // Volanie API na aktualizáciu hesla
        await this.adminPanel.updateUser(user.id, passwordData);
        
        // Reset formulára a emitovanie udalosti
        this.passwordForm = { password: "", password_confirmation: "" };
        this.passwordError = "";
        
        // Emitovanie udalosti pre zatvorenie modalu
        this.$emit('password-updated');
        this.$emit('close');
        
      } catch (error) {
        console.error("Chyba pri aktualizácii hesla:", error);
        this.passwordError = error.response?.data?.message || error.message || "Nepodarilo sa aktualizovať heslo";
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

input[type="password"] {
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
  margin-top: 20px;
}

.right-actions {
  display: flex;
  gap: 10px;
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