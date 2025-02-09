<template>
    <template v-if="!loading">
        <carousel :items-to-show="1" :autoplay="5000" :transition="1000" :wrapAround="true">
            <slide v-for="banner in banners" :key="banner.id">
                <img :src="banner.image" alt="">
            </slide>
        </carousel>
    </template>
</template>
<script>
import "vue3-carousel/dist/carousel.css";
import { Carousel, Slide} from "vue3-carousel";
export default {
    props: ["id"],
    data() {
        return {
            loading: true,
            banners: [],
        };
    },
    components: {Carousel, Slide},
    async mounted() {
        await this.getBanners();
        this.loading = false;
    },
    methods: {
        async getBanners() {
            await axios
                .get("/api/banners/" + this.id)
                .then((response) => {
                    this.banners = response.data.banners;
                })
                .catch((error) => {
                    console.log(error);
                });
        },
    },
};
</script>

<style lang="scss">
html,
body,
#app {
  background-color: transparent !important;
}

.footer-donate {
  display: none !important;
}
</style>
