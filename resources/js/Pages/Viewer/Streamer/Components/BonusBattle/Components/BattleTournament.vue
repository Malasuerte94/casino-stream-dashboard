<template>
  <div class="relative h-[50vh] overflow-hidden"
       @mousedown="startDrag"
       @mousemove="onDrag"
       @mouseup="endDrag"
       @mouseleave="endDrag"
       @wheel="zoom">
    <div class="transform origin-top-left"
         :style="{ transform: `scale(${scale}) translate(${offsetX}px, ${offsetY}px)` }"
         ref="tournamentContainer">
      <div class="flex flex-col items-center gap-8">
        <!-- Each stage is now w-auto so it only occupies as much width as needed -->
        <div v-for="(stage, stageIndex) in fixedStages" :key="stage.id" class="w-auto">
          <h3 class="text-lg font-bold mb-4 text-center">{{ stage.name }}</h3>
          <!-- Wrap the grid in a flex container to center it -->
          <div class="flex justify-center">
            <div :style="{ gridTemplateColumns: `repeat(${stage.brackets.length}, auto)` }"
                 class="grid gap-4">
              <!-- Each bracket in the stage -->
              <div v-for="(bracket, bracketIndex) in stage.brackets" :key="bracket.id"
                   class="flex flex-col items-center relative">
                <div class="flex items-center justify-center gap-2
                            backdrop-blur-xl bg-white/10 shadow-lg shadow-black/40
                            border border-white/20 p-2 rounded-lg">
                  <!-- Participant A -->
                  <div class="text-center relative"
                       :class="{ 'font-bold text-green-500': bracket.winner && bracket.winner.id === bracket.participantA.id }">
                    <div class="flex flex-col justify-center items-center">
                      <div class="text-sm font-bold">{{ bracket.participantA.game }}</div>
                      <div>
                        <img :src="getGameThumbnail(bracket.participantA.full_game.image)"
                             alt="GAME THUMB" class="h-24 rounded-lg">
                      </div>
                    </div>
                    <table class="w-full mt-2 text-xs bg-white/10 rounded-md overflow-hidden"
                           :class="{ 'bg-white/80': bracket.winner && bracket.winner.id === bracket.participantA.id }">
                      <thead>
                      <tr class="bg-white/20">
                        <th class="border-r border-white p-1">COST</th>
                        <th class="border-r border-white p-1">RESULT</th>
                        <th class="p-1">SCORE</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(score, index) in getScores(bracket.scores, bracket.participantA.id)"
                          :key="'a-score-' + index">
                        <td class="border-r border-white p-1">{{ score.cost }}</td>
                        <td class="border-r border-white p-1">{{ score.result }}</td>
                        <td class="p-1">{{ parseFloat(score.score).toFixed(2) }}</td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                  <div class="font-bold text-orange-500">VS</div>
                  <!-- Participant B -->
                  <div class="text-center relative"
                       :class="{ 'font-bold text-green-500': bracket.winner && bracket.winner.id === bracket.participantB.id }">
                    <div class="flex flex-col justify-center items-center">
                      <div class="text-sm font-bold">{{ bracket.participantB.game }}</div>
                      <div>
                        <img :src="getGameThumbnail(bracket.participantB.full_game.image)"
                             alt="GAME THUMB" class="h-24 rounded-lg">
                      </div>
                    </div>
                    <table class="w-full mt-2 text-xs bg-white/10 rounded-md overflow-hidden"
                           :class="{ 'bg-white/80': bracket.winner && bracket.winner.id === bracket.participantB.id }">
                      <thead>
                      <tr class="bg-white/20">
                        <th class="border-r border-white p-1">COST</th>
                        <th class="border-r border-white p-1">RESULT</th>
                        <th class="p-1">SCORE</th>
                      </tr>
                      </thead>
                      <tbody>
                      <tr v-for="(score, index) in getScores(bracket.scores, bracket.participantB.id)"
                          :key="'b-score-' + index">
                        <td class="border-r border-white p-1">{{ score.cost }}</td>
                        <td class="border-r border-white p-1">{{ score.result }}</td>
                        <td class="p-1">{{ parseFloat(score.score).toFixed(2) }}</td>
                      </tr>
                      </tbody>
                    </table>
                  </div>
                </div>
                <!-- Display the bracket winner -->
                <div v-if="bracket.winner" class="mt-2 text-center text-green-600 font-bold text-xs">
                  Winner: {{ bracket.winner.game }}
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- Final Winner (computed from final stage) -->
        <div v-if="finalWinner" class="mt-8 text-center font-bold text-xl text-yellow-500">
          🏆 Winner: {{ finalWinner }} 🏆
        </div>
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
    },
    getGameThumbnail(imageName) {
      return imageName ? `/storage/games/${imageName}` : '';
    }
  },
  computed: {
    fixedStages() {
      // Deep clone stages to avoid mutating the prop
      const stages = JSON.parse(JSON.stringify(this.latestBattle.stages));

      // Example logic: for a two-stage tournament, ensure stage 2's bracket has the expected order.
      if (stages.length > 1 && stages[0].brackets && stages[0].brackets.length >= 2) {
        const expectedTopWinner = stages[0].brackets[0].winner;
        if (stages[1].brackets && stages[1].brackets.length > 0) {
          const stage2Bracket = stages[1].brackets[0];
          // Swap participants if the top slot isn't the expected top winner.
          if (expectedTopWinner && stage2Bracket.participantA.id !== expectedTopWinner.id) {
            const temp = stage2Bracket.participantA;
            stage2Bracket.participantA = stage2Bracket.participantB;
            stage2Bracket.participantB = temp;
          }
        }
      }

      return stages;
    },
    finalWinner() {
      const stages = this.fixedStages;
      if (stages && stages.length) {
        const finalStage = stages[stages.length - 1];
        if (finalStage.brackets && finalStage.brackets.length) {
          return finalStage.brackets[0].winner ? finalStage.brackets[0].winner.game : null;
        }
      }
      return null;
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
