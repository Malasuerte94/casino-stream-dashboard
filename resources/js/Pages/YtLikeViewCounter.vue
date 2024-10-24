<template>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Lilita+One&family=Signika+Negative:wght@300..700&display=swap" rel="stylesheet">
  <div>
    <div class="stats-container">
      <div class="stat-item yt">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
        <span class="stat-number" :class="{ 'number-changed': youtubeData.views !== prevViews }">{{ youtubeData.views }}</span>
      </div>

      <div class="stat-item like">
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M23.873 9.81c.086-.251.127-.508.127-.763 0-.77-.379-1.514-1.055-1.982-2.152-1.492-1.868-1.117-2.68-3.544-.339-1.014-1.321-1.7-2.429-1.696-2.654.008-2.193.153-4.335-1.354-.446-.314-.974-.471-1.501-.471s-1.055.157-1.502.471c-2.154 1.515-1.687 1.362-4.335 1.354-1.107-.003-2.09.683-2.429 1.696-.812 2.433-.533 2.055-2.68 3.544-.675.469-1.054 1.212-1.054 1.982 0 .255.041.512.127.763.83 2.428.827 1.963 0 4.38-.086.251-.127.509-.127.763 0 .77.379 1.514 1.055 1.982 2.147 1.489 1.869 1.114 2.68 3.544.339 1.014 1.321 1.7 2.429 1.696 2.654-.009 2.193-.152 4.335 1.354.446.314.974.471 1.501.471s1.055-.157 1.502-.471c2.141-1.506 1.681-1.362 4.335-1.354 1.107.003 2.09-.683 2.429-1.696.812-2.428.528-2.053 2.68-3.544.675-.468 1.054-1.212 1.054-1.982 0-.254-.041-.512-.127-.763-.831-2.427-.827-1.963 0-4.38zm-7.565 1.984c.418.056.63.328.63.61 0 .323-.277.66-.844.705-.348.027-.434.312-.016.406.351.08.549.326.549.591 0 .314-.279.654-.913.771-.383.07-.421.445-.016.477.344.026.479.146.479.312 0 .466-.826 1.333-2.426 1.333-2.501.001-3.407-1.499-6.751-1.499v-4.964c1.766-.271 3.484-.817 4.344-3.802.239-.831.39-1.734 1.187-1.734 1.188 0 1.297 2.562.844 4.391.656.344 1.875.468 2.489.442.886-.036 1.136.409 1.136.745 0 .505-.416.675-.677.755-.304.094-.444.404-.015.461z"/></svg>
        <span class="stat-number" :class="{ 'number-changed': youtubeData.likes !== prevLikes }">{{ youtubeData.likes }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["id"],
  data() {
    return {
      loading: true,
      ytLink: [],
      youtubeData: { views: 0, likes: 0 },
      prevViews: 0,
      prevLikes: 0,
      intervalId: null,
      refresh: 120000
    };
  },
  async mounted() {
    await this.getYoutubeLink();
    await this.scrapYtData();
    this.loading = false;

    // Refresh data every 2 minutes (120000 milliseconds)
    this.intervalId = setInterval(async () => {
      await this.scrapYtData();
    }, this.refresh);
  },
  beforeDestroy() {
    // Clear the interval when the component is destroyed to avoid memory leaks
    if (this.intervalId) {
      clearInterval(this.intervalId);
    }
  },
  methods: {
    async getYoutubeLink() {
      await axios
          .get("/api/youtube-link/" + this.id)
          .then((response) => {
            this.ytLink = response.data.youtube;
            console.log(this.ytLink);
          })
          .catch((error) => {
            console.log(error);
          });
    },
    async scrapYtData() {
      this.error = null;

      try {
        const response = await axios.post("/api/get-youtube-data", {
          url: this.ytLink.url,
        });

        if (response.data.success) {
          // Store previous values to trigger animation
          this.prevViews = this.youtubeData.views;
          this.prevLikes = this.youtubeData.likes;

          // Update to new values
          this.youtubeData = response.data.data;
          console.log(this.youtubeData);
        } else {
          this.error =
              response.data.message || "Failed to fetch YouTube data";
        }
      } catch (error) {
        this.error =
            error.response?.data?.message ||
            "An error occurred while fetching YouTube data";
        console.error("YouTube Data Error:", error);
      }
    },
  },
};
</script>
<style lang="scss">
.stats-container {
  font-family: "Lilita One", sans-serif;
  display: flex;
  gap: 20px;
  margin-bottom: 20px;
  align-items: center;

  .stat-item {
    display: flex;
    gap: 5px;
    align-items: center;
    font-size: 24px;

    .icon {
      width: 32px;
      height: 32px;
      margin-right: 8px;
    }

    .stat-number {
      &.number-changed {
        animation: pulse 0.3s ease-in-out;
      }
    }
    &.yt {
      fill: red;
    }
    &.like {
     fill: #1C64F2;
    }
  }
}

/* Animation for the number change */
@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    transform: scale(1);
  }
}
</style>
