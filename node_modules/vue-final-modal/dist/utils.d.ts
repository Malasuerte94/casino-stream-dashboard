export declare const once: (fn: ((...args: any[]) => void) | null) => (...args: any[]) => void;
export declare const noop: () => void;
export declare function clamp(val: number, min: number, max: number): number;
export declare const isString: (value: unknown) => value is string;
/**
 * @example
 * const arr = [1, 2, 6, 3, 4, 5]
 * arrayMoveItemToLast(arr, 6)
 * console.log(arr) // [1, 2, 3, 4, 5, 6]
 */
export declare function arrayMoveItemToLast<T>(arr: T[], item: T): void;
export declare function arrayRemoveItem<T>(arr: T[], item: T): T[] | undefined;
type Entries<T> = {
    [K in keyof T]: [K, T[K]];
}[keyof T][];
/**
 * Type safe variant of `Object.entries()`
 */
export declare function objectEntries<T extends Record<any, any>>(object: T): Entries<T>;
export {};
