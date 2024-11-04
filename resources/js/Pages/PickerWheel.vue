<template>
  <div id="game">
    <button @click="spinWheel" class="spin-button" :disabled="isSpinning">Spin</button>
    <div class="wheel-container">
      <canvas ref="wheelCanvas" :width="canvasSize" :height="canvasSize"></canvas>
      <div class="selector-arrow"></div>
    </div>
    <div v-if="showSelectedGame" class="selected-game">{{ selectedGame }}</div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      games: [
        "Game 1", "Game 2", "Game 3", "Game 4", "Game 5",
        "Game 6", "Game 7", "Game 8"
      ],
      colors: ['#FF6B6B', '#4ECDC4', '#FFD93D', '#95E1D3', '#A8E6CF'],
      isSpinning: false,
      selectedAngle: 0,
      selectedGame: null,
      spinDuration: 4000,
      showSelectedGame: false,
      canvasSize: 500,
    };
  },
  mounted() {
    this.drawWheel();
  },
  methods: {
    drawWheel() {
      const canvas = this.$refs.wheelCanvas;
      const ctx = canvas.getContext("2d");
      const numSlices = this.games.length;
      const sliceAngle = (2 * Math.PI) / numSlices;
      const radius = canvas.width / 2;

      ctx.clearRect(0, 0, canvas.width, canvas.height);

      for (let i = 0; i < numSlices; i++) {
        const startAngle = (i * sliceAngle) - (Math.PI / 2);
        const endAngle = startAngle + sliceAngle;

        ctx.fillStyle = this.colors[i % this.colors.length];

        ctx.beginPath();
        ctx.moveTo(radius, radius);
        ctx.arc(radius, radius, radius, startAngle, endAngle);
        ctx.closePath();
        ctx.fill();

        // Draw text
        ctx.save();
        ctx.translate(radius, radius);
        ctx.rotate(startAngle + (sliceAngle / 2));
        ctx.textAlign = "right";
        ctx.fillStyle = "#fff";
        ctx.font = "bold 18px Arial";
        ctx.fillText(this.games[i], radius - 30, 0);
        ctx.restore();
      }
    },
    spinWheel() {
      this.isSpinning = true;
      this.showSelectedGame = false;

      const numSlices = this.games.length;
      const sliceAngle = 360 / numSlices;

      // Randomly choose the winning slice index
      const winningIndex = Math.floor(Math.random() * numSlices);
      this.selectedGame = this.games[winningIndex];

      // Calculate the target angle to land in the middle of the slice
      const extraSpins = 5;
      // No additional offset needed since we want to align with the middle of the slice
      const targetAngle = (extraSpins * 360) + (360 - (winningIndex * sliceAngle));

      // Start the animation
      const startTime = performance.now();
      const duration = this.spinDuration;

      const animate = (currentTime) => {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);

        // Cubic easing out for smooth deceleration
        const easedProgress = 1 - Math.pow(1 - progress, 3);
        this.selectedAngle = easedProgress * targetAngle;

        this.drawRotatedWheel();

        if (progress < 1) {
          requestAnimationFrame(animate);
        } else {
          this.isSpinning = false;
          this.showSelectedGame = true;
        }
      };

      requestAnimationFrame(animate);
    },
    drawRotatedWheel() {
      const canvas = this.$refs.wheelCanvas;
      const ctx = canvas.getContext("2d");

      ctx.clearRect(0, 0, canvas.width, canvas.height);
      ctx.save();
      ctx.translate(canvas.width / 2, canvas.height / 2);
      ctx.rotate((this.selectedAngle * Math.PI) / 180);
      ctx.translate(-canvas.width / 2, -canvas.height / 2);

      this.drawWheel();
      ctx.restore();
    }
  },
};
</script>

<style scoped>
#game {
  width: 100vw;
  height: 100vh;
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-direction: column;
}

.spin-button {
  margin-bottom: 20px;
  padding: 10px 20px;
  font-size: 1.2em;
}

.wheel-container {
  position: relative;
  width: 500px;
  height: 500px;
}

.selector-arrow {
  position: absolute;
  top: -20px;
  left: 50%;
  transform: translateX(-50%);
  width: 0;
  height: 0;
  border-left: 10px solid transparent;
  border-right: 10px solid transparent;
  border-bottom: 20px solid red;
  z-index: 10;
}

.selected-game {
  margin-top: 20px;
  padding: 10px 20px;
  background-color: #333;
  color: #fff;
  font-size: 1.5em;
  border-radius: 5px;
}
</style>