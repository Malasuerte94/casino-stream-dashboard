import type { Ref } from 'vue';
import type VueFinalModal from './VueFinalModal.vue';
export declare function useToClose(props: InstanceType<typeof VueFinalModal>['$props'], emit: InstanceType<typeof VueFinalModal>['$emit'], options: {
    vfmRootEl: Ref<HTMLDivElement | undefined>;
    vfmContentEl: Ref<HTMLDivElement | undefined>;
    visible: Ref<boolean>;
    modelValueLocal: Ref<boolean>;
}): {
    onEsc: () => void;
    onMouseupRoot: () => void;
    onMousedown: (e?: MouseEvent) => void;
};
