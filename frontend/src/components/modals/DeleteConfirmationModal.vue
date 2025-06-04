<template>
  <div class="modal">
    <div class="modal-content confirmation-modal">
      <div class="modal-header">
        <h3>Potvrdiť odstránenie</h3>
        <button @click="$emit('close')" class="close-btn">&times;</button>
      </div>
      <div class="modal-body">
        <p>
          Ste si istí, že chcete odstrániť používateľa
          <strong>{{ selectedUser() ? selectedUser().name : '' }}</strong>?
        </p>
        <p class="warning-text">Táto akcia sa nedá vrátiť späť.</p>

        <div class="form-actions">
          <button @click="$emit('close')" class="btn-cancel">
            Zrušiť
          </button>
          <button @click="confirmDelete" class="btn-delete">
            Áno, odstrániť používateľa
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DeleteConfirmationModal',
  inject: ['selectedUser', 'adminPanel'],
  emits: ['close', 'confirm'],
  
  methods: {
    async confirmDelete() {
      try {
        const user = this.selectedUser();
        if (!user) return;
        
        // Volanie API na odstránenie používateľa
        await this.adminPanel.deleteUser(user.id);
        this.$emit('confirm');
        
      } catch (error) {
        console.error("Chyba pri odstraňovaní používateľa:", error);

        // Spracovanie chýb oprávnenia (napríklad pokus o odstránenie admin používateľa)
        if (error.response && error.response.status === 403) {
          alert(error.response.data.message || "Nemáte oprávnenie na odstránenie tohto používateľa.");
        } else {
          alert("Nepodarilo sa odstrániť používateľa: " + (error.response?.data?.message || error.message || "Neznáma chyba"));
        }
        
        this.$emit('close');
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

.confirmation-modal {
  max-width: 400px;
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

.warning-text {
  color: #dc3545;
  font-weight: 500;
  margin-top: 10px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  margin-top: 20px;
}

.btn-cancel, .btn-delete {
  padding: 8px 12px;
  border-radius: 4px;
  font-weight: 500;
  cursor: pointer;
  border: none;
  transition: background-color 0.2s;
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