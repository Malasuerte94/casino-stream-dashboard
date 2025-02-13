<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {ref, onMounted, computed} from 'vue';
import vSelect from 'vue-select';
import "vue-select/dist/vue-select.css";
import { useGameStore } from '@/stores/gameStore';
import BonusBattleViewers from "@/Components/BonusBattle/BonusBattleViewers.vue";
import DialogModal from "@/Components/DialogModal.vue";
import { useBattleStore } from '@/stores/battleStore';

const battleStore = useBattleStore();
const loading = ref(true);
const title = ref('');
const stake = ref('5-10');
const prize = ref('');
const buys = ref('2');
const concurrents = computed(() => battleStore.concurrents);
const activeBattle = ref(null);
const activeConcurrents = ref(null);
const activeStage = ref(null);
const activeBracket = ref(null);
const activeScores = ref([{}]);
const totalBattles = ref(null);
const history = ref([]);
const activeSelect = ref(false);

const winner = ref(null);
const currentPair = ref([]);
let showForceClose = ref(false);

const gameStore = useGameStore();
const availableGames = computed(() => gameStore.availableGames);
let debounceTimer = ref(null);

onMounted(async () => {
  await fetchActiveBattle();
});

const cantStart = computed(() => {
  if (concurrents.value.length < 4) return true;
  return concurrents.value.some(concurrent => !concurrent.game_id);
});

const canGoNext = computed(() => {
  if(activeScores.value) {
    if (activeScores.value.length < 2) return false;
    return activeScores.value.every(score => score.cost_buy > 0);
  }
  return false
});

const confirmLogout = () => {
  showForceClose.value = true;
};

const closeModal = () => {
  showForceClose.value = false;
};

const addConcurrent = (number) => {
  battleStore.addConcurrent(number);
};

const addScore = async () => {
  currentPair.value.forEach((concurrent) => {
    const lastScore = concurrent?.scores.length > 0
        ? concurrent.scores[concurrent.scores.length - 1].cost_buy
        : 0;

    concurrent?.scores.push({
      bracket_id: activeBracket.value.id,
      bonus_concurrent_id: concurrent.id,
      cost_buy: lastScore,
      score: 0,
      result_buy: 0,
    });
  });

  await syncScores();
  await fetchActiveBattle();
};


const removeScore = async (concurrentIndex, scoreIndex) => {
  await deleteScore(currentPair.value[concurrentIndex].scores[scoreIndex].id);
  currentPair.value[concurrentIndex].scores.splice(scoreIndex, 1);
  await fetchActiveBattle();
};

const recalculateScore = async (concurrentIndex, scoreIndex) => {
  if (debounceTimer) {
    clearTimeout(debounceTimer);
  }
  debounceTimer = setTimeout(async () => {
    const scoreEntry = currentPair.value[concurrentIndex].scores[scoreIndex];
    if (scoreEntry.cost_buy > 0) {
      scoreEntry.score = parseFloat((scoreEntry.result_buy / scoreEntry.cost_buy).toFixed(3));
      await syncScores();
    } else {
      alert('Amount must be greater than 0.');
    }
  }, 1000);
};
const calculateTotalScore = (scores) => {
  return scores.reduce((total, score) => total + score.score, 0).toFixed(3);
};

const deleteScore = async (score_id) => {
  try {
    loading.value = true;
    const response = await axios.delete(`/api/bonus-battles/delete-score/${score_id}`);
    console.log('Bonus score deleted successfully:', response.data);
    await syncScores();
  } catch (error) {
    console.error('Failed to delete bonus score:', error.response?.data || error.message);
  } finally {
    loading.value = false;
  }
};

const syncScores = async () => {
  try {
    loading.value = true;
    const response = await axios.post('/api/bonus-battles/add-score', {
      active_bracket: activeBracket.value.id,
      bracket: currentPair.value
    });
    console.log('Bonus battle stored successfully:', response.data);
  } catch (error) {
    console.error('Failed to store bonus score:', error.response?.data || error.message);
  } finally {
    loading.value = false;
  }
}

