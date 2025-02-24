export default {
  install(app, options) {
    const defaultCurrency = options?.currency || 'RON';
    app.config.globalProperties.$formatCurrency = (value, currency = defaultCurrency) => {
      return new Intl.NumberFormat("ro-RO", {
        style: "currency",
        currency,
        minimumFractionDigits: 0
      }).format(value);
    };
  }
};
