<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {ref, onMounted, computed} from 'vue';
import vSelect from 'vue-select';
import "vue-select/dist/vue-select.css";
import { useGameStore } from '@/stores/gameStore';

const loading = ref(true);
const title = ref('');
const stake = ref('5-10');
const prize = ref('');
const buys = ref('2');
const concurrents = ref([
  { game_id: null, for_user: null },
  { game_id: null, for_user: null }
]);
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

const gameStore = useGameStore();
const availableGames = computed(() => gameStore.availableGames);

onMounted(async () => {
  await fetchActiveBattle();
});

const cantStart = computed(() => {
  if (concurrents.value.length < 2) return true;
  return concurrents.value.some(concurrent => !concurrent.game_id);
});

const canGoNext = computed(() => {
  if(activeScores.value) {
    if (activeScores.value.length < 2) return false;
    return activeScores.value.every(score => score.cost_buy > 0);
  }
  return false
});

const addConcurrent = (number) => {
  concurrents.value = Array.from({ length: number }, () => ({
    game_id: '',
    for_user: '',
  }));
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
  const scoreEntry = currentPair.value[concurrentIndex].scores[scoreIndex];
  if (scoreEntry.cost_buy > 0) {
    scoreEntry.score = parseFloat((scoreEntry.result_buy / scoreEntry.cost_buy).toFixed(3));
    await syncScores();
  } else {
    alert('Amount must be greater than 0.');
  }
};
const calculateTotalScore = (scores) => {
  return scores.reduce((total, score) => total + score.score, 0).toFixed(3);
};

const deleteScore = async (score_id) => {
  try {
    const response = await axios.delete(`/api/bonus-battles/delete-score/${score_id}`);
    console.log('Bonus score deleted successfully:', response.data);
    await syncScores();
  } catch (error) {
    console.error('Failed to delete bonus score:', error.response?.data || error.message);
  }
};

