import { type ModalExposed } from '../../Modal';
export interface VueFinalModalEmits {
    (e: 'update:modelValue', modelValue: boolean): void;
    (e: 'beforeOpen', event: {
        stop: () => void;
    }): void;
    (e: 'opened'): void;
    (e: 'beforeClose', event: {
        stop: () => void;
    }): void;
    (e: 'closed'): void;
    /** onClickOutside will only be emitted when clickToClose equal to `false` */
    (e: 'clickOutside'): void;
}
declare const _default: __VLS_WithTemplateSlots<import("vue").DefineComponent<{
    readonly teleportTo: {
        readonly type: import("vue").PropType<string | false | import("vue").RendererElement | null>;
        readonly default: "body";
    };
    readonly modalId: {
        readonly type: import("vue").PropType<import('../../Modal').ModalId>;
        readonly default: undefined;
    };
    readonly modelValue: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
    readonly displayDirective: {
        readonly type: import("vue").PropType<"if" | "show" | "visible">;
        readonly default: "if";
        readonly validator: (prop: any) => boolean;
    };
    readonly hideOverlay: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
    readonly overlayBehavior: {
        readonly type: import("vue").PropType<"auto" | "persist">;
        readonly default: "auto";
        readonly validator: (prop: any) => boolean;
    };
    readonly overlayTransition: {
        readonly type: import("vue").PropType<("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps>;
        readonly default: undefined;
    };
    readonly contentTransition: {
        readonly type: import("vue").PropType<("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps>;
        readonly default: undefined;
    };
    readonly overlayClass: {
        readonly type: import("vue").PropType<any>;
        readonly default: undefined;
    };
    readonly contentClass: {
        readonly type: import("vue").PropType<any>;
        readonly default: undefined;
    };
    readonly overlayStyle: {
        readonly type: import("vue").PropType<import('../../Modal').StyleValue>;
        readonly default: undefined;
    };
    readonly contentStyle: {
        readonly type: import("vue").PropType<import('../../Modal').StyleValue>;
        readonly default: undefined;
    };
    readonly clickToClose: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly escToClose: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly background: {
        readonly type: import("vue").PropType<"interactive" | "non-interactive">;
        readonly default: "non-interactive";
        readonly validator: (prop: any) => boolean;
    };
    readonly focusTrap: {
        readonly type: import("vue").PropType<false | import("focus-trap").Options>;
        readonly default: () => {
            allowOutsideClick: boolean;
        };
    };
    readonly lockScroll: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly reserveScrollBarGap: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly zIndexFn: {
        readonly type: import("vue").PropType<(context: {
            index: number;
        }) => number | undefined>;
        readonly default: ({ index }: {
            index: number;
        }) => number;
    };
    readonly swipeToClose: {
        readonly type: import("vue").PropType<"none" | "up" | "right" | "down" | "left">;
        readonly default: "none";
        readonly validator: (prop: any) => boolean;
    };
    readonly threshold: {
        readonly type: import("vue").PropType<number>;
        readonly default: 0;
    };
    readonly showSwipeBanner: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
    readonly preventNavigationGestures: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
}, {
    modalExposed: import("vue").ComputedRef<ModalExposed>;
}, unknown, {}, {}, import("vue").ComponentOptionsMixin, import("vue").ComponentOptionsMixin, {
    "update:modelValue": (modelValue: boolean) => void;
    beforeOpen: (event: {
        stop: () => void;
    }) => void;
    opened: () => void;
    beforeClose: (event: {
        stop: () => void;
    }) => void;
    closed: () => void;
    clickOutside: () => void;
}, string, import("vue").VNodeProps & import("vue").AllowedComponentProps & import("vue").ComponentCustomProps, Readonly<import("vue").ExtractPropTypes<{
    readonly teleportTo: {
        readonly type: import("vue").PropType<string | false | import("vue").RendererElement | null>;
        readonly default: "body";
    };
    readonly modalId: {
        readonly type: import("vue").PropType<import('../../Modal').ModalId>;
        readonly default: undefined;
    };
    readonly modelValue: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
    readonly displayDirective: {
        readonly type: import("vue").PropType<"if" | "show" | "visible">;
        readonly default: "if";
        readonly validator: (prop: any) => boolean;
    };
    readonly hideOverlay: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
    readonly overlayBehavior: {
        readonly type: import("vue").PropType<"auto" | "persist">;
        readonly default: "auto";
        readonly validator: (prop: any) => boolean;
    };
    readonly overlayTransition: {
        readonly type: import("vue").PropType<("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps>;
        readonly default: undefined;
    };
    readonly contentTransition: {
        readonly type: import("vue").PropType<("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps>;
        readonly default: undefined;
    };
    readonly overlayClass: {
        readonly type: import("vue").PropType<any>;
        readonly default: undefined;
    };
    readonly contentClass: {
        readonly type: import("vue").PropType<any>;
        readonly default: undefined;
    };
    readonly overlayStyle: {
        readonly type: import("vue").PropType<import('../../Modal').StyleValue>;
        readonly default: undefined;
    };
    readonly contentStyle: {
        readonly type: import("vue").PropType<import('../../Modal').StyleValue>;
        readonly default: undefined;
    };
    readonly clickToClose: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly escToClose: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly background: {
        readonly type: import("vue").PropType<"interactive" | "non-interactive">;
        readonly default: "non-interactive";
        readonly validator: (prop: any) => boolean;
    };
    readonly focusTrap: {
        readonly type: import("vue").PropType<false | import("focus-trap").Options>;
        readonly default: () => {
            allowOutsideClick: boolean;
        };
    };
    readonly lockScroll: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly reserveScrollBarGap: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: true;
    };
    readonly zIndexFn: {
        readonly type: import("vue").PropType<(context: {
            index: number;
        }) => number | undefined>;
        readonly default: ({ index }: {
            index: number;
        }) => number;
    };
    readonly swipeToClose: {
        readonly type: import("vue").PropType<"none" | "up" | "right" | "down" | "left">;
        readonly default: "none";
        readonly validator: (prop: any) => boolean;
    };
    readonly threshold: {
        readonly type: import("vue").PropType<number>;
        readonly default: 0;
    };
    readonly showSwipeBanner: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
    readonly preventNavigationGestures: {
        readonly type: import("vue").PropType<boolean>;
        readonly default: undefined;
    };
}>> & {
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
}, {
    readonly teleportTo: string | false | import("vue").RendererElement | null;
    readonly modalId: import('../../Modal').ModalId;
    readonly modelValue: boolean;
    readonly displayDirective: "if" | "show" | "visible";
    readonly hideOverlay: boolean;
    readonly overlayBehavior: "auto" | "persist";
    readonly overlayTransition: ("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps;
    readonly contentTransition: ("vfm-fade" | "vfm-slide-down" | "vfm-slide-up" | "vfm-slide-right" | "vfm-slide-left" | (string & Record<never, never>)) | import("vue").TransitionProps;
    readonly overlayClass: any;
    readonly contentClass: any;
    readonly overlayStyle: import('../../Modal').StyleValue;
    readonly contentStyle: import('../../Modal').StyleValue;
    readonly clickToClose: boolean;
    readonly escToClose: boolean;
    readonly background: "interactive" | "non-interactive";
    readonly focusTrap: false | import("focus-trap").Options;
    readonly lockScroll: boolean;
    readonly reserveScrollBarGap: boolean;
    readonly zIndexFn: (context: {
        index: number;
    }) => number | undefined;
    readonly swipeToClose: "none" | "up" | "right" | "down" | "left";
    readonly threshold: number;
    readonly showSwipeBanner: boolean;
    readonly preventNavigationGestures: boolean;
}, {}>, __VLS_DefineSlots<{
    default?(props: {
        close: () => void;
    }): void;
    'swipe-banner'?(): void;
}>>;
export default _default;
type __VLS_WithTemplateSlots<T, S> = T & {
    new (): {
        $slots: S;
    };
};
type __VLS_DefineSlots<T> = {
    [SlotName in keyof T]: (_: T[SlotName]) => any;
};
