const data = {};
const http = {

    ajax: function ajax(options, done, error) {
        let presetBeforeSend = options.beforeSend || null;
        options.beforeSend = function (request) {
            if (presetBeforeSend) {
                presetBeforeSend(request);
            }
            http.addLocale(request);
            http.addCsrf(request);
        };

        options = Object.assign({
            dataType: 'JSON'
        }, options || {});

        let a = $.ajax(options);

        let preError = error || null;
        error = function (response) {
            /**
             * Check for CSRF?
             */
            if ($dispatcher && response.responseJSON && ['Cross Site Request Forbidden', 'Cross Site Request Invalid'].indexOf(response.responseJSON.message || '') >= 0) {
                $dispatcher.$emit('notification:error', 'Your session has expired, please refresh your browser');
            }

            if (preError) {
                preError(response);
            }
        };

        if (a.done) {
            if (done) {
                a.done(done);
            }
            if (error) {
                a.fail(error);
            }
        } else {
            a.then(done || function(data){ console.log('no success handler', data); }, error || function(data){ console.log('no error handler', data); });
        }

        return a;
    },

    get: function (url, whenDone, whenError, options) {
        return this.ajax(Object.assign({
            url: url,
            type: 'GET'
        }, options || {}), whenDone, whenError);
    },

    post: function post(url, data, whenDone, whenError) {
        if (typeof data === 'function') {
            data = data();
        }

        data = http.fixUndefined(data);

        return this.ajax({
            url: url,
            type: 'POST',
            data: data
        }, whenDone, whenError);
    },

    patch: function post(url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        data = http.fixUndefined(data);

        return this.ajax({
            url: url,
            type: 'PATCH',
            data: data
        }, whenDone, whenError);
    },

    put: function post(url, data, whenDone, whenError) {
        if (typeof data == 'function') {
            data = data();
        }

        data = http.fixUndefined(data);

        return this.ajax({
            url: url,
            type: 'PATCH',
            data: data
        }, whenDone, whenError);
    },

    search: function get(url, whenDone, whenError, options) {
        this.ajax(Object.assign({
            url: url,
            type: 'SEARCH'
        }, options || {}), whenDone, whenError);
    },

    delete: function (url, whenDone, whenError) {
        return this.deleteJSON(url, whenDone, whenError);
    },

    deleteJSON: function deleteJSON(url, whenDone, whenError) {
        return this.ajax({
            url: url,
            type: 'DELETE'
        }, whenDone, whenError);
    },

    getJSON: function getJSON(url, whenDone, whenError, options) {
        return this.get(url, whenDone, whenError, options);
    },

    addCsrf: function(request) {
        var elements = document.getElementsByName('pckgvdth');
        if (elements.length === 0) {
            return;
        }
        request.setRequestHeader("X-Pckg-CSRF", elements[0].getAttribute('content'));
    },

    addLocale: function(request) {
        if (!Pckg || !Pckg.config || !Pckg.config.locale || !Pckg.config.locale.current) {
            return;
        }

        request.setRequestHeader("X-Pckg-Locale", Pckg.config.locale.current);
    },

    fixUndefined: function (data) {
        if (typeof data === 'string' || typeof data === 'number') {
            return data;
        } else if (typeof data === 'undefined') {
            return null;
        }

        $.each(data, function (key, val) {
            if (Array.isArray(val) || typeof val == 'object') {
                data[key] = http.fixUndefined(val);

            } else if (typeof val == 'undefined') {
                data[key] = '';

            } else if (val === true) {
                data[key] = 1;

            } else if (val === false || val === '' || val === null) {
                data[key] = null;

            } else {
                data[key] = val;
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
    },

    redirect: function (url) {
        if (!url || typeof url == 'undefined') {
            url = window.location.href;
        }

        window.location.href = url;
    }

};

const locale = {

    price: function (price, decimals, currency) {
        return this.number(price, decimals) + ' ' + (currency || Pckg.config.locale.currencySign);
    },

    number: function (price, decimals, locale) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        return parseFloat(price).toLocaleString((locale || Pckg.config.locale.current).replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: decimals,
            minimumFractionDigits: decimals
        });
    },

    roundPrice: function (price, decimals) {
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof price == 'undefined' || price === null) {
            price = 0.0;
        }

        let digits = parseInt(price) == parseFloat(price) ? 0 : (decimals || 2);

        return parseFloat(price).toLocaleString(Pckg.config.locale.current.replace('_', '-').toLowerCase(), {
            currency: 'eur',
            currencyDisplay: 'symbol',
            maximumFractionDigits: digits,
            minimumFractionDigits: digits
        }) + ' ' + Pckg.config.locale.currencySign;
    },

    roundNumber: function(number, decimals){
        if (typeof decimals == 'undefined' || decimals === null) {
            decimals = Pckg.config.locale.decimals;
        }

        if (typeof number == 'undefined' || number === null) {
            number = 0.0;
        }

        return parseInt(number) == parseFloat(number) ? parseInt(number) : parseFloat(number).toFixed(decimals || 2)
    },

    date: function (date) {
        if (!date) {
            return null;
        }

        return new Date(date).toLocaleDateString(Pckg.config.locale.current.split('_').join('-'), { year: 'numeric', month: 'long', day: 'numeric' });
    },

    time: function (time) {
        if (!time) {
            return null;
        }

        return new Date(date).toLocaleTimeString(Pckg.config.locale.current.split('_').join('-'), {hour: '2-digit', minute: '2-digit'});
    },

    datetime: function (datetime) {
        return this.date(datetime) + ' ' + this.time(datetime);
    },

};

