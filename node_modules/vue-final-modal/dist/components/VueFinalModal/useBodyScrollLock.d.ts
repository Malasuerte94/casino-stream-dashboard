import type { Ref } from 'vue';
import type VueFinalModal from './VueFinalModal.vue';
type BodyScrollOptions = {
    reserveScrollBarGap?: boolean;
    allowTouchMove?: (el?: null | HTMLElement) => boolean;
};
export declare const disableBodyScroll: (targetElement?: HTMLElement, options?: BodyScrollOptions) => void;
export declare const enableBodyScroll: (targetElement?: HTMLElement) => void;
export declare function useLockScroll(props: InstanceType<typeof VueFinalModal>['$props'], options: {
    lockScrollEl: Ref<undefined | HTMLElement>;
    modelValueLocal: Ref<boolean>;
}): {
    enableBodyScroll: () => void;
    disableBodyScroll: () => void;
};
export {};
