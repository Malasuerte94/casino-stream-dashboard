<template>
    <div >
      <button @click="test()">Test</button>
      {{data}}
    </div>
</template>

<script>
export default {
  data() {
    return {
      data: [],
    };
  },
  async mounted() {
  },
  methods: {
    async test() {
      try {
        const response = await axios.get(`/api/test`);
        this.data = response.data;
      } catch (error) {
        console.error("Error fetching weekly schedules:", error);
      }
    },
  },
};
</script>