const startBattle = async () => {
  if (!title.value || concurrents.value.some(c => !c.game_id)) {
    alert('All fields must be filled to start.');
    return;
  }

  try {
    const response = await axios.post('/api/bonus-battles', {
      title: title.value,
      stake: stake.value,
      prize: prize.value,
      buys: buys.value,
      concurrents: concurrents.value,
    });

    console.log('Bonus battle stored successfully:', response.data);

    await fetchActiveBattle();

  } catch (error) {
    console.error('Failed to store bonus battle:', error.response?.data || error.message);
    alert('An error occurred while saving the bonus battle. Please try again.');
  }
};

const endBattle = async () => {
  try {
    const response = await axios.post('/api/bonus-battles/end-battle', {
      bonus_battle_id: activeBattle.value.id,
    });
    winner.value = response.data.winner;
    await fetchActiveBattle();
  } catch (error) {
    console.log('endBattle error:', error.response?.data || error.message);
  }
};

const forceCloseBonusBattle = async () => {
  try {
    loading.value = true;
    await axios.post('/api/bonus-battles/end-battle-forced', {
      bonus_battle_id: activeBattle.value.id,
    });
    closeModal();
  } catch (error) {
    console.error('Failed to force close bonus battle:', error.response?.data || error.message);
    alert('A aparut o eroare la închiderea forțată a turneului. Te rugăm să în contactezi pe Mala!');
  } finally {
    await fetchActiveBattle();
    loading.value = false;
  }
};

const finishRound = async () => {
  try {
    loading.value = true;
    await axios.post(`/api/bonus-battles/finish-round`, {
      active_bracket: activeBracket.value.id
    });
    await fetchActiveBattle();
  } catch (error) {
    console.error('Failed to finish round:', error.response?.data || error.message);
    alert('An error occurred while finishing the round. Please try again.');
  } finally {
    loading.value = false;
  }
};

const updateGame = async (concurrentId, gameId) => {
  try {
    loading.value = true;
    await axios.put(`/api/bonus-battles/edit-concurrent`, {
      id: concurrentId,
      game_id: gameId
    });
    await fetchActiveBattle();
    activeSelect.value = false;
  } catch (error) {
    alert('Please try again.');
  } finally {
    loading.value = false;
  }
};

const fetchActiveBattle = async () => {
  try {
    loading.value = true;
    const response = await axios.get('/api/bonus-battles/active');
    totalBattles.value = response.data.total_battles;
    activeBattle.value = response.data.battle;
    activeConcurrents.value = response.data.concurrents;
    activeStage.value = response.data.stage;
    activeBracket.value = response.data.bracket;
    activeScores.value = response.data.scores;
    history.value = response.data.history;
    title.value = 'Bonus Battle #' + (totalBattles.value + 1);

    if (activeConcurrents.value) {
      const concurrentsList = activeConcurrents.value;
      currentPair.value = concurrentsList.slice(0, 2).map(concurrent => {
        const concurrentScores = activeScores.value.filter(
            score => score.bonus_concurrent_id === concurrent.id
        );
        return {
          ...concurrent,
          scores: concurrentScores.length ? concurrentScores : [],
        };
      });
    }

    const hasNoScores = currentPair.value.some(concurrent => concurrent.scores.length === 0);
    if (hasNoScores) {
      const buys = activeBattle.value?.buys || 0;
      if (buys > 0) {
        for (let i = 0; i < buys; i++) {
          await addScore();
        }
      }
    }

  } catch (error) {
    console.error('Failed to fetch active bonus battle:', error.message);
  } finally {
    loading.value = false;
  }
};

const getGameThumbnail = (gameId) => {
  const selectedGame = gameStore.availableGames.find(game => game.id === gameId);
  return selectedGame
      ? `/storage/games/${selectedGame.image}`
      : '';
};

const handleWinnersPicked = (winnersArray) => {
  console.log('Winners picked:', winnersArray);
  concurrents.value = concurrents.value.map((concurrent, index) => ({
    ...concurrent,
    for_user: winnersArray[index] !== undefined ? winnersArray[index] : ''
  }));
};

