var data = data || {};
var http = {

    formToData: function (vueElement, keys) {
        var data = {};

        if (typeof keys === 'undefined') {
            $.each(vueElement.form, function (key, val) {
                data[key] = val;
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

    search: function get(url, whenDone, whenError, options) {
        if (options) {
            return this.getJSON(url, whenDone, whenError, options);
        }

        return $.ajax({
            url: url,
            type: 'SEARCH'
        }).done(whenDone).fail(whenError);
    },

    get: function (url, whenDone, whenError, options) {
        if (options) {
            return this.getJSON(url, whenDone, whenError, options);
        }

        return $.ajax({
            url: url,
            type: 'GET'
        }).done(whenDone).fail(whenError);
    },

    getJSON: function (url, whenDone, whenError, options) {
        var request = $.ajax(Object.assign({
            url: url,
            dataType: 'JSON',
            type: 'GET'
        }, options || {}));

        if (whenDone) {
            request.done(whenDone);
        }

        if (whenError) {
            request.fail(whenError);
        }

        return request;
    },

    deleteJSON: function (url, whenDone, whenError) {
        return $.ajax({
            url: url,
            dataType: 'JSON',
            type: 'DELETE'
        }).done(whenDone).fail(whenError);
    },

    post: function (url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        if (typeof whenDone == 'undefined') {
            whenDone = http.postDone;
        }

        if (typeof whenError == 'undefined') {
            whenError = http.postError;
        }

        data = http.fixUndefined(data);

        return $.ajax({
            url: url,
            dataType: 'JSON',
            type: 'POST',
            data: data
        }).done(whenDone).fail(whenError);
    },

    patch: function (url, data, whenDone, whenError) {
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
            type: 'PATCH',
            data: data
        }).done(whenDone).fail(whenError);
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

    postError: function postError(response) {
        console.log('postError', response.responseJSON);
    },

    redirect: function (url) {
        if (!url || typeof url == 'undefined') {
            url = window.location.href;
        }

        window.location.href = url;
    }

};

var locale = {

    price: function (price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(Pckg.config.locale.current.replace('_', '-').toLowerCase(), {
                currency: 'eur',
                currencyDisplay: 'symbol',
                maximumFractionDigits: decimals,
                minimumFractionDigits: decimals
            }) + ' ' + Pckg.config.locale.currencySign;
    },

    roundPrice: function (price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString(Pckg.config.locale.current.replace('_', '-').toLowerCase(), {
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

        moment.locale(Pckg.config.locale.current);

        return moment(date).format(Pckg.config.locale.format.dateMoment);
    },

    time: function (time) {
        if (!time) {
            return null;
        }

        moment.locale(Pckg.config.locale.current);

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

var collection = {

    groupBy: function(collection, groupBy) {
        let groups = {};

        $.each(collection, function(i, item){
            let group = groupBy(item, i);
            if (!groups[group]) {
                groups[group] = [];
            }
            groups[group].push(item);
        });

        return groups;
    },

    map: function(collection, map) {
        let mapped = {};

        $.each(collection, function(i, item){
            mapped[i] = map(item);
        });

        return mapped;
    },

    keyBy: function(collection, key) {
        let keyed = {};

        $.each(collection, function(i, item){
            keyed[key(item, i)] = item;
        });

        return keyed;
    }

};

var utils = {

    ucfirst: function(str) {
        if (!str) {
            return '';
        }

        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    isSameDate: function (first, second) {
        return locale.date(first) == locale.date(second);
    },

    fix: function (value) {
        return value ? value : null;
    },

    url: function (url, params, absolute) {
        if (!url) {
            return;
        }
        if (url.indexOf('@') === 0 && Pckg.router.urls[url.substring(1)]) {
            url = Pckg.router.urls[_url.substring(1)];
        }
        if (url.indexOf('@') === 0 && Pckg.router.urls[url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)]) {
            url = Pckg.router.urls[_url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)];
        }
        if (!url) {
            return;
        }
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        if (absolute) {
            url = (Pckg.site.url || '') + url;
        }

        return url;
    },

    sluggify: function (str) {
        return str.replace(/[^a-zA-Z0-9 -]/g, '')
            .replace(/[ -]+/g, '-')
            .replace(/^-|-$/g, '')
            .toLowerCase();
    },

    nl2br: function (str, is_xhtml) {
        var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
        return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
    },

    closeIfIframe: function () {
        this.sendToParent('popup.close');
    },

    closeAndRefresh: function () {
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
    },
    groupBy: function (collection, groupBy) {
        grouped = {};
        $.each(collection, function (key, val) {
            grouped[groupBy(val)] ? grouped[groupBy(val)].push(val) : (grouped[groupBy(val)] = [val]);
        });

        return grouped;
    },

    lazyTemplate: function(resolve, obj, url) {
        if (typeof url !== 'string') {
            url = url(obj);
        }
        http.getJSON(url, function(data) {
            obj.template = data.template;
            resolve(obj);
        });
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