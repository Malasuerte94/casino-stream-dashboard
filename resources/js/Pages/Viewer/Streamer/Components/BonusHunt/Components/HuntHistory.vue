<template>
  <div
      class="flex gap-2 overflow-x-auto overflow-y-visible scrollbar-hide cursor-grab active:cursor-grabbing select-none py-4"
      ref="scrollContainer">
    <div v-for="bonusHunt in bonusHuntHistory" @click="seeHunt(bonusHunt)"
         class="rounded-lg backdrop-blur-xl p-4 bg-white/10 shadow-lg shadow-black/40 border border-white/20
                w-[90%] md:w-1/4 flex flex-col gap-2 justify-between min-w-[250px] hover:scale-110 transition-all duration-300">
      <div class="flex flex-row justify-between items-center">
        <div class="text-sm">
          BONUS HUNT #{{ bonusHunt.id }}
        </div>
        <div class="bg-gray-800 px-2 py-1 rounded text-xs"
             :class="{ 'text-gray-500': bonusHunt.ended, 'text-green-500': !bonusHunt.ended }">
          {{ bonusHunt.ended ? 'TERMINAT' : 'ACTIV' }}
        </div>
      </div>
      <div class="flex flex-row justify-between items-center text-xl">
        <div class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded">
          <div>
            <SvgGame class="h-5 w-5 text-indigo-500 fill-current"/>
          </div>
          <div>{{ bonusHunt.bonus_hunt_games.length }}</div>
        </div>
        <div class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded">
          <div>
            <SvgMoney class="h-5 w-5 text-green-500"/>
          </div>
          <div>{{ bonusHunt.start }} {{ currency }}</div>
        </div>
      </div>
      <div class="flex flex-row items-center justify-between gap-1 text-sm">
        <div class="flex flex-row items-center gap-1">
          <div>
            <SvgCalendar class="w-4 h-4 text-gray-500 stroke-current"/>
          </div>
          <div>{{ convertDate(bonusHunt.created_at) }}</div>
        </div>
        <div
            class="flex flex-row items-center gap-1 bg-gray-700 px-2 py-1 rounded"
            :class="{
              'text-green-500': parseFloat(bonusHunt.result) >= parseFloat(bonusHunt.start),
              'text-red-500': parseFloat(bonusHunt.result) <= parseFloat(bonusHunt.start)
            }"
        >
          <div>
            <SvgMoney class="h-4 w-4"/>
          </div>
          <div>{{ bonusHunt.result }} {{ currency }}</div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import SvgCalendar from "/public/storage/assets/images/calendar.svg";
import SvgGame from "/public/storage/assets/images/slots.svg";
import SvgMoney from "/public/storage/assets/images/money.svg";
import SvgBackward from "/public/storage/assets/images/backward.svg";

export default {
  components: {
    SvgCalendar,
    SvgGame,
    SvgMoney,
    SvgBackward
  },
  props: {
    bonusHuntHistory: {
      type: Array,
      required: true
    },
    currency: {
      type: String,
      required: false,
      default: 'RON'
    }
  },
  async mounted() {
    this.initDragScroll();
  },
  methods: {
    initDragScroll() {
      const container = this.$refs.scrollContainer;
      let isDown = false;
      let startX;
      let scrollLeft;

      container.addEventListener("mousedown", (e) => {
        isDown = true;
        container.classList.add("active");
        startX = e.pageX - container.offsetLeft;
        scrollLeft = container.scrollLeft;
      });

      container.addEventListener("mouseleave", () => {
        isDown = false;
        container.classList.remove("active");
      });

      container.addEventListener("mouseup", () => {
        isDown = false;
        container.classList.remove("active");
      });

      container.addEventListener("mousemove", (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - container.offsetLeft;
        const walk = (x - startX) * 2; // Adjust speed factor if needed
        container.scrollLeft = scrollLeft - walk;
      });
    },
    convertDate(isoString) {
      const date = new Date(isoString);
      return new Intl.DateTimeFormat("ro-RO", {
        day: "2-digit",
        month: "short",
        year: "numeric",
      }).format(date);
    },
    seeHunt(bonusHunt) {
      this.$emit('seeHunt', bonusHunt);
    }
  },
};
</script>

<style scoped>
/* Hide scrollbar for Webkit-based browsers */
.scrollbar-hide::-webkit-scrollbar {
  display: none;
}

/* Hide scrollbar for Firefox */
.scrollbar-hide {
  scrollbar-width: none;
}
</style>