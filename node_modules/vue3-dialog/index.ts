import dialog from './dialog';
import applyStyles from './styles';

const plugin = {
    install(app, options) {

        app.config.globalProperties.$dialogOptions = {
            message: 'Hello World!',
            buttons: ['cancel', 'confirm'],
            presets: {},
            callbacks: {},
            wrapperClass: '__dialog-wrapper',
            boxClass: '__dialog-box',
            messageClass: '__dialog-message',
            buttonsClass: '__dialog-buttons',
            css: {
                default: true,
                wrapper: true,
                darken: 0.6
            }
        };

        const mergeOptions = localOptions => {

            const globalOptions = app.config.globalProperties.$dialogOptions;

            if (localOptions) {

                if ('preset' in localOptions) {
                    const p = globalOptions.presets[localOptions.preset];
                    if (p) {
                        for (const key in p) localOptions[key] = localOptions[key] || p[key];
                    }
                }

                const merged = { ...globalOptions, ...localOptions, css: {...globalOptions.css, ...(localOptions?.css || {}) } };

                if (Array.isArray(merged.buttons)) {
                    const buttons = {};
                    merged.buttons.forEach(b => buttons[b] = {});
                    merged.buttons = buttons;
                }

                Object.keys(localOptions).forEach(key => {
                    const value = localOptions[key];
                    if (!['message', 'buttons', 'presets', 'callbacks', 'css', 'wrapperClass', 'boxClass', 'messageClass', 'buttonsClass', 'preset'].includes(key) && (value instanceof Function) && (value.length === 0)) merged.callbacks[key] = value;
                })

                return merged;

            }

            else return globalOptions;
        };

        app.config.globalProperties.$dialogOptions = mergeOptions(options);

        applyStyles(app.config.globalProperties.$dialogOptions.css);

        const createDialog = localOptions => dialog(mergeOptions(localOptions));

        app.directive('dialog', {
            mounted: (el, { value, arg }) =>  {
                if (arg) {
                    if (value) value.preset = arg;
                    else value = { preset: arg };
                }
                el.onclick = () => createDialog(value || {});
            },
            unmounted: el => el.onclick = null
        });

        app.config.globalProperties.$dialog = localOptions => createDialog(localOptions);

        app.provide('$dialog', localOptions => createDialog(localOptions));

        app.provide('$dialogOptions', app.config.globalProperties.$dialogOptions);

    }
};

export default plugin;