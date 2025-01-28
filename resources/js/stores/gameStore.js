import { defineStore } from 'pinia';
import axios from 'axios';

export const useGameStore = defineStore('gameStore', {
  state: () => ({
    games: [],
    loading: false,
    error: null,
  }),
  getters: {
    availableGames: (state) => {
      if (!state.loading && state.games.length === 0) {
        state.fetchGames();
      }
      return state.games;
    },
  },
  actions: {
    async fetchGames() {
      if (this.loading) return; // Prevent duplicate calls
      this.loading = true;
      this.error = null;
      try {
        const response = await axios.get('/api/games');
        this.games = response.data.games;
      } catch (error) {
        console.error('Error fetching games:', error);
        this.error = 'Failed to fetch games.';
      } finally {
        this.loading = false;
      }
    },
  },
});
