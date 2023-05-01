export const textEditorHints = tags => {
    return {
        words: tags,
        match: /\B{(\w*)$/,
        search: function (keyword, callback) {
            callback($.grep(this.words, function (item) {
                return item.includes(keyword);
            }));
        }
    }
}