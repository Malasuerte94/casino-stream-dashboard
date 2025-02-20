<template>
    <div class="canvas-container">
        <div class="board">
            <div v-for="column in 6" :key="'real_' + column" :id="'real_' + column">
                <transition name="drop" appear v-for="item in nextSpinDisplay.filter(i => i.column === column)" :key="item.unique_id">
                    <div v-if="elementVisible[item.identifier - 1]" :id="'element' + item.row + '_' + item.column">
                        {{ getElement(item.identifier) }}
                    </div>
                </transition>
            </div>
        </div>
        <button class="spin_button" @click="spin()">SPIN</button>
    </div>
</template>

<script>
export default {
    props: ["id"],
    data() {
        return {
            elementVisible: Array(30).fill(false),
            nextSpinDisplay: [
                { identifier: 1, row: 1, column: 1, unique_id: '23f3jf1' },
                { identifier: 2, row: 2, column: 1, unique_id: '23f3jf2' },
                { identifier: 3, row: 3, column: 1, unique_id: '23f3jf3' },
                { identifier: 4, row: 4, column: 1, unique_id: '23f3jf4' },
                { identifier: 5, row: 5, column: 1, unique_id: '23f3jf5' },
                { identifier: 6, row: 1, column: 2, unique_id: '23f3jf6' },
                { identifier: 7, row: 2, column: 2, unique_id: '23f3jf7' },
                { identifier: 8, row: 3, column: 2, unique_id: '23f3jf8' },
                { identifier: 9, row: 4, column: 2, unique_id: '23f3jf9' },
                { identifier: 10, row: 5, column: 2, unique_id: '23f3jf10' },
                { identifier: 11, row: 1, column: 3, unique_id: '23f3jf11' },
                { identifier: 12, row: 2, column: 3, unique_id: '23f3jf12' },
                { identifier: 13, row: 3, column: 3, unique_id: '23f3jf13' },
                { identifier: 14, row: 4, column: 3, unique_id: '23f3jf14' },
                { identifier: 15, row: 5, column: 3, unique_id: '23f3jf15' },
                { identifier: 16, row: 1, column: 4, unique_id: '23f3jf16' },
                { identifier: 17, row: 2, column: 4, unique_id: '23f3jf17' },
                { identifier: 18, row: 3, column: 4, unique_id: '23f3jf18' },
                { identifier: 19, row: 4, column: 4, unique_id: '23f3jf19' },
                { identifier: 20, row: 5, column: 4, unique_id: '23f3jf20' },
                { identifier: 21, row: 1, column: 5, unique_id: '23f3jf21' },
                { identifier: 22, row: 2, column: 5, unique_id: '23f3jf22' },
                { identifier: 23, row: 3, column: 5, unique_id: '23f3jf23' },
                { identifier: 24, row: 4, column: 5, unique_id: '23f3jf24' },
                { identifier: 25, row: 5, column: 5, unique_id: '23f3jf25' },
                { identifier: 26, row: 1, column: 6, unique_id: '23f3jf26' },
                { identifier: 27, row: 2, column: 6, unique_id: '23f3jf27' },
                { identifier: 28, row: 3, column: 6, unique_id: '23f3jf28' },
                { identifier: 29, row: 4, column: 6, unique_id: '23f3jf29' },
                { identifier: 30, row: 5, column: 6, unique_id: '23f3jf30' },
            ]
        };
    },
    async mounted() { },
    methods: {
        spin() {
                        // Hide all elements
            this.elementVisible = this.elementVisible.map(() => false);

            // After hiding, show elements individually with a delay to create a spin effect
            this.elementVisible.forEach((_, index) => {
                setTimeout(() => {
                    this.$set(this.elementVisible, index, true);
                }, index * 50);  // Increase the multiplier for slower effect
            });
        },
        getElement($identifier) {
            if ($identifier === 1) {
                return 'X';
            }
            return '';
        }
    }
};
</script>

<style lang="scss">
.canvas-container {
    width: 640px;
    height: 360px;
    margin: 0 auto;
    background-image: url("../storage/assets/images/bg_main.png");
    background-position: bottom;
    background-repeat: no-repeat;
    background-size: cover;
    position: relative;
    .board {
        width: 448px;
        height: 252px;
        margin: 0 20px auto 20px;
        background-image: url("../storage/assets/images/boad.png");
        background-position: bottom;
        background-repeat: no-repeat;
        background-size: cover;
        position: absolute;
        margin-top: 30px;
        left: 0;
        right: 0;
        margin-left: auto;
        margin-right: auto;

        .drop-enter-active, .drop-leave-active {
        animation-duration: 0.5s;  // You can adjust this for faster/slower drops
        }
        .drop-enter, .drop-leave-to /* .drop-leave-active in <2.1.8 */ {
            animation-name: dropEffect;
            animation-fill-mode: both;
        }
    }
    .spin_button {
        padding: 10px;
        background: green;
        color: white;
    }
}

@keyframes dropEffect {
    0% {
        opacity: 0;
        transform: translateY(-100%);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

</style>
