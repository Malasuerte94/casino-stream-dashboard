export declare const isProd: boolean;
interface LoggerComposition {
    logError: (message: string, error?: Error | ErrorEvent) => void;
    logWarning: (message: string) => void;
    logMessage: (name: string, value: any) => void;
}
export declare function useLogger(): LoggerComposition;
export {};
