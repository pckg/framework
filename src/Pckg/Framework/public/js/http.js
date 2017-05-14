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

        return data;
    },

    submitForm: function (vueElement, fields) {
        return http.post($(vueElement.$el.attr('action')), http.formToData(vueElement, fields));
    },

    get: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            method: 'get'
        }).done(whenDone).error(whenError);
    },

    getJSON: function (url, whenDone, whenError) {
        var request = $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'get'
        });

        if (whenDone) {
            request.done(whenDone);
        }

        if (whenError) {
            request.error(whenError);
        }

        return request;
    },

    deleteJSON: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            method: 'delete'
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

    form: function ($form, successCallback) {
        return http.post($form.attr('action'), $form.serializeArray(), successCallback);
    },

    fixUndefined: function (data) {
        $.each(data, function (key, val) {
            if (Array.isArray(val) || typeof val == 'object') {
                data[key] = http.fixUndefined(val);

            } else if (typeof val == 'undefined') {
                data[key] = '';

            } else if (val === true) {
                data[key] = 1;

            } else if (val === false || val === '' || val === null) {
                data[key] = null;

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
        if (!url || typeof url == 'undefined') {
            url = window.location.href = window.location.href;
        }

        window.location.href = url;
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
            }) + ' ' + Pckg.config.locale.currencySign;
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
            }) + ' ' + Pckg.config.locale.currencySign;
    },

    date: function (date) {
        if (!date) {
            return null;
        }

        moment.locale(props.locale);

        return moment(date).format(Pckg.config.locale.format.dateMoment);
    },

    time: function (time) {
        if (!time) {
            return null;
        }

        moment.locale(props.locale);

        return moment(time).format(Pckg.config.locale.format.timeMoment);
    },

    datetime: function (datetime) {
        return this.date(datetime) + ' ' + this.time(datetime);
    },

    trans: function (trans, params) {
        $.each(params, function (key, val) {
            trans = trans.replace('{{ ' + key + ' }}', val);
        });

        return trans;
    }

};

var utils = {

    isSameDate: function (first, second) {
        return locale.date(first) == locale.date(second);
    },

    fix: function (value) {
        return value ? value : null;
    },

    url: function (url, params) {
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        return url;
    },

    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    closeIfIframe: function () {
        this.sendToParent('popup.close');
    },

    closeAndRefresh: function(){
        $.magnificPopup.close();
        http.redirect();
    },

    sendToParent: function (data) {
        parent.postMessage(data, window.location.origin);
    },

    isIframe: function () {
        return parent.window != window;
    },

    collect: function (item) {
        return Array.isArray(item)
            ? item
            : [item];
    },
    prepend: function (value, array) {
        var newArray = array.slice(0);

        newArray.unshift(value);

        return newArray;
    },
    sort: function (obj) {
        var sorted = {};
        Object.keys(obj).sort().forEach(function (value, key) {
            sorted[key] = value;
        });

        return sorted;
    },
    mergeObject: function (to, from) {
        $.each(from, function (key, value) {
            to[key] = value;
        });

        return to;
    },
    pushTo: function (to, from) {
        $.each(from, function (key, value) {
            to.push(value);
        });

        return to;
    },
    firstOf: function (items, callback) {
        var first = null;

        $.each(items, function (i, item) {
            if (callback(item, i)) {
                first = item;
                return false;
            }
        });

        return first;
    },
    lastOf: function (items, callback) {
        return this.firstOf(items.reverse(), callback);
    },
    last: function (items) {
        return items[items.length - 1];
    },
    sortInt: function (a, b) {
        return a < b ? -1 : (a > b ? 1 : 0);
    },
    splice: function (collection, item) {
        return collection.splice(collection.indexOf(item), 1);
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
            center: [46.055144, 14.512284],
            //mapTypeControl: false,
            //scrollwheel: false
        }
    }
};

var d = function (data) {
    console.log(data);
};