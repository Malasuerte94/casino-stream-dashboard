<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import {ref, onMounted} from 'vue';

const title = ref('');
const stake = ref('');
const concurrents = ref([]);

const activeBattle = ref(null);
const activeConcurrents = ref(null);
const activeStage = ref(null);
const activeBracket = ref(null);
const activeScores = ref(null);
const totalBattles = ref(null);

const winner = ref(null);
const currentPair = ref([]);

const addConcurrent = () => {
  const currentCount = concurrents.value.length;
  const nextCount = currentCount === 0 ? 2 : currentCount * 2;
  for (let i = 0; i < nextCount - currentCount; i++) {
    concurrents.value.push({name: '', for_user: ''});
  }
};

const removeConcurrent = () => {
  const currentCount = concurrents.value.length;
  if (currentCount <= 2) {
    alert('You must have at least 2 participants.');
    return;
  }
  const nextCount = currentCount / 2;
  concurrents.value.splice(nextCount);
};

const addScore = async () => {
  currentPair.value.forEach((concurrent) => {
    concurrent?.scores.push({
      bracket_id: activeBracket.value.id,
      bonus_concurrent_id: concurrent.id,
      cost_buy: 0,
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
};

const recalculateScore = async (concurrentIndex, scoreIndex) => {
  const scoreEntry = currentPair.value[concurrentIndex].scores[scoreIndex];
  if (scoreEntry.cost_buy > 0) {
    scoreEntry.score = parseFloat((scoreEntry.result_buy / scoreEntry.cost_buy).toFixed(2));
    await syncScores();
  } else {
    alert('Amount must be greater than 0.');
  }
};
const calculateTotalScore = (scores) => {
  return scores.reduce((total, score) => total + score.score, 0).toFixed(2);
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
  if (!title.value || concurrents.value.some(c => !c.name)) {
    alert('All fields must be filled to start.');
    return;
  }

  try {
    const response = await axios.post('/api/bonus-battles', {
      title: title.value,
      stake: stake.value,
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

const fetchActiveBattle = async () => {
  try {
    const response = await axios.get('/api/bonus-battles/active');
    totalBattles.value = response.data.total_battles;
    activeBattle.value = response.data.battle;
    activeConcurrents.value = response.data.concurrents;
    activeStage.value = response.data.stage;
    activeBracket.value = response.data.bracket;
    activeScores.value = response.data.scores;
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

  } catch (error) {
    console.error('Failed to fetch active bonus battle:', error.message);
  }
};

onMounted(() => {
  fetchActiveBattle();
});
</script>

<template>
  <AppLayout title="Bonus Battle">
    <div class="py-2">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Hide form if there's an active battle -->
        <div v-if="!activeBattle && !winner" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 space-y-4">
            <!-- Input Form -->
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
              <label class="block text-sm font-medium text-gray-700">Participanți</label>
              <div v-for="(concurrent, index) in concurrents" :key="index" class="flex space-x-2 mt-1">
                <div class="border-solid border-black border-[1px] flex gap-2 px-2 py-2 rounded-md">
                  <input
                      type="text"
                      v-model="concurrent.name"
                      class="block w-full border-gray-300 rounded-md shadow-sm"
                      placeholder="Nume Joc"
                  />
                  <input
                      type="text"
                      v-model="concurrent.for_user"
                      class="block w-full border-gray-300 rounded-md shadow-sm"
                      placeholder="Cine a ales (opțional)?"
                  />
                </div>
              </div>
              <div class="flex gap-2 mt-2">
                <button type="button" @click="addConcurrent"
                        class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm">Adaugă Participanți
                </button>
                <button type="button" @click="removeConcurrent"
                        class="bg-red-600 text-white px-4 py-2 rounded-md text-sm">ȘTERGE
                </button>
              </div>
            </div>
            <button type="button" @click="startBattle" class="mt-4 bg-green-600 text-white px-4 py-2 rounded-md">Start
            </button>
          </div>
        </div>
        <div v-else-if="winner" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Câștigător! - {{ winner.name }}</h2>
          </div>
        </div>

        <!-- Active Battle Display -->
        <div v-if="activeBattle" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
          <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Active Battle: {{ activeBattle.title }}</h2>
            <p>Stake: {{ activeBattle.stake }} | Etapa: {{ activeStage.name }}</p>


            <!-- Brackets Section -->
            <div class="grid grid-cols-2 gap-4 mt-4" v-if="currentPair.length > 1">
              <div v-for="(concurrent, index) in currentPair" :key="index" class="border rounded-md p-4 shadow">
                <h3 class="text-lg font-bold mb-2">{{ concurrent.name }}</h3>
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
                          class="block w-20 border-gray-300 rounded-md shadow-sm disabled"
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
              <p class="mt-4 text-lg font-bold">Câștigător: {{ winner.name }}</p>
            </div>

            <button
                v-if="activeStage.name !== 'Final'"
                type="button"
                @click="finishRound"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
            >
              Următoarii Concurenți
            </button>
            <button
                v-if="activeStage.name === 'Final'"
                type="button"
                @click="endBattle"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
            >
              Termina Battle
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
