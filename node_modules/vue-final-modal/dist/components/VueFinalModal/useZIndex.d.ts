import type VueFinalModal from './VueFinalModal.vue';
export declare function useZIndex(props: InstanceType<typeof VueFinalModal>['$props']): {
    zIndex: import("vue").Ref<number | undefined>;
    refreshZIndex: (index: number) => void;
    resetZIndex: () => void;
};