const collection = {

    collect: function(items, of) {
        return items.map((item) => { return new of(item) });
    },

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

    map: function (items, map) {
        let mapped = {};

        $.each(items, function (i, item) {
            mapped[i] = collection.getCallableKey(map, item, i);
        });

        return mapped;
    },

    keyBy: function (items, key) {
        let keyed = {};

        $.each(items, function (i, item) {
            keyed[collection.getCallableKey(key, item, i)] = item;
        });

        return keyed;
    },

    getCallableKey: function(key, item, i) {
        if (typeof key === 'string') {
            return item[key];
        }

        return key(item, i);
    },

};

const utils = {

    is: function (val) {
        if (typeof val === 'number') {
            return val.toString().length > 0;
        } else if (typeof val === 'boolean') {
            return val;
        } else if (typeof val === 'string') {
            return val.length > 0;
        }

        return !!val;
    },

    lcfirst: function (str) {
        if (!str) {
            return '';
        }

        return str.charAt(0).toLowerCase() + str.slice(1);
    },

    ucfirst: function(str) {
        if (!str) {
            return '';
        }

        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    url: function (url, params, absolute) {
        if (!url) {
            return;
        }
        if (url.indexOf('@') === 0 && Pckg.router.urls[url.substring(1)]) {
            url = Pckg.router.urls[url.substring(1)];
        }
        if (url.indexOf('@') === 0 && Pckg.router.urls[url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)]) {
            url = Pckg.router.urls[url.substring(1) + ':' + Pckg.config.locale.current.substring(0, 2)];
        }
        if (!url) {
            return;
        }
        $.each(params, function (key, val) {
            url = url.replace('[' + key + ']', val);
        });

        if (url.indexOf('@/') === 0) {
            url = url.substring(1);
        }

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

    html2text: function (html) {
        let span = document.createElement('span');
        span.innerHTML = html;
        return span.textContent || span.innerText;
    },

    isIframe: function () {
        return parent.window != window;
    },

    collect: function (item) {
        return Array.isArray(item)
            ? item
            : [item];
    },
    sort: function (obj) {
        var sorted = {};
        Object.keys(obj).sort().forEach(function (value, key) {
            sorted[key] = value;
        });

        return sorted;
    },
    next: function (all, current, noLoop) {
        let i = all.indexOf(current) + 1;
        if (i < 0) {
            i = 0;
        } else if (i >= all.length) {
            if (noLoop) {
                i = all.length - 1;
            } else {
                i = 0;
            }
        }
        return all[i];
    },
    prev: function (all, current, noLoop) {
        let i = all.indexOf(current) - 1;
        if (i < 0) {
            if (noLoop) {
                i = 0;
            } else {
                i = all.length - 1;
            }
        } else if (i >= all.length) {
            i = all.length - 1;
        }
        return all[i];
    },
    sortInt: function (a, b) {
        return a < b ? -1 : (a > b ? 1 : 0);
    },
    splice: function (collection, item) {
        let index = collection.indexOf(item);
        if (index === -1) {
            return collection;
        }
        return collection.splice(index, 1);
    },
    toggle: function(items, key){
        if (items.indexOf(key) >= 0) {
            utils.splice(items, key);
        } else {
            items.push(key);
        }
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
            if (data.form) {
                let originalData = obj.data;
                obj.data = function () {
                    let d = originalData ? originalData.call(this) : {};
                    d.form = data.form;
                    return d;
                };
            }
            resolve(obj);
        });
    },

    base64decode: function(str) {
        return atob(str);
    },

    base64encode: function(str) {
        return btoa(str);
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

//scroll to function, works on iframe, user can stop it by scrolling
window.globalScrollTo = function(target, offsetTop)
{
    if (target.length < 1) {
        return;
    }

    if (!offsetTop) {
        offsetTop = 80;
    }

    //scroll if iframe
    if ('parentIFrame' in window) {
        parentIFrame.scrollToOffset(0, target.offset().top - offsetTop);
        //scroll if not iframe
    } else {
        let page = $('html,body');

        //stop scrolling if user interacts
        page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
            page.stop();
        });

        page.animate({scrollTop: target.offset().top - offsetTop}, 1000, function () {
            //stop scrolling if user interacts
            page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove");
        });
    }
}

window.observerOr = function(observer, or) {
    return typeof ResizeObserver === 'undefined' ? or() : observer();
}

window.http = http;
window.locale = locale;
window.collection = collection;
window.utils = utils;