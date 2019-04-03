var password = {
    // Add another object to the rules array here to add rules.
    // They are executed from top to bottom, with callbacks in between if defined.
    rules: [

        //Take a combination of 12 letters and numbers, both lower and upper case.
        {
            characters: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890',
            max: 5
        },

        //Take 4 special characters, use the callback to shuffle the resulting 16 character string
        {
            characters: '!@#$%^&*()_+|~-={}[]:";<>?,./',
            max: 3,
            callback: function (s) {
                var a = s.split(""),
                    n = a.length;

                for (var i = n - 1; i > 0; i--) {
                    var j = Math.floor(Math.random() * (i + 1));
                    var tmp = a[i];
                    a[i] = a[j];
                    a[j] = tmp;
                }
                return a.join("");
            }
        }
    ],
    generate: function () {
        var g = '';

        $.each(password.rules, function (k, v) {
            var m = v.max;
            for (var i = 1; i <= m; i++) {
                g = g + v.characters[Math.floor(Math.random() * (v.characters.length))];
            }
            if (v.callback) {
                g = v.callback(g);
            }
        });
        return g;
    }
}