const syncScores = async () => {
  try {
    const response = await axios.post('/api/bonus-battles/add-score', {
      active_bracket: activeBracket.value.id,
      bracket: currentPair.value
    });

    console.log('Bonus battle stored successfully:', response.data);

  } catch (error) {
    console.error('Failed to store bonus score:', error.response?.data || error.message);
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

const finishRound = async () => {
  try {
    await axios.post(`/api/bonus-battles/finish-round`, {
      active_bracket: activeBracket.value.id
    });
    await fetchActiveBattle();
  } catch (error) {
    console.error('Failed to finish round:', error.response?.data || error.message);
    alert('An error occurred while finishing the round. Please try again.');
  }
};

const updateGame = async (concurrentId, gameId) => {
  try {
    await axios.put(`/api/bonus-battles/edit-concurrent`, {
      id: concurrentId,
      game_id: gameId
    });
    await fetchActiveBattle();
    activeSelect.value = false;
  } catch (error) {
    alert('Please try again.');
  }
};

const fetchActiveBattle = async () => {
  try {
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
</script>
<template>
  <AppLayout title="Bonus Battle" v-if="!loading">
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Hide form if there's an active battle -->
        <div v-if="!activeBattle && !winner" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 space-y-4">
            <!-- Input Form -->
            <div class="flex flex-row gap-2">
              <div>
                <label for="title" class="block text-sm font-medium text-gray-700">Titlu Bonus Battle</label>
                <input type="text" v-model="title" id="title"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
              </div>
              <div>
                <label for="stake" class="block text-sm font-medium text-gray-700">Miză (eg: 5-8, 5, 10-20, etc.)</label>
                <input type="text" v-model="stake" id="stake"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
              </div>
              <div>
                <label for="stake" class="block text-sm font-medium text-gray-700">Premiu</label>
                <input type="text" v-model="prize" id="prize"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
              </div>
              <div>
                <label for="stake" class="block text-sm font-medium text-gray-700">Câte buys per joc (poți face ulterior câte vrei)</label>
                <input type="text" v-model="buys" id="buys"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
              </div>
            </div>
            <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg shadow-md space-y-4">
              <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 flex gap-2 items-center">Participanți
                <div class="flex gap-4">
                  <button @click="addConcurrent(2)" class="btn-primary">2</button>
                  <button @click="addConcurrent(4)" class="btn-primary">4</button>
                  <button @click="addConcurrent(8)" class="btn-primary">8</button>
                  <button @click="addConcurrent(16)" class="btn-primary">16</button>
                </div>
              </h3>
              <div v-for="(concurrent, index) in concurrents" :key="index" class="flex items-center gap-4 bg-white dark:bg-gray-700 p-4 rounded-lg shadow">
                <img
                    v-if="concurrent.game_id"
                    :src="getGameThumbnail(concurrent.game_id)"
                    alt="Game Thumbnail"
                    class="w-16 h-16 object-cover rounded-lg"
                />
                <v-select
                    :options="availableGames"
                    label="name"
                    :reduce="game => game.id"
                    v-model="concurrent.game_id"
                    placeholder="Alege Joc"
                    class="flex-1 input-select"
                />
                <input
                    type="text"
                    v-model="concurrent.for_user"
                    class="flex-1 bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white dark:placeholder-gray-400 p-2.5"
                    placeholder="Cine a ales? (opțional)"
                />
              </div>
            </div>
            <button :disabled="cantStart"
                    :class="{'input-disabled opacity-50': cantStart}"
                    type="button" @click="startBattle" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md">Start</button>
          </div>
        </div>
        <div v-if="winner && !activeBattle" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 flex justify-center items-center bg-gray-100 dark:bg-gray-800">
            <div class="w-full max-w-md bg-white dark:bg-gray-700 shadow-lg rounded-lg p-6 text-center">
              <img
                  v-if="winner.game.image"
                  :src="getGameThumbnail(winner.game.id)"
                  alt="Game Thumbnail"
                  class="w-32 auto mx-auto object-cover rounded-lg mb-4"
              />
              <h2 class="text-2xl font-bold text-gray-800 dark:text-white mb-2">
                Câștigător! - {{ winner.game.name }}
              </h2>
              <p class="text-lg font-medium text-gray-600 dark:text-gray-300">
                User: {{ winner.for_user }}
              </p>
            </div>
          </div>
        </div>
        <!-- Active Battle Display -->
        <div v-if="activeBattle" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
          <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Active Battle: {{ activeBattle.title }}</h2>
            <p>Miza: {{ activeBattle.stake }} | Etapa: {{ activeStage.name }} | Premiu: {{ activeBattle.prize }} | Buys: {{ activeBattle.buys }}</p>

            <!-- Brackets Section -->
            <div class="grid grid-cols-2 gap-4 mt-4" v-if="currentPair.length > 1">
              <div v-for="(concurrent, index) in currentPair" :key="index" class="border rounded-md p-4 shadow">
                <div class="flex gap-2 items-center mb-2 bg-blend-darken  p-2 rounded border-blue-900 border">
                <img
                    v-if="concurrent.game.image"
                    :src="getGameThumbnail(concurrent.game.id)"
                    alt="Game Thumbnail"
                    class="w-auto h-[100px] rounded-lg"
                />
                  <div class="w-full">
                    <h3 class="text-lg font-bold mb-2">{{ concurrent.game.name }} - {{ concurrent.for_user}}</h3>
                    <div class="flex flex-row">
                      <v-select
                          :disabled="!activeSelect"
                          :options="availableGames"
                          label="name"
                          :reduce="game => game.id"
                          @update:modelValue="updateGame(concurrent.id, $event)"
                          placeholder="Schimbi jocul?"
                          class="flex-1"
                      />
                      <div>
                        <button class="btn-secondary" @click="activeSelect = !activeSelect">SCHIMBA JOCU</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div>
                  <div v-for="(score, scoreIndex) in concurrent.scores" :key="scoreIndex"
                       class="flex items-center space-x-2 mt-1">
                    <label class="text-sm font-medium text-gray-700">
                      Cost Buy
                      <input
                          type="number"
                          v-model="concurrent.scores[scoreIndex].cost_buy"
                          class="block w-40 border-gray-300 rounded-md shadow-sm"
                          placeholder="Amount"
                      />
                    </label>
                    <label class="text-sm font-medium text-gray-700">
                      Rezultat Buy
                      <input
                          :disabled="score.cost_buy <= 0"
                          :class="{'input-disabled opacity-50': score.cost_buy <= 0}"
                          type="number"
                          v-model="concurrent.scores[scoreIndex].result_buy"
                          class="block w-40 border-gray-300 rounded-md shadow-sm"
                          placeholder="Result"
                          @input="recalculateScore(index, scoreIndex)"
                      />
                    </label>
                    <label class="text-sm font-medium text-gray-700">
                      Scor
                      <input
                          readonly
                          disabled
                          type="number"
                          v-model="concurrent.scores[scoreIndex].score"
                          class="block w-20 border-gray-300 rounded-md shadow-sm disabled input-disabled"
                          placeholder="Result"
                      />
                    </label>
                    <button
                        v-if="scoreIndex >= 1"
                        type="button"
                        @click="removeScore(index, scoreIndex)"
                        class="bg-red-600 text-white px-4 py-2 rounded-md text-sm"
                    >
                      X
                    </button>
                  </div>
                  <button
                      type="button"
                      @click="addScore()"
                      class="mt-2 bg-blue-600 text-white px-4 py-2 rounded-md text-sm"
                  >
                    Adaugă Buy
                  </button>
                </div>
                <p class="mt-4 text-lg font-bold">
                  Scor Total: {{ calculateTotalScore(concurrent.scores) }}
                </p>
              </div>
            </div>
            <div v-if="winner">
              <p class="mt-4 text-lg font-bold">Câștigător: {{ winner.game.name }} | User: {{ winner.for_user }}</p>
            </div>

            <button
                v-if="activeStage.name !== 'Final'"
                type="button"
                @click="finishRound"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
                :disabled="!canGoNext"
                :class="{'input-disabled opacity-50': !canGoNext}"
            >
              Următoarii Concurenți
            </button>
            <button
                :disabled="!canGoNext"
                :class="{'input-disabled opacity-50': !canGoNext}"
                v-if="activeStage.name === 'Final'"
                type="button"
                @click="endBattle"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
            >
              Termina Battle
            </button>
          </div>
          <div class="p-6">
            <table v-if="history.stage_brackets.length > 0" class="table-auto w-full text-sm text-gray-200 mb-2">
              <tbody>
              <tr
                  v-for="(bracket, index) in history.stage_brackets"
                  :key="bracket.id"
                  :class="index % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'"
              >
                <!-- Game Names -->
                <td class="px-4 py-2 font-bold">
        <span :class="{ 'line-through text-gray-500': bracket.winner !== bracket.participant_a }">
          {{ bracket.participant_a }}
        </span>
                  vs
                  <span :class="{ 'line-through text-gray-500': bracket.winner !== bracket.participant_b }">
          {{ bracket.participant_b }}
        </span>
                </td>

                <!-- Scores -->
                <td class="px-4 py-2">
                  {{ parseFloat(bracket.participant_a_score).toFixed(3) }} - {{ parseFloat(bracket.participant_b_score).toFixed(3) }}
                </td>
              </tr>
              </tbody>
            </table>


            <table v-if="history.concurrents.length > 0" class="table-auto w-full text-sm text-gray-200">
              <tbody>
              <tr
                  v-for="(concurrent, index) in history.concurrents"
                  :key="concurrent?.id"
                  :class="index % 2 === 0 ? 'bg-gray-900' : 'bg-gray-800'"
              >
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
.btn-primary {
  @apply bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300;
  @apply dark:bg-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-800;
}

.btn-danger {
  @apply bg-red-600 text-white px-4 py-2 rounded-md text-sm hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300;
  @apply dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800;
}
</style>