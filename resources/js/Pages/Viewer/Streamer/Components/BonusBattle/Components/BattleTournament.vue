<template>
  <div class="relative w-full h-full overflow-hidden" @mousedown="startDrag" @mousemove="onDrag" @mouseup="endDrag" @mouseleave="endDrag" @wheel="zoom">
    <div class="transform origin-top-left" :style="{ transform: `scale(${scale}) translate(${offsetX}px, ${offsetY}px)` }" ref="tournamentContainer">
      <div class="flex flex-col items-center gap-4">
        <div v-for="(stage, stageIndex) in latestBattle.stages" :key="stage.id" class="flex flex-col items-center relative">
          <h3 class="text-lg font-bold mb-2">{{ stage.name }}</h3>
          <div class="flex gap-4 relative">
            <div v-for="(bracket, bracketIndex) in stage.brackets" :key="bracket.id" class="flex flex-col items-center relative">
              <div class="flex items-center justify-center gap-2 backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40 border border-white/20 p-2 rounded-lg">
                <!-- Participant A -->
                <div class="text-center relative" :class="{ 'font-bold text-green-500': bracket.winner.id === bracket.participantA.id }">
                  <div class="text-sm font-bold">{{ bracket.participantA.game }}</div>
                  <table class="w-full mt-2 text-xs bg-white/10 rounded-md overflow-hidden" :class="{ 'bg-white/80': bracket.winner.id === bracket.participantA.id }">
                    <thead>
                    <tr class="bg-white/20">
                      <th class="border-r border-white p-1">COST</th>
                      <th class="border-r border-white p-1">RESULT</th>
                      <th class="p-1">SCORE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(score, index) in getScores(bracket.scores, bracket.participantA.id)" :key="'a-score-' + index">
                      <td class="border-r border-white p-1">{{ score.cost }}</td>
                      <td class="border-r border-white p-1">{{ score.result }}</td>
                      <td class="p-1">{{ parseFloat(score.score).toFixed(2) }}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
                <div class="font-bold text-orange-500">VS</div>
                <!-- Participant B -->
                <div class="text-center relative" :class="{ 'font-bold text-green-500': bracket.winner.id === bracket.participantB.id }">
                  <div class="text-sm font-bold">{{ bracket.participantB.game }}</div>
                  <table class="w-full mt-2 text-xs bg-white/10 rounded-md overflow-hidden" :class="{ 'bg-white/80': bracket.winner.id === bracket.participantB.id }">
                    <thead>
                    <tr class="bg-white/20">
                      <th class="border-r border-white p-1">COST</th>
                      <th class="border-r border-white p-1">RESULT</th>
                      <th class="p-1">SCORE</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="(score, index) in getScores(bracket.scores, bracket.participantB.id)" :key="'b-score-' + index">
                      <td class="border-r border-white p-1">{{ score.cost }}</td>
                      <td class="border-r border-white p-1">{{ score.result }}</td>
                      <td class="p-1">{{ parseFloat(score.score).toFixed(2) }}</td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-if="latestBattle.summary.winning_game !== 'N/A'" class="mt-8 text-center font-bold text-xl text-yellow-500">🏆 Winner: {{ latestBattle.summary.winning_game }} 🏆</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    latestBattle: {
      type: Object,
      required: true
    }
  },
  data() {
    return {
      isDragging: false,
      startX: 0,
      startY: 0,
      offsetX: 0,
      offsetY: 0,
      scale: 1
    };
  },
  methods: {
    getScores(scores, participantId) {
      return scores.filter(score => score.participant_id === participantId);
    },
    startDrag(event) {
      this.isDragging = true;
      this.startX = event.clientX - this.offsetX;
      this.startY = event.clientY - this.offsetY;
    },
    onDrag(event) {
      if (!this.isDragging) return;
      this.offsetX = event.clientX - this.startX;
      this.offsetY = event.clientY - this.startY;
    },
    endDrag() {
      this.isDragging = false;
    },
    zoom(event) {
      event.preventDefault();
      const scaleAmount = event.deltaY * -0.001;
      this.scale = Math.min(Math.max(this.scale + scaleAmount, 0.5), 2);
    }
  }
};
</script>

<style scoped>
.relative {
  cursor: grab;
  user-select: none;
}
</style>