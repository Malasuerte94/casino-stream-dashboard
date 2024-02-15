import type { Ref } from 'vue';
import type VueFinalModal from './VueFinalModal.vue';
export declare function useFocusTrap(props: InstanceType<typeof VueFinalModal>['$props'], options: {
    focusEl: Ref<undefined | HTMLDivElement>;
}): {
    focus(): void;
    blur(): void;
};
