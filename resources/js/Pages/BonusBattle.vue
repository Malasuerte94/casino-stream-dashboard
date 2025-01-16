<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref, onMounted } from 'vue';

const title = ref('Bonus Battle #1');
const stake = ref('');
const concurrents = ref([{ name: '' }]);
const brackets = ref([]);
const showBrackets = ref(false);
const activeBattle = ref(null);
const currentPair = ref([]);

const addConcurent = () => {
  concurrents.value.push({ name: '' });
};

const removeConcurent = (index) => {
  concurrents.value.splice(index, 1);
};

const addScore = (concurrentIndex) => {
  currentPair.value[concurrentIndex].scores.push(0);
};

const removeScore = (concurrentIndex, scoreIndex) => {
  currentPair.value[concurrentIndex].scores.splice(scoreIndex, 1);
};

const calculateTotalScore = (scores) => {
  return scores.reduce((total, score) => total + (parseInt(score) || 0), 0);
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

    // Generate brackets locally for display
    const pairs = [];
    for (let i = 0; i < concurrents.value.length; i += 2) {
      if (concurrents.value[i + 1]) {
        pairs.push([concurrents.value[i].name, concurrents.value[i + 1].name]);
      } else {
        pairs.push([concurrents.value[i].name, 'BYE']);
      }
    }

    brackets.value = pairs;
    showBrackets.value = true;

  } catch (error) {
    console.error('Failed to store bonus battle:', error.response?.data || error.message);
    alert('An error occurred while saving the bonus battle. Please try again.');
  }
};

const finishRound = async () => {
  try {
    const scoresData = currentPair.value.map(concurrent => ({
      concurrent_id: concurrent.id,
      total_score: calculateTotalScore(concurrent.scores),
    }));

    await axios.post(`/api/bonus-battles/${activeBattle.value.id}/finish-round`, {
      scores: scoresData,
    });

    // Fetch the next pair of concurrents
    const nextPairIndex = activeBattle.value.concurrents.indexOf(currentPair.value[0]) + 2;
    const nextPair = activeBattle.value.concurrents.slice(nextPairIndex, nextPairIndex + 2);

    if (nextPair.length > 0) {
      currentPair.value = nextPair.map(concurrent => ({
        ...concurrent,
        scores: [],
      }));
    } else {
      alert('Stage finished!');
      currentPair.value = [];
    }
  } catch (error) {
    console.error('Failed to finish round:', error.response?.data || error.message);
    alert('An error occurred while finishing the round. Please try again.');
  }
};

const fetchActiveBattle = async () => {
  try {
    const response = await axios.get('/api/bonus-battles/active');
    activeBattle.value = response.data.battle;

    if (activeBattle.value) {
      // Initialize the first pair of concurrents
      const concurrentsList = activeBattle.value.concurrents;
      currentPair.value = concurrentsList.slice(0, 2).map(concurrent => ({
        ...concurrent,
        scores: [],
      }));
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
  <AppLayout title="Bonuses">
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
            <p>Stake: {{ activeBattle.stake }}</p>


            <div class="grid grid-cols-2 gap-4 mt-4">
              <div v-for="(concurrent, index) in currentPair" :key="index" class="border rounded-md p-4 shadow">
                <h3 class="text-lg font-bold mb-2">{{ concurrent.name }}</h3>
                <div>
                  <label class="block text-sm font-medium text-gray-700">Scores</label>
                  <div v-for="(score, scoreIndex) in concurrent.scores" :key="scoreIndex" class="flex space-x-2 mt-1">
                    <input
                        type="number"
                        v-model="concurrent.scores[scoreIndex]"
                        class="block w-full border-gray-300 rounded-md shadow-sm"
                        placeholder="Enter score"
                    />
                    <button
                        type="button"
                        @click="removeScore(index, scoreIndex)"
                        class="text-red-600"
                    >
                      Remove
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
                <p class="mt-4 text-lg font-bold">Total Score: {{ calculateTotalScore(concurrent.scores) }}</p>
              </div>
            </div>

            <button
                type="button"
                @click="finishRound"
                class="mt-6 bg-green-600 text-white px-4 py-2 rounded-md"
            >
              Finish Round
            </button>




          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
