var _ = function(key, group) {
   if (typeof group === 'undefined') {
       group = 'default';
   }
   var translations = {
        'default': {

        },
        'gamereport' : {
            'not_current_round': 'This game is not in the current round, please ask the administrator to adjust the score.',
            'won': 'Congratulations on winning!',
            'lost': 'Better luck next time...',
            'ok': 'Saved!',
            'no_winner': 'You can\'t save this match without a winner.'
        }
   };
    return translations[group][key];
};

jQuery(function($) {
    var page = $('body[data-page]').attr('data-page');
    if (typeof window[page] !== 'undefined') {
        var pageJs = new window[page]();
        pageJs.init();
    }
});

