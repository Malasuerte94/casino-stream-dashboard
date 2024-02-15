import type { Ref } from 'vue';
import type VueFinalModal from './VueFinalModal.vue';
export declare function useModelValue(props: InstanceType<typeof VueFinalModal>['$props'], emit: InstanceType<typeof VueFinalModal>['$emit'], options: {
    open: () => boolean;
    close: () => boolean;
}): {
    modelValueLocal: Ref<boolean>;
};
