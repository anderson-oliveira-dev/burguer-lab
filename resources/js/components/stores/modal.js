import { defineStore } from 'pinia';

export const useModalStore = defineStore('modal', {
    state: () => ({
        modals: {
            login: false,
        }
    }),
    getters: {
        isOpen: (state) => (modalId) => !!state.modals[modalId],
    },
    actions: {
        open(modalId) {
            this.modals[modalId] = true;
        },
        close(modalId) {
            this.modals[modalId] = false;
        },
        toggle(modalId) {
            this.modals[modalId] = !this.modals[modalId];
        },
        closeAll() {
            Object.keys(this.modals).forEach(key => {
                this.modals[key] = false;
            });
        }
    }
});