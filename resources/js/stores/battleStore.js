// stores/battleStore.js
import { defineStore } from 'pinia';

export const useBattleStore = defineStore('battle', {
  state: () => ({
    concurrents: [
      { game_id: null, for_user: null },
      { game_id: null, for_user: null },
      { game_id: null, for_user: null },
      { game_id: null, for_user: null }
    ],
  }),
  actions: {
    setConcurrents(newConcurrents) {
      this.concurrents = newConcurrents;
    },
    addConcurrent(number) {
      this.concurrents = Array.from({ length: number }, () => ({
        game_id: '',
        for_user: '',
      }));
    },
    updateConcurrent(index, payload) {
      this.concurrents[index] = { ...this.concurrents[index], ...payload };
    }
  },
  persist: true,
});
