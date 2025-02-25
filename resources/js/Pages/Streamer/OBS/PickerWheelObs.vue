<template>
  <div id="game" v-if="games.length > 0">
    <div :class="['wheel-container', { centered: isSpinning }]">
      <div class="wheel" :class="{ idle: !isSpinning }" ref="wheel">
        <img class="center-image" :src="centerImageUrl" alt="center image" />
        <div
            v-for="(game, index) in games"
            :key="index"
            class="slice-label"
            :data-index="index"
            :style="getLabelStyle(index)"
        >
          {{ game }}
        </div>
      </div>
      <div class="selector-arrow">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
          <path d="M12 21l-12-18h24z" />
        </svg>
      </div>
    </div>
    <div v-if="showSelectedGame" class="selected-game">{{ selectedGame }}</div>
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
      selectedGame: null,
      spinDuration: 4000,
      showSelectedGame: false,
      centerImageUrl: "",
      previousEndDegree: 0,
      animation: null,
      newEndDegree: 0,
    };
  },
  async mounted() {
    await this.getList();
    await this.getProfilePicture();
    this.pollForSpin();
  },
  methods: {
    async getList() {
      try {
        const response = await axios.get(`/api/wheel-list/${this.id}`);
        let tempGames = response.data
            .split('\n')
            .map(game => game.trim())
            .filter(game => game);
        if (JSON.stringify(tempGames) !== JSON.stringify(this.games)) {
          this.games = tempGames;
        }
      } catch (error) {
        console.log("Error checking list:", error);
      }
    },
    pollForSpin() {
      setInterval(() => {
        axios.get(`/api/spin/check/${this.id}`)
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
      }, 3000); // Adjust polling interval as needed
    },
    async getProfilePicture() {
      await axios.get(`/api/user/profile-picture/${this.id}`)
          .then((response) => {
            this.centerImageUrl = response.data.profile_picture;
          })
          .catch((error) => {
            console.log("Error getting user picture", error);
          });
    },
    getLabelStyle(index) {
      const numSlices = this.games.length;
      const sliceAngle = 360 / numSlices;
      const backgroundColor = this.colors[index % 2];
      const width = 250; // fixed width in px

      const height = width * (2 * Math.tan(Math.PI / numSlices));

      return {
        'align-content': 'center',
        'clip-path': 'polygon(0% 0, 100% 50%, 0% 100%)',
        display: 'grid',
        'font-size': '20px',
        'grid-area': '1 / -1',
        // Optionally remove unsupported aspect-ratio property:
        // 'aspect-ratio': `1/calc(2*tan(180deg/${numSlices}))`,
        'list-style': 'none',
        'padding-left': '1ch',
        transform: `rotate(${sliceAngle * (index - 1)}deg)`,
        'transform-origin': 'center right',
        'width': `${width}px`,
        'height': `${Math.round(height)}px`, // rounding to an integer value
        'background-color': backgroundColor
      };
    },
    spinWheel() {
      if (this.isSpinning) return;
      this.isSpinning = true;
      this.showSelectedGame = false;
      const wheelEl = this.$refs.wheel;
      if (this.animation) {
        this.animation.cancel();
      }
      const randomAdditionalDegrees = Math.random() * 360 + 1800;
      this.newEndDegree = this.previousEndDegree + randomAdditionalDegrees;
      this.animation = wheelEl.animate([
        { transform: `rotate(${this.previousEndDegree}deg)` },
        { transform: `rotate(${this.newEndDegree}deg)` }
      ], {
        duration: this.spinDuration,
        easing: 'cubic-bezier(0.440, -0.205, 0.000, 1.130)',
        fill: 'forwards'
      });
      this.animation.onfinish = () => {
        wheelEl.style.transform = '';
        const arrowEl = document.querySelector('.selector-arrow');
        const arrowRect = arrowEl.getBoundingClientRect();
        const cx = arrowRect.left + arrowRect.width / 2;
        const cy = arrowRect.top + arrowRect.height / 2;
        const elements = document.elementsFromPoint(cx, cy);
        const sliceEl = elements.find(el => el.classList && el.classList.contains('slice-label'));
        if (sliceEl) {
          const sliceIndex = Number(sliceEl.getAttribute('data-index'));
          this.selectedGame = this.games[sliceIndex];
          this.showSelectedGame = true;
        } else {
          console.warn("No slice element found at the arrow position");
        }
        this.previousEndDegree = this.newEndDegree;
        setTimeout(() => {
          this.animation.cancel();
          this.isSpinning = false;
          this.showSelectedGame = false;
        }, 3000);
      };
    },
  },
};
</script>

<style lang="scss">
html,
body,
#app {
  background-color: transparent !important;
  background: unset !important;
  overflow: hidden;
}

.footer-donate {
  display: none !important;
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

.wheel-container {
  position: absolute;
  bottom: -200px;
  right: -120px;
  width: 500px;
  height: 500px;
  box-shadow: 0 0 20px 20px #0000002b;
  transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
  transform: rotate3d(1, -1, 0, 54deg);
  border-radius: 50%;

  .wheel {
    all: unset;
    aspect-ratio: 1 / 1;
    container-type: inline-size;
    direction: ltr;
    display: grid;
    place-content: center start;
    width: 500px;
    height: 500px;
    border-radius: 50%;
    position: relative;
    transition: transform 2s cubic-bezier(0.25, 0.1, 0.25, 1);
    clip-path: inset(0 0 0 0 round 50%);
    perspective: 800px;
    &.idle {
      animation: idle-spin 20s linear infinite;
    }

    .center-image {
      border: 2px solid black;
      position: absolute;
      top: 50%;
      left: 50%;
      width: 100px;
      height: 100px;
      border-radius: 50%;
      box-shadow: 0 0 20px 20px #000000a6;
      z-index: 10;
      transform: translate(-50%, -50%) translateZ(50px);
    }

    .slice-label {
      font-size: 14px;
    }
  }

  &.centered {
    position: fixed;
    bottom: 20%;
    right: 60%;
    transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
    animation: none;
    transform: rotate3d(1, -1, 1, 0deg);

    .wheel {
      transition: all 0.5s cubic-bezier(0.74, 0.2, 0.36, 1.09);
      transform: rotate3d(1, -1, 1, 0deg);
    }
  }

  .selector-arrow {
    position: absolute;
    top: -4px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 10;
  }
}

.selected-game {
  margin-top: 20px;
  text-align: center;
  font-size: 1.5em;
  background: #333;
  color: #fff;
  padding: 10px;
  border-radius: 5px;
}

@keyframes idle-spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>
