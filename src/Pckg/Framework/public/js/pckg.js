/*var Pckg = {};
Pckg.Collection = class Collection extends Array {

    static collect(items, type) {
        var collection = new Pckg.Collection();

        $.each(items, function (i, item) {
            collection.push(new type(item));
        }.bind(this));

        return collection;
    }
};
Pckg.Database = {};
Pckg.Database.Record = class Record {
    constructor(data) {
        $.each(data, function (key, val) {
            this[key] = val;
        }.bind(this));
    }

    $set(key, val) {
        $vue.$set(this, key, val);
    }

    getData() {
        var fields = this.getEntity().getFields();
        var data = {};

        $.each(fields, function (i, field) {
            return data[field] = this[field] ? this[field] : null;
        });

        return data;
    }

    insert(callback) {
        var data = this.getData();

        if (typeof callback == 'undefined') {
            callback = function (data) {
            }
        }

        http.post(this.getUrl('insert'), data, callback);
    }

    getEntity() {
        return new Pckg.Database.Entity();
    }
};
Pckg.Database.Entity = class Entity {
    constructor(data) {
        $.each(data, function (key, val) {
            this[key] = val;
        }.bind(this));
    }

    $set(key, val) {
        $vue.$set(this, key, val);
    }

    getFields() {
        return [];
    }

    getUrl(type, data) {
        if (type == 'insert') {
            return utils.url();
        }
    }

};*/