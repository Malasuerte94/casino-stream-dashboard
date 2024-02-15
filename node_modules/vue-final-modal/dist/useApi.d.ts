import type { Component } from 'vue';
import VueFinalModal from './components/VueFinalModal/VueFinalModal.vue';
import type { ModalSlotOptions, UseModalOptions, UseModalReturnType, Vfm } from './Modal';
import type { ComponentEmit, ComponentProps } from './Component';
/**
 * Returns the vfm instance. Equivalent to using `$vfm` inside
 * templates.
 */
export declare function useVfm(): Vfm;
/**
 * Create a dynamic modal.
 */
export declare function useModal<T extends Component = typeof VueFinalModal>(_options: UseModalOptions<T>): UseModalReturnType<T>;
export declare function useModalSlot<T extends Component>(options: {
    component: T;
    attrs?: ComponentProps<T>;
}): {
    component: T;
    attrs?: ComponentProps<T> | undefined;
};
export declare function isModalSlotOptions(value: unknown): value is ModalSlotOptions;
export declare function pickModalProps(props: Record<string, any>, modalProps: Record<string, any>): Record<string, any>;
export declare function byPassAllModalEvents(emit?: ComponentEmit<typeof VueFinalModal>): ComponentProps<typeof VueFinalModal>;
export declare function useVfmAttrs<TP extends Component, MP extends Component>(options: {
    props: ComponentProps<TP>;
    modalProps: ComponentProps<MP>;
    emit?: any;
}): import("vue").ComputedRef<{
    teleportTo?: string | false | import("vue").RendererElement | null | undefined;
    modalId?: import("./Modal").ModalId | undefined;
    modelValue?: boolean | undefined;
    displayDirective?: "if" | "show" | "visible" | undefined;
    hideOverlay?: boolean | undefined;
    overlayBehavior?: "auto" | "persist" | undefined;
    overlayTransition?: ("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps | undefined;
    contentTransition?: ("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps | undefined;
    overlayClass?: any;
    contentClass?: any;
    overlayStyle?: import("./Modal").StyleValue | undefined;
    contentStyle?: import("./Modal").StyleValue | undefined;
    clickToClose?: boolean | undefined;
    escToClose?: boolean | undefined;
    background?: "interactive" | "non-interactive" | undefined;
    focusTrap?: false | import("focus-trap").Options | undefined;
    lockScroll?: boolean | undefined;
    reserveScrollBarGap?: boolean | undefined;
    zIndexFn?: ((context: {
        index: number;
    }) => number | undefined) | undefined;
    swipeToClose?: "none" | "up" | "right" | "down" | "left" | undefined;
    threshold?: number | undefined;
    showSwipeBanner?: boolean | undefined;
    preventNavigationGestures?: boolean | undefined;
    "onUpdate:modelValue"?: ((modelValue: boolean) => any) | undefined;
    onBeforeOpen?: ((event: {
        stop: () => void;
    }) => any) | undefined;
    onOpened?: (() => any) | undefined;
    onBeforeClose?: ((event: {
        stop: () => void;
    }) => any) | undefined;
    onClosed?: (() => any) | undefined;
    onClickOutside?: (() => any) | undefined;
    key?: string | number | symbol | undefined;
    ref?: import("vue").VNodeRef | undefined;
    ref_for?: boolean | undefined;
    ref_key?: string | undefined;
    onVnodeBeforeMount?: ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void) | ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void)[] | undefined;
    onVnodeMounted?: ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void) | ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void)[] | undefined;
    onVnodeBeforeUpdate?: ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>, oldVNode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void) | ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>, oldVNode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void)[] | undefined;
    onVnodeUpdated?: ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>, oldVNode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void) | ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>, oldVNode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void)[] | undefined;
    onVnodeBeforeUnmount?: ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void) | ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void)[] | undefined;
    onVnodeUnmounted?: ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void) | ((vnode: import("vue").VNode<import("vue").RendererNode, import("vue").RendererElement, {
        [key: string]: any;
    }>) => void)[] | undefined;
    class?: unknown;
    style?: unknown;
}>;
