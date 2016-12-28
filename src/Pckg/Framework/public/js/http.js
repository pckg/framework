var data = data || {};
var http = {

    formToData: function (vueElement, keys) {
        var data = {};

        if (typeof keys === 'undefined') {
            $.each(vueElement.form, function (key, val) {
                data[key] = vueElement.form[key];
            });

        } else {
            $.each(keys, function (i, key) {
                data[key] = vueElement.form[key];
            });

        }

        console.log(data, vueElement.form);

        return data;
    },

    submitForm: function (vueElement, fields) {
        var data = http.formToData(vueElement, fields);
        var $form = $(vueElement.$el);
        var url = $form.attr('action');

        return http.post(url, data);
    },

    get: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    getJSON: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    post: function (url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        if (typeof whenDone == 'undefined') {
            whenDone = http.postDone;
        }

        data = http.fixUndefined(data);

        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'post',
            data: data
        }).done(whenDone).error(whenError);
    },

    fixUndefined: function (data) {
        $.each(data, function (key, val) {
            if (Array.isArray(val) || typeof val == 'object') {
                data[key] = http.fixUndefined(val);

            } else if (typeof val == 'undefined') {
                data[key] = '';

            }
        });

        return data;
    },

    postDone: function (json) {
        if (typeof json.redirect !== 'undefined') {
            if (typeof window.parent !== 'undefined' && window.parent.location.href.indexOf(json.redirect)) {
                parent.postMessage('refresh', window.location.origin);

            } else {
                window.location.href = json.redirect;

            }
        }
    },

    redirect: function (url) {
        window.location.href = url != null
            ? url
            : window.location.href;
    }

};

var locale = {

    price: function (price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = 2;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(props.locale.replace('_', '-').toLowerCase(), {
                currency: 'eur',
                currencyDisplay: 'symbol',
                maximumFractionDigits: decimals,
                minimumFractionDigits: decimals
            }) + ' €';
    },

    roundPrice: function (price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = 2;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(props.locale.replace('_', '-').toLowerCase(), {
                currency: 'eur',
                currencyDisplay: 'symbol',
                maximumFractionDigits: decimals,
                minimumFractionDigits: 0
            }) + ' €';
    },

    date: function (date) {
        moment.locale(props.locale);

        return moment(date).format('LL');
    },

    time: function (time) {
        moment.locale(props.locale);

        return moment(time).format('LT');
    },

    trans: function (trans, params) {
        $.each(params, function (key, val) {
            trans = trans.replace('{{ ' + key + ' }}', val);
        });

        return trans;
    }

};

var utils = {

    fix: function (value) {
        return value ? value : null;
    },

    url: function (url, params) {
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        return url;
    },

    vues: {},

    lastVue: null,

    firstVue: null,

    createVue: function (key, data) {
        this.lastVue = this.vues[key] = new Vue(data);

        if (this.firstVue) {
            //this.firstVue.$addChild(this.lastVue);
        } else {
            this.firstVue = this.lastVue;
        }

        return this.lastVue;
    },

    pushToVue: function (obj) {
        if (this.firstVue) {
            console.log('Overwriting last Vue.js object', obj);
            $.each(obj, function (type, datas) {
                if (type == 'methods') {
                    console.log(" " + type);
                    $.each(datas, function (key, data) {
                        console.log("  " + key);
                        this.firstVue[key] = data.bind(this.firstVue);
                    }.bind(this));
                } else if (type == 'data') {
                    console.log(" " + type);
                    $.each(datas, function (key, data) {
                        console.log("  " + key);
                        this.firstVue[key] = data;
                    }.bind(this));
                } else if (type == 'on') {
                    console.log(" " + type);
                    $.each(datas, function (event, callback) {
                        console.log("  " + event);
                        this.firstVue.$on(event, callback.bind(this.firstVue));
                    }.bind(this));
                } else {
                    console.log('unknown', type, datas);
                }
            }.bind(this));
            console.log(this.firstVue);
        } else {
            $.extend(true, $vue, obj);
        }
    },

    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    closeIfIframe: function () {
        this.sendToParent('popup.close');
    },

    sendToParent: function (data) {
        console.log("Sending event", data);
        parent.postMessage(data, window.location.origin);
    },

    isIframe: function () {
        return parent.window != window;
    }

};

var settings = settings || {};
settings.vue = settings.vue || {};
settings.vue.gmaps = {
    themes: {
        getOptions: function (theme) {
            var themes = settings.vue.gmaps.themes;
            var options = themes.base;

            // merge with defaults
            if (typeof themes[theme] != 'undefined') {
                options = this.mergeOptions(options, themes[theme]);
            }

            return options;
        },
        mergeOptions: function (original, overwrite) {
            $.each(overwrite, function (i, values) {
                original[i] = values;
            });

            return original;
        },
        base: {
            zoom: 10,
            center: new google.maps.LatLng(46.055144, 14.512284),
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            //mapTypeControl: false,
            //scrollwheel: false
        }
    }
};

var d = function (data) {
    console.log(data);
};