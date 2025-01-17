<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';

const title = ref('Bonus Battle #1');
const stake = ref('');
const concurrents = ref([]);

const activeBattle = ref(null);
const activeConcurrents = ref(null);
const activeStage = ref(null);
const winner = ref(null);
const currentPair = ref([]);

const addConcurrent = () => {
  concurrents.value.push({ name: '' });
  concurrents.value.push({ name: '' });
};

const removeConcurrent = (index) => {
  concurrents.value.splice(index, 2);
};

const addScore = (concurrentIndex) => {
  currentPair.value[concurrentIndex].scores.push({
    amount: 0,
    result: 0,
    score: 0,
    confirmed: false,
  });
};

const confirmScore = (concurrentIndex, scoreIndex) => {
  const scoreEntry = currentPair.value[concurrentIndex].scores[scoreIndex];
  if (scoreEntry.amount > 0) {
    scoreEntry.score = parseFloat((scoreEntry.result / scoreEntry.amount).toFixed(2));
    scoreEntry.confirmed = true;
  } else {
    alert('Amount must be greater than 0.');
  }
};

const removeScore = (concurrentIndex, scoreIndex) => {
  currentPair.value[concurrentIndex].scores.splice(scoreIndex, 1);
};

const calculateTotalScore = (scores) => {
  return scores.reduce((total, score) => total + (score.confirmed ? score.score : 0), 0).toFixed(2);
};

const generateBrackets = async () => {
  if (!title.value || !stake.value || concurrents.value.some(c => !c.name)) {
    alert('All fields must be filled to start.');
    return;
  }

  try {
    // API call to store bonus battle
    const response = await axios.post('/api/bonus-battles', {
      title: title.value,
      stake: stake.value,
      concurrents: concurrents.value,
    });

    console.log('Bonus battle stored successfully:', response.data);

    fetchActiveBattle();

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
    await fetchActiveBattle();
  } catch (error) {
    console.log('endBattle error:', error.response?.data || error.message);
  }
};

const finishRound = async () => {
  try {
    const scoresData = currentPair.value.map(concurrent => ({
      concurrent_id: concurrent.id,
      total_score: calculateTotalScore(concurrent.scores),
    }));

    await axios.post(`/api/bonus-battles/finish-round`, {
      scores: scoresData,
      active_stage: activeStage.value.id,
      active_battle: activeBattle.value.id
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
    console.log(response.data)
    activeBattle.value = response.data.battle;
    activeConcurrents.value = response.data.concurrents;
    activeStage.value = response.data.stage;
    winner.value = response.data.winner;

    if (activeConcurrents.value) {
      const concurrentsList = activeConcurrents.value;
      currentPair.value = concurrentsList.slice(0, 2).map(concurrent => ({
        ...concurrent,
        scores: [],
      }));
    }

    if (activeStage.value) {
      const currentActiveStage = activeStage.value.find(stage => stage.active === 1);
      if (currentActiveStage) {
        activeStage.value = currentActiveStage;
      } else {
        console.warn('No active stage found!');
      }
    }
  } catch (error) {
    console.error('Failed to fetch active bonus battle:', error.response?.data || error.message);
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
        <div v-if="!activeBattle" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6 space-y-4">
            <!-- Input Form -->
            <div>
              <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
              <input type="text" v-model="title" id="title" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            </div>
            <div>
              <label for="stake" class="block text-sm font-medium text-gray-700">Stake</label>
              <input type="number" v-model="stake" id="stake" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Concurrents</label>
              <div v-for="(concurrent, index) in concurrents" :key="index" class="flex space-x-2 mt-1">
                <input
                    type="text"
                    v-model="concurrent.name"
                    class="block w-full border-gray-300 rounded-md shadow-sm"
                    placeholder="Enter name"
                />
                <button type="button" @click="removeConcurrent(index)" class="text-red-600">Remove</button>
              </div>
              <button type="button" @click="addConcurrent" class="mt-2 text-blue-600">Add Concurrent</button>
            </div>
            <button type="button" @click="generateBrackets" class="mt-4 bg-blue-600 text-white px-4 py-2 rounded-md">Start</button>
          </div>
        </div>

        <!-- Active Battle Display -->
        <div v-if="activeBattle" class="bg-white overflow-hidden shadow-xl sm:rounded-lg mt-6">
          <div class="p-6">
            <h2 class="text-xl font-bold mb-4">Active Battle: {{ activeBattle.title }}</h2>
            <p>Stake: {{ activeBattle.stake }} | Current Stage: {{ activeStage.name }}</p>


            <!-- Brackets Section -->
            <div class="grid grid-cols-2 gap-4 mt-4" v-if="currentPair.length > 1">
              <div v-for="(concurrent, index) in currentPair" :key="index" class="border rounded-md p-4 shadow">
                <h3 class="text-lg font-bold mb-2">{{ concurrent.name }}</h3>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Scores</label>
                  <div v-for="(score, scoreIndex) in concurrent.scores" :key="scoreIndex" class="flex items-center space-x-2 mt-1">
                    <input
                        type="number"
                        v-model="concurrent.scores[scoreIndex].amount"
                        class="block w-50 border-gray-300 rounded-md shadow-sm"
                        placeholder="Amount"
                    />
                    <input
                        type="number"
                        v-model="concurrent.scores[scoreIndex].result"
                        class="block w-50 border-gray-300 rounded-md shadow-sm"
                        placeholder="Result"
                        @input="confirmScore(index, scoreIndex)"
                    />
                    <button
                        type="button"
                        @click="removeScore(index, scoreIndex)"
                        class="text-red-600"
                    >
                      X
                    </button>
                  </div>
                  <button
                      type="button"
                      @click="addScore(index)"
                      class="mt-2 text-blue-600"
                  >
                    Add Score
                  </button>
                </div>
                <p class="mt-4 text-lg font-bold">
                  Total Score: {{ calculateTotalScore(concurrent.scores) }}
                </p>
              </div>
            </div>
            <div v-if="winner">
              <p class="mt-4 text-lg font-bold">Winner: {{ winner.name }}</p>
            </div>

            <button
                v-if="!winner"
                type="button"
                @click="finishRound"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
            >
              Finish Round
            </button>
            <button
                v-if="winner"
                type="button"
                @click="endBattle"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
            >
              End Battle
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
