export const pckgDelimiters = {
    delimiters: ['${', '}']
};

export const dynamicEvents = {
    created: function () {
        $.each(this.triggers, function (method, events) {
            $.each(Array.isArray(events) ? events : [events], function (i, event) {
                this.$parent._data.localBus.$on(event, this[method]);
            }.bind(this));
        }.bind(this));
    },
    beforeDestroy: function () {
        $.each(this.triggers, function (method, events) {
            $.each(Array.isArray(events) ? events : [events], function (i, event) {
                this.$parent._data.localBus.$off(event, this[method]);
            }.bind(this));
        }.bind(this));
    },
};

export const pckgCdn = {
    methods: {
        cdn: function (file, id) {
            if (!file) {
                return file;
            }

            if (typeof file !== 'string') {
                return null;
            }
            if (file.indexOf('http://') === 0 || file.indexOf('https://') === 0 || file.indexOf('//') === 0) {
                return file;
            }

            if (!Pckg || !Pckg.config || !Pckg.config.cdn || !Pckg.config.cdn.host) {
                return file;
            }

            if (id) {
                return 'https://' + id + '.cdn.startcomms.com' + file;
            }

            return 'https://' + Pckg.config.cdn.host + file;
        },
        imageCache: function (pic, type, arg) {
            return pic && pic.length > 0
                ? '/cache/img/' + type + '/' + arg + pic
                : null;
        },
        mediaImage: function (pic, folder) {
            return pic && pic.length > 0
                ? '/storage/uploads/' + (folder ? folder + '/' : '') + pic
                : null;
        }
    }
};

// already migrated to pckg/translator
export const pckgTranslations = {
    methods: {
        __: function (key, data) {
            let translation;
            if (typeof key === 'object') {
                /**
                 * Get translation by current locale.
                 */
                let locale = Pckg.config.locale.current.toLowerCase();
                translation = key[locale] || Object.values(key)[0];
            } else {
                translation = $store.state.translations[key] || key;

                if (false && Object.keys($store.state.translations).indexOf(key) === -1) {
                    if ($store && $store.getters.isAdmin) {
                        return '__' + key;
                    }

                    return key.split('.').reverse()[0];
                }
            }

            if (!data) {
                return translation;
            }

            $.each(data, function (k, val) {
                translation = translation.replace('{{ ' + k + ' }}', val);
            });

            return translation;
        }
    }
};

// already migrated to htmlbuilder?
export const pckgFormValidator = {
    methods: {
        validateAndSubmit: function (submit, invalid) {
            this.$validator.validateAll().then(function (ok) {
                if (ok) {
                    submit();
                    return;
                }

                var element = $(this.$el).find('.htmlbuilder-validator-error').first();
                if (element && typeof globalScrollTo == Function) {
                    globalScrollTo(element);
                }
                if (invalid) {
                    invalid();
                }
            }.bind(this));
        },
        clearErrorResponse: function () {
            this.errors.clear();
        },
        hydrateErrorResponse: function (response) {
            /**
             * Clear existing errors.
             */
            this.clearErrorResponse();

            /**
             * Skip if no JSON response.
             */
            if (!response.responseJSON) {
                return;
            }

            /**
             * Populate errors.
             */
            $.each(response.responseJSON.descriptions || [], function (name, message) {
                this.errors.remove(name);
                this.errors.add({field: name, msg: message});
            }.bind(this));

            /**
             * Scroll to error.
             */
            this.$nextTick(function () {
                let e = $(this.$el).find('.htmlbuilder-validator-error').first();
                if (!e) {
                    return;
                }
                globalScrollTo(e);
            }.bind(this));
        }
    }
};

export const pckgSync = {
    methods: {
        single: function (name, request, object) {
            if (typeof object == 'undefined') {
                object = this;
            }

            if (object['_pckgSync' + name]) {
                object['_pckgSync' + name].abort();
            }

            object['_pckgSync' + name] = request;
        }
    }
};

export const pckgLocale = {
    methods: {
        locale: function () {
            return locale;
        }
    }
};

export const pckgTimeout = {
    methods: {
        timeout: function (name, callback, timeout, object) {
            if (typeof object == 'undefined') {
                object = this;
            }

            this.removeTimeout(name, object);

            this.setTimeout(name, callback, timeout, object);
        },
        removeTimeout: function (name, object) {
            if (typeof object == 'undefined') {
                object = this;
            }

            if (object['_pckgTimeout' + name]) {
                clearTimeout(this['_pckgTimeout' + name]);
            }
        },
        setTimeout: function (name, callback, timeout, object) {
            if (typeof object == 'undefined') {
                object = this;
            }

            object['_pckgTimeout' + name] = setTimeout(callback, timeout);
        }
    }
};

export const pckgInterval = {
    methods: {
        interval: function (name, callback, interval, object) {
            if (typeof object == 'undefined') {
                object = this;
            }

            this.removeInterval(name, object);

            this.setInterval(name, callback, interval, object);
        },
        removeInterval: function (name, object) {
            if (object['_pckgInterval' + name]) {
                clearInterval(this['_pckgInterval' + name]);
            }
        },
        setInterval: function (name, callback, interval, object) {
            object['_pckgInterval' + name] = setInterval(callback, interval);
        }
    }
};

export const pckgCleanRequest = {
    methods: {
        cleanRequest: function (callback, name) {
            if (this['_pckgCleanRequest' + name]) {
                this['_pckgCleanRequest' + name].abort();
            }

            this['_pckgCleanRequest' + name] = callback();
        }
    }
};

export const pckgNativeEvents = {
    methods: {
        onNative: function (element, event, callback) {
            $(element).on(event, callback.bind(this));
        },
        offNative: function (element, callback) {
            $(element).off(callback);
        }
    }
};