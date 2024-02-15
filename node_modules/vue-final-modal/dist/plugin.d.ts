import type { ComponentInternalInstance, ComputedRef } from 'vue';
import type { ModalExposed, Vfm } from './Modal';
export declare let activeVfm: Vfm | undefined;
export declare const setActiveVfm: (vfm: Vfm | undefined) => Vfm | undefined;
export declare const defaultVfm: Vfm;
export declare const getActiveVfm: () => Vfm | undefined;
export declare function createVfm(): Vfm;
export declare function getModalExposed(componentInternalInstance: undefined | null | ComponentInternalInstance): undefined | null | ComputedRef<ModalExposed>;
