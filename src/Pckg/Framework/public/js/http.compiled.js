var _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (e) {
    return typeof e
} : function (e) {
    return e && "function" == typeof Symbol && e.constructor === Symbol && e !== Symbol.prototype ? "symbol" : typeof e
}, data = data || {}, http = {
    ajax: function (e, t, n) {
        let o = e.beforeSend || null;
        e.beforeSend = function (e) {
            o && o(e), http.addLocale(e), http.addCsrf(e)
        }, e = Object.assign({dataType: "JSON"}, e || {});
        let r = $.ajax(e), i = n || null;
        return n = function (e) {
            $dispatcher && e.responseJSON && ["Cross Site Request Forbidden", "Cross Site Request Invalid"].indexOf(e.responseJSON.message || "") >= 0 && $dispatcher.$emit("notification:error", "Your session has expired, please refresh your browser"), i && i(e)
        }, r.done ? (t && r.done(t), n && r.fail(n)) : r.then(t || function (e) {
            console.log("no success handler", e)
        }, n || function (e) {
            console.log("no error handler", e)
        }), r
    }, get: function (e, t, n, o) {
        return this.ajax(Object.assign({url: e, type: "GET"}, o || {}), t, n)
    }, post: function (e, t, n, o) {
        return "function" == typeof t && (t = t()), t = http.fixUndefined(t), this.ajax({
            url: e,
            type: "POST",
            data: t
        }, n, o)
    }, patch: function (e, t, n, o) {
        return "function" == typeof t && (t = t()), t = http.fixUndefined(t), this.ajax({
            url: e,
            type: "PATCH",
            data: t
        }, n, o)
    }, put: function (e, t, n, o) {
        return "function" == typeof t && (t = t()), t = http.fixUndefined(t), this.ajax({
            url: e,
            type: "PATCH",
            data: t
        }, n, o)
    }, search: function (e, t, n, o) {
        this.ajax(Object.assign({url: e, type: "SEARCH"}, o || {}), t, n)
    }, delete: function (e, t, n) {
        return this.deleteJSON(e, t, n)
    }, deleteJSON: function (e, t, n) {
        return this.ajax({url: e, type: "DELETE"}, t, n)
    }, getJSON: function (e, t, n, o) {
        return this.get(e, t, n, o)
    }, addCsrf: function (e) {
        var t = document.getElementsByName("pckgvdth");
        0 !== t.length && e.setRequestHeader("X-Pckg-CSRF", t[0].getAttribute("content"))
    }, addLocale: function (e) {
        Pckg && Pckg.config && Pckg.config.locale && Pckg.config.locale.current && e.setRequestHeader("X-Pckg-Locale", Pckg.config.locale.current)
    }, fixUndefined: function (e) {
        return "string" == typeof e || "number" == typeof e ? e : void 0 === e ? null : ($.each(e, function (t, n) {
            Array.isArray(n) || "object" == (void 0 === n ? "undefined" : _typeof(n)) ? e[t] = http.fixUndefined(n) : e[t] = void 0 === n ? "" : !0 === n ? 1 : !1 === n || "" === n || null === n ? null : http.fixUndefined(n)
        }), e)
    }, postDone: function (e) {
        void 0 !== e.redirect && (void 0 !== window.parent && window.parent.location.href.indexOf(e.redirect) ? parent.postMessage("refresh", window.location.origin) : window.location.href = e.redirect)
    }, postError: function (e) {
    }, redirect: function (e) {
        e && void 0 !== e || (e = window.location.href), window.location.href = e
    }
}, locale = {
    price: function (e, t, n) {
        return this.number(e, t) + " " + (n || Pckg.config.locale.currencySign)
    }, number: function (e, t, n) {
        return null == t && (t = Pckg.config.locale.decimals), null == e && (e = 0), parseFloat(e).toLocaleString((n || Pckg.config.locale.current).replace("_", "-").toLowerCase(), {
            currency: "eur",
            currencyDisplay: "symbol",
            maximumFractionDigits: t,
            minimumFractionDigits: t
        })
    }, roundPrice: function (e, t) {
        null == t && (t = Pckg.config.locale.decimals), null == e && (e = 0);
        let n = parseInt(e) == parseFloat(e) ? 0 : t || 2;
        return parseFloat(e).toLocaleString(Pckg.config.locale.current.replace("_", "-").toLowerCase(), {
            currency: "eur",
            currencyDisplay: "symbol",
            maximumFractionDigits: n,
            minimumFractionDigits: n
        }) + " " + Pckg.config.locale.currencySign
    }, roundNumber: function (e, t) {
        return null == t && (t = Pckg.config.locale.decimals), null == e && (e = 0), parseInt(e) == parseFloat(e) ? parseInt(e) : parseFloat(e).toFixed(t || 2)
    }, date: function (e) {
        return e ? (moment.locale(Pckg.config.locale.current), moment(e).format(Pckg.config.locale.format.dateMoment)) : null
    }, time: function (e) {
        return e ? (moment.locale(Pckg.config.locale.current), moment(e).format(Pckg.config.locale.format.timeMoment)) : null
    }, datetime: function (e) {
        return this.date(e) + " " + this.time(e)
    }
}, collection = {
    collect: function (e, t) {
        return e.map(e => new t(e))
    }, groupBy: function (e, t) {
        let n = {};
        return $.each(e, function (e, o) {
            let r = t(o, e);
            n[r] || (n[r] = []), n[r].push(o)
        }), n
    }, map: function (e, t) {
        let n = {};
        return $.each(e, function (e, o) {
            n[e] = collection.getCallableKey(t, o, e)
        }), n
    }, keyBy: function (e, t) {
        let n = {};
        return $.each(e, function (e, o) {
            n[collection.getCallableKey(t, o, e)] = o
        }), n
    }, getCallableKey: function (e, t, n) {
        return "string" == typeof e ? t[e] : e(t, n)
    }
}, utils = {
    is: function (e) {
        return "number" == typeof e ? e.toString().length > 0 : "boolean" == typeof e ? e : "string" == typeof e ? e.length > 0 : !!e
    }, lcfirst: function (e) {
        return e ? e.charAt(0).toLowerCase() + e.slice(1) : ""
    }, ucfirst: function (e) {
        return e ? e.charAt(0).toUpperCase() + e.slice(1) : ""
    }, url: function (e, t, n) {
        if (e && (0 === e.indexOf("@") && Pckg.router.urls[e.substring(1)] && (e = Pckg.router.urls[e.substring(1)]), 0 === e.indexOf("@") && Pckg.router.urls[e.substring(1) + ":" + Pckg.config.locale.current.substring(0, 2)] && (e = Pckg.router.urls[e.substring(1) + ":" + Pckg.config.locale.current.substring(0, 2)]), e)) return $.each(t, function (t, n) {
            e = e.replace("[" + t + "]", n)
        }), 0 === e.indexOf("@/") && (e = e.substring(1)), n && (e = (Pckg.site.url || "") + e), e
    }, sluggify: function (e) {
        return e.replace(/[^a-zA-Z0-9 -]/g, "").replace(/[ -]+/g, "-").replace(/^-|-$/g, "").toLowerCase()
    }, nl2br: function (e, t) {
        return (e + "").replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, "$1" + (t || void 0 === t ? "<br />" : "<br>") + "$2")
    }, html2text: function (e) {
        let t = document.createElement("span");
        return t.innerHTML = e, t.textContent || t.innerText
    }, isIframe: function () {
        return parent.window != window
    }, collect: function (e) {
        return Array.isArray(e) ? e : [e]
    }, sort: function (e) {
        var t = {};
        return Object.keys(e).sort().forEach(function (e, n) {
            t[n] = e
        }), t
    }, next: function (e, t, n) {
        let o = e.indexOf(t) + 1;
        return o < 0 ? o = 0 : o >= e.length && (o = n ? e.length - 1 : 0), e[o]
    }, prev: function (e, t, n) {
        let o = e.indexOf(t) - 1;
        return o < 0 ? o = n ? 0 : e.length - 1 : o >= e.length && (o = e.length - 1), e[o]
    }, sortInt: function (e, t) {
        return e < t ? -1 : e > t ? 1 : 0
    }, splice: function (e, t) {
        let n = e.indexOf(t);
        return -1 === n ? e : e.splice(n, 1)
    }, toggle: function (e, t) {
        e.indexOf(t) >= 0 ? utils.splice(e, t) : e.push(t)
    }, groupBy: function (e, t) {
        return grouped = {}, $.each(e, function (e, n) {
            grouped[t(n)] ? grouped[t(n)].push(n) : grouped[t(n)] = [n]
        }), grouped
    }, lazyTemplate: function (e, t, n) {
        "string" != typeof n && (n = n(t)), http.getJSON(n, function (n) {
            if (t.template = n.template, n.form) {
                let e = t.data;
                t.data = function () {
                    let t = e ? e.call(this) : {};
                    return t.form = n.form, t
                }
            }
            e(t)
        })
    }, base64decode: function (e) {
        return atob(e)
    }, base64encode: function (e) {
        return btoa(e)
    }
}, settings = settings || {};
settings.vue = settings.vue || {}, settings.vue.gmaps = {
    themes: {
        getOptions: function (e) {
            var t = settings.vue.gmaps.themes, n = t.base;
            return void 0 !== t[e] && (n = this.mergeOptions(n, t[e])), n
        }, mergeOptions: function (e, t) {
            return $.each(t, function (t, n) {
                e[t] = n
            }), e
        }, base: {zoom: 10, center: [46.055144, 14.512284]}
    }
};
var d = function (e) {
    console.log(e)
};
window.globalScrollTo = function (e, t) {
    e.length < 1 || (t || (t = 80), "parentIFrame" in window ? parentIFrame.scrollToOffset(0, e.offset().top - t) : (page = $("html,body"), page.on("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove", function () {
        page.stop()
    }), page.animate({scrollTop: e.offset().top - t}, 1e3, function () {
        page.off("scroll mousedown wheel DOMMouseScroll mousewheel keyup touchmove")
    })))
}, window.observerOr = function (e, t) {
    return "undefined" == typeof ResizeObserver ? t() : e()
};
