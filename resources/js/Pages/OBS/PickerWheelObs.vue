<template>
  <div id="game" v-if="games.length > 0">
    <div :class="['wheel-container', { centered: isSpinning }]" @transitionend="handleTransitionEnd">
      <div class="box-transform">
        <div class="selector-arrow">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 21l-12-18h24z"/></svg>
        </div>
        <img class="logo" src="https://yt3.googleusercontent.com/CfIgnRwE7ROK9CugvIS1PQOjiOUfMyruXwnHwE3FmRnHu7Utw-RjZKldzKONjXFSTUl8zCfX=s160-c-k-c0x00ffffff-no-rj" alt=""/>
        <canvas :class="[{ idle: !isSpinning && !showSelectedGame }]" ref="wheelCanvas" :width="canvasSize" :height="canvasSize"></canvas>
        <div v-if="showSelectedGame" class="selected-game">{{ selectedGame }}</div>
      </div>
    </div>
  </div>
  <div v-else class="text-center text-gray-500">No games found</div>
</template>

<script>
export default {
  props: ["id"],
  data() {
    return {
      games: [],
      colors: ['#f48a00', '#022522'],
      isSpinning: false,
      selectedAngle: 0,
      selectedGame: null,
      spinDuration: 2000,
      showSelectedGame: false,
      canvasSize: 500,
      returnToCornerTimeout: null, // Timeout for returning to corner
    };
  },
  async mounted() {
    await this.getList();
    this.pollForSpin();
  },
  methods: {
    async getList() {
      try {
        const response = await axios.get(`/api/wheel-list/${this.id}`);
        let tempGames = response.data.split('\n').map(game => game.trim()).filter(game => game);
        if (JSON.stringify(tempGames) !== JSON.stringify(this.games)) {
          this.games = tempGames;
          await this.$nextTick(() => {
            this.drawWheel();
          });
        }
      } catch (error) {
        console.log("Error checking list:", error);
      }
    },
    pollForSpin() {
      setInterval(() => {
        axios
            .get(`/api/spin/check/${this.id}`)
            .then((response) => {
              this.getList();
              if (response.data.shouldSpin) {
                this.spinWheel();
                axios.post(`/api/spin/clear/${this.id}`).catch((error) => {
                  console.log("Error clearing spin trigger:", error);
                });
              }
            })
            .catch((error) => {
              console.log("Error checking spin trigger:", error);
            });
      }, 3000); // Poll every 3 seconds or adjust as needed
    },
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

        // Set slice fill color
        ctx.fillStyle = this.colors[i % this.colors.length];

        // Draw slice with border
        ctx.beginPath();
        ctx.moveTo(radius, radius);
        ctx.arc(radius, radius, radius, startAngle, endAngle);
        ctx.closePath();
        ctx.fill();

        // Set border color and width
        ctx.lineWidth = 1;
        ctx.strokeStyle = "#000";
        ctx.stroke();

        // Draw text with shadow
        ctx.save();
        ctx.translate(radius, radius);
        ctx.rotate(startAngle + (sliceAngle / 2));
        ctx.textAlign = "right";
        ctx.fillStyle = "#fff";
        ctx.font = "bold 14px Arial";

        // Text shadow settings
        ctx.shadowColor = "rgba(0, 0, 0, 0.5)";
        ctx.shadowBlur = 4;
        ctx.shadowOffsetX = 2;
        ctx.shadowOffsetY = 2;

        ctx.fillText(this.games[i], radius - 5, 0);
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

      const extraSpins = 5;
      const targetAngle = (extraSpins * 360) + (360 - (winningIndex * sliceAngle)) - (sliceAngle / 2);

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
          this.showSelectedGame = true;
          this.returnToCornerTimeout = setTimeout(() => {
            this.isSpinning = false;
            this.showSelectedGame = false;
          }, 3000);
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
    },
    handleTransitionEnd() {
      // Reset wheel position and stop spinning after the return animation
      if (!this.isSpinning && this.returnToCornerTimeout) {
        clearTimeout(this.returnToCornerTimeout);
        this.returnToCornerTimeout = null;
        // Ensure the idle animation resumes when returning to the corner
        this.showSelectedGame = false;
      }
    }
  },
};
</script>

<style>
html,
body,
#app {
  background-color: transparent !important;
  overflow: hidden;
}
.footer-donate {
  display: none;
}
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
  position: absolute;
  bottom: -200px;
  right: -120px;
  width: 500px;
  height: 500px;
  transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
}

.wheel-container.centered {
  position: fixed;
  bottom: 20%;
  right: 60%;
  transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
  animation: none;
}

.box-transform {
  border-radius: 50%;
  box-shadow: 8px 9px 0 3px black;
  transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
  transform: rotate3d(1, -1, 0, 54deg);
}

.wheel-container.centered .box-transform {
  transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
  transform: rotate3d(1, -1, 1, 0deg);
}

.logo {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 50%;
  z-index: 1;
  transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
  box-shadow: 0 0 20px 17px #cf2650;
}

@keyframes idle-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

canvas {
  border: 4px solid #000000;
  box-shadow: 0 0 15px rgba(0, 0, 0, 0.5);
  overflow: visible;
  border-radius: 50%;
}
canvas.idle {
    animation: idle-spin 20s linear infinite;
}

.selector-arrow {
  position: absolute;
  top: -1px;
  z-index: 10;
  scale: 1.2;
  left: 0;
  right: 0;
  margin: auto;
  display: flex;
  justify-content: center;
  fill: #ff0000;
  stroke: #000000;
}

.selected-game {
  padding: 10px 20px;
  background-color: #333;
  color: #fff;
  font-size: 1.5em;
  border-radius: 5px;
  box-shadow: -1px 20px 12px 3px rgb(0 0 0 / 35%);
  position: absolute;
  top: 72%;
  margin: auto;
  left: 0;
  right: 0;
  display: flex;
  width: 50%;
  height: 18%;
  align-items: center;
  justify-content: center;
}
</style>