</script>
<template>
  <AppLayout title="Bonus Battle" v-if="!loading">
    <div class="py-4">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Hide form if there's an active battle -->
        <div v-if="!activeBattle && !winner" class="bg-gray-800 overflow-hidden shadow-lg rounded-md transition-all duration-300">
          <div class="p-6 space-y-4">
            <!-- Input Form -->
            <div class="flex flex-row gap-2">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-300">Titlu Bonus Battle</label>
                <input type="text" v-model="title" id="title" class="mt-1 block w-full input-primary" />
              </div>
              <div>
                <label for="stake" class="block text-sm font-medium text-gray-300">Miză (eg: 5-8, 5, 10-20, etc.)</label>
                <input type="text" v-model="stake" id="stake" class="mt-1 block w-full input-primary" />
              </div>
              <div>
                <label for="prize" class="block text-sm font-medium text-gray-300">Premiu</label>
                <input type="text" v-model="prize" id="prize" class="mt-1 block w-full input-primary" />
              </div>
              <div>
                <label for="buys" class="block text-sm font-medium text-gray-300">Câte buys per joc (poți face ulterior câte vrei)</label>
                <input type="text" v-model="buys" id="buys" class="mt-1 block w-full input-primary" />
              </div>
            </div>
            <div class="flex flex-col md:flex-row md:space-x-4">
              <div class="md:w-2/3 bg-gray-800 p-6 rounded-md shadow-md space-y-4 transition-all duration-300">
              <h3 class="text-lg font-semibold text-gray-300 flex gap-2 items-center">
                Participanți
                <div class="flex gap-4">
                  <button @click="addConcurrent(4)" class="btn-primary">4</button>
                  <button @click="addConcurrent(8)" class="btn-primary">8</button>
                  <button @click="addConcurrent(16)" class="btn-primary">16</button>
                  <button @click="addConcurrent(4)" class="btn-danger">RESET</button>
                </div>
              </h3>
              <transition-group name="fade">
                <div v-for="(concurrent, index) in concurrents" :key="index"
                   class="relative overflow-hidden flex items-center gap-4 bg-gray-900 p-4 rounded-md shadow transition-all duration-300"
                   :style="concurrent.game_id ? {'--bg-image': 'url(' + getGameThumbnail(concurrent.game_id) + ')'} : {}">
                <!-- Blurred Background Layer -->
                <div v-if="concurrent.game_id"
                     class="absolute inset-0 pointer-events-none bg-blur transition-all duration-300"></div>
                <!-- Row Content -->
                <div class="relative z-10 flex items-center gap-4 w-full">
                  <img v-if="concurrent.game_id"
                       :src="getGameThumbnail(concurrent.game_id)"
                       alt="Game Thumbnail"
                       class="w-16 h-16 object-cover rounded-md"/>
                  <v-select
                      :options="availableGames"
                      label="name"
                      :reduce="game => game.id"
                      v-model="concurrent.game_id"
                      placeholder="Alege Joc"
                      class="flex-1 transition-all duration-300"
                      append-to-body
                  />
                  <input type="text" v-model="concurrent.for_user"
                         class="flex-1 input-primary"
                         placeholder="Cine a ales? (opțional)"/>
                </div>
              </div>
              </transition-group>
            </div>
              <BonusBattleViewers @winnersPicked="handleWinnersPicked" :fillCount="concurrents.length" />
            </div>
            <button :disabled="cantStart"
                    :class="{'opacity-50 cursor-not-allowed': cantStart}"
                    type="button" @click="startBattle"
                    class="mt-4 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-all duration-300">
              Start
            </button>
          </div>
        </div>

        <div v-if="winner && !activeBattle" class="bg-gray-900 overflow-hidden shadow-lg rounded-md transition-all duration-300">
          <div class="p-6 flex justify-center items-center bg-gray-800">
            <div class="w-full max-w-md bg-gray-900 shadow-lg rounded-md p-6 text-center transition-all duration-300">
              <img v-if="winner.game.image"
                   :src="getGameThumbnail(winner.game.id)"
                   alt="Game Thumbnail"
                   class="w-32 mx-auto object-cover rounded-md mb-4"/>
              <h2 class="text-2xl font-bold text-gray-100 mb-2">
                Câștigător! - {{ winner.game.name }}
              </h2>
              <p class="text-lg font-medium text-gray-300">
                User: {{ winner.for_user }}
              </p>
            </div>
          </div>
        </div>


        <!-- Log Out Other Devices Confirmation Modal -->
        <DialogModal :show="showForceClose" @close="closeModal">
          <template #title>
            Ești sigur că vrei să închizi forțat acest Bonus Battle?
          </template>

          <template #content>
            Vom închide forțat acest Bonus Battle și nu vei mai putea reveni la el. Ești sigur că vrei să continui?
          </template>

          <template #footer>
            <button @click="closeModal" class="btn-danger">
              ANULEAZĂ
            </button>

            <button
                class="ml-3 btn-primary"
                @click="forceCloseBonusBattle"
            >
              ÎNCHIDE FORȚAT ACEST BONUS BATTLE
            </button>
          </template>
        </DialogModal>

        <!-- Active Battle Display -->
        <div v-if="activeBattle" class="bg-gray-900 overflow-hidden shadow-lg rounded-md mt-6 transition-all duration-300">
          <div class="p-6">
            <div class="flex justify-between items-center">
              <h2 class="text-xl font-bold mb-4 text-gray-100">Active Battle: {{ activeBattle.title }}</h2>
              <button class="btn-danger" @click="confirmLogout">
                INCHIDE TURNEU FORȚAT
              </button>
            </div>
            <p class="text-gray-300">
              Miza: {{ activeBattle.stake }} | Etapa: {{ activeStage.name }} | Premiu: {{ activeBattle.prize }} | Buys: {{ activeBattle.buys }}
            </p>

            <!-- Brackets Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4" v-if="currentPair.length > 1">
              <div v-for="(concurrent, index) in currentPair" :key="index"
                   class="relative border rounded-md p-4 shadow transition-all duration-300 game-active-row"
                   :class="concurrent.game.image ? 'bg-transparent' : 'bg-gray-900'"
                   :style="concurrent.game.image ? {'--bg-image': 'url(' + getGameThumbnail(concurrent.game.id) + ')'} : {}">
                <!-- Blurred Background Layer -->
                <div v-if="concurrent.game.image"
                     class="absolute inset-0 pointer-events-none bg-blur transition-all duration-300"></div>
                <!-- Row Content with overlay -->
                <div class="relative z-10 p-4" :class="concurrent.game.image ? 'bg-black bg-opacity-60' : 'bg-gray-900'">
                  <div class="flex gap-2 items-center mb-2 p-2 rounded-md border border-gray-600 transition-all duration-300">
                    <img v-if="concurrent.game.image"
                         :src="getGameThumbnail(concurrent.game.id)"
                         alt="Game Thumbnail"
                         class="w-auto h-[100px] rounded-md"/>
                    <div class="w-full">
                      <h3 class="text-lg font-bold mb-2 text-gray-100">
                        {{ concurrent.game.name }} - {{ concurrent.for_user }}
                      </h3>
                      <div class="flex flex-row gap-2">
                        <v-select :disabled="!activeSelect"
                                  :options="availableGames"
                                  label="name"
                                  :reduce="game => game.id"
                                  @update:modelValue="updateGame(concurrent.id, $event)"
                                  placeholder="Schimbi jocul?"
                                  class="flex-1 transition-all duration-300"
                                  append-to-body
                        />
                        <button class="btn-secondary transition-all duration-300" @click="activeSelect = !activeSelect">
                          SCHIMBA JOCU
                        </button>
                      </div>
                    </div>
                  </div>
                  <!-- Scores Section -->
                  <div>
                    <div v-for="(score, scoreIndex) in concurrent.scores" :key="scoreIndex"
                         class="flex items-center space-x-2 mt-1 transition-all duration-300">
                      <label class="text-sm font-medium text-gray-300">
                        Cost Buy
                        <input type="number" v-model="concurrent.scores[scoreIndex].cost_buy"
                               class="block w-40 border border-gray-600 rounded-md shadow-sm p-2 transition-all duration-300 input-primary"
                               :disabled="loading"
                               :class="{'input-disabled': loading}"
                               placeholder="Amount" @input="recalculateScore(index, scoreIndex)"/>
                      </label>
                      <label class="text-sm font-medium text-gray-300">
                        Rezultat Buy
                        <input :disabled="score.cost_buy <= 0 || loading"
                               :class="{'input-disabled': score.cost_buy <= 0 || loading, 'input-primary': score.cost_buy > 0}"
                               type="number" v-model="concurrent.scores[scoreIndex].result_buy"
                               class="block w-40 border border-gray-600 rounded-md shadow-sm p-2 transition-all duration-300"
                               placeholder="Result" @input="recalculateScore(index, scoreIndex)"/>
                      </label>
                      <label class="text-sm font-medium text-gray-300">
                        Scor
                        <input readonly disabled type="number" v-model="concurrent.scores[scoreIndex].score"
                               class="block w-20 border border-gray-600 rounded-md shadow-sm p-2 input-disabled transition-all duration-300"
                               placeholder="Result"/>
                      </label>
                      <button v-if="scoreIndex >= 1" type="button" @click="removeScore(index, scoreIndex)"
                              class="btn-danger transition-all duration-300 align-middle">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                      </button>
                    </div>
                    <button type="button" @click="addScore()" :disabled="loading"
                            :class="{'input-disabled': loading}"
                            class="btn-primary mt-2">
                      Adaugă Buy
                    </button>
                  </div>
                  <p class="mt-4 text-lg font-bold text-gray-100">
                    Scor Total: {{ calculateTotalScore(concurrent.scores) }}
                  </p>
                </div>
              </div>
            </div>
            <div v-if="winner" class="mt-4">
              <p class="text-lg font-bold text-gray-100">Câștigător: {{ winner.game.name }} | User: {{ winner.for_user }}</p>
            </div>
            <div class="mt-6 flex flex-col gap-4">
              <button v-if="activeStage.name !== 'Final'" type="button" @click="finishRound"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-all duration-300"
                      :disabled="!canGoNext || loading" :class="{'input-disabled opacity-50': !canGoNext || loading}">
                Următoarii Concurenți
              </button>
              <button v-if="activeStage.name === 'Final'" type="button" @click="endBattle"
                      class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md transition-all duration-300"
                      :disabled="!canGoNext" :class="{'input-disabled opacity-50': !canGoNext}">
                Termina Battle
              </button>
            </div>
          </div>
          <div class="p-6">
            <table v-if="history.stage_brackets.length > 0" class="table-auto w-full text-sm text-gray-200 mb-2 transition-all duration-300">
              <tbody>
              <tr v-for="(bracket, index) in history.stage_brackets" :key="bracket.id"
                  :class="index % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'">
                <td class="px-4 py-2 font-bold">
                    <span :class="{ 'line-through text-gray-500': bracket.winner !== bracket.participant_a }">
                      {{ bracket.participant_a }}
                    </span>
                  vs
                  <span :class="{ 'line-through text-gray-500': bracket.winner !== bracket.participant_b }">
                      {{ bracket.participant_b }}
                    </span>
                </td>
                <td class="px-4 py-2">
                  {{ parseFloat(bracket.participant_a_score).toFixed(3) }} - {{ parseFloat(bracket.participant_b_score).toFixed(3) }}
                </td>
              </tr>
              </tbody>
            </table>
            <table v-if="history.concurrents.length > 0" class="table-auto w-full text-sm text-gray-200 transition-all duration-300">
              <tbody>
              <tr v-for="(concurrent, index) in history.concurrents" :key="concurrent?.id"
                  :class="index % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'">
                <td class="border border-black px-1 py-1 shrink w-0">
                  {{ concurrent?.is_eliminated ? '❌' : '✅' }}
                </td>
                <td class="border border-black px-2 py-1">
                  {{ concurrent?.game.name || 'N/A' }}
                </td>
                <td class="border border-black px-2 py-1">
                  {{ concurrent?.for_user || 'N/A' }}
                </td>
              </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style>
/* Blurred Background for Rows */
.game-active-row,
.game-row {
  position: relative;
  overflow: hidden;
}
.game-active-row .row-content,
.game-row .row-content {
  position: relative;
  z-index: 10;
}
.game-active-row.bg-transparent .row-content,
.game-row.bg-transparent .row-content {
  @apply bg-black bg-opacity-60;
}
.game-active-row:not(.bg-transparent) .row-content,
.game-row:not(.bg-transparent) .row-content {
  @apply bg-gray-900;
}
</style>

