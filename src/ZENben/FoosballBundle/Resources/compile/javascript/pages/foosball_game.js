var foosball_game = (function(){
    return {
        init: function() {
            $('.btn-join').click(function () {
                var value = $('.comment').val();
                var url = $(this).attr('data-url');
                $('.confirm-participation').loading();
                $.getJSON(url, {comment: value})
                    .done(function (data) {
                        if (data.success) {
                            $('.want-to-join').fadeOut(function () {
                                $('.joined').fadeIn();
                                $('.dummy em').html(data.comment);
                                $('.dummy').slideDown(function () {
                                    $('.dummy').animate({opacity: 1});
                                });
                            });
                        } else {
                            $('.comment').addClass('invalid');
                            $('.want-to-join').fadeIn();
                        }
                    })
                    .fail(function () {
                        $('.want-to-join').fadeIn();
                    })
                    .always(function () {
                        $('.confirm-participation').loading('hide');
                    });
            });

            // gamepage
            function edit_fn(container, data, doneCb) {
            }

            function render_fn(container, data, score) {
                container.append(data.name);
            }

            if (gameStarted == true) {
                $('#brackets').bracket({
                    init: bracketData,
                    skipConsolationRound: true,
                    decorator: {edit: edit_fn, render: render_fn},
                    onMatchClick: onMatchClick,
                    onMatchHover: onMatchHover
                });
            }

            $('.info .btn').click(function () {
                var $me = $(this);
                var $score = $me.closest('.info').find('.score big');
                var val = $score.html() * 1;
                if ($me.hasClass('btn-success')) {
                    $score.html(val + 1);
                } else {
                    $score.html(val - 1);
                }
                var $otherScore = $me.closest('.row').find('.score big').not($score);
                updateScoresDisplay($score, $otherScore);
            });

            function updateScoresDisplay($score, $otherScore) {
                if ($score.html() > $otherScore.html()) {
                    $score.addClass('win');
                    $score.removeClass('lose');
                    $otherScore.addClass('lose');
                    $otherScore.removeClass('win');
                } else if ($score.html() < $otherScore.html()) {
                    $score.addClass('lose');
                    $score.removeClass('win');
                    $otherScore.addClass('win');
                    $otherScore.removeClass('lose');
                } else {
                    $score.removeClass('win lose');
                    $otherScore.removeClass('win lose');
                }
            }

            $('.modal-gamereport').click(function () {
                var $button = $(this).find('.btn.save');
                $button.html($button.attr('data-text-save'));
                $button.attr('data-done', 'false');
            });

            $('.modal-gamereport .btn.save').click(function () {
                var $me = $(this);

                if ($me.attr('data-done') === 'true') {
                    $me.closest('.modal').modal('hide');
                    location.reload();
                    return;
                }

                $button.loading();
                var url = $me.attr('data-url').replace('!id!', $me.attr('data-id'));
                var scoreRed = $me.closest('.modal-content').find('.info-red .score big').html();
                var scoreBlue = $me.closest('.modal-content').find('.info-blue .score big').html();

                $.getJSON(url, {
                    red: scoreRed,
                    blue: scoreBlue
                })
                    .done(function (data) {
                        if (data.success) {
                            $me.attr('data-done', 'true');
                            if (data.won === 1) {
                                $me.html('OK!');
                            } else if (data.won === -1) {
                                $me.html('OK...');
                            } else {
                                $me.html('OK');
                            }
                            var $displayed = $('.modal-gamereport .message span').html(_(data.message,'gamereport'));
                            $displayed.fadeIn();
                        } else {
                            var $displayed = $('.modal-gamereport .message span').html(_(data.message,'gamereport'));
                            $displayed.fadeIn();
                            $me.attr('data-done', 'true');
                            $me.html('OK...');
                        }
                    })
                    .always(function () {
                        $button.loading('hide');
                    });

                return false;
            });

            function onMatchClick(data) {
                $button = $('.modal-gamereport .btn.save');
                $button.html($button.attr('data-text-save'));

                $('.modal-gamereport [data-won]').hide();

                if (data.red.name === '' || data.blue.name === '') {
                    return true;
                }

                $('.gamereport .match-id').html(data.matchId);
                $('.modal-gamereport .title').html(data.matchId);
                $('.modal-gamereport .red').html(data.red.name);
                $('.modal-gamereport .blue').html(data.blue.name);

                $('.modal-gamereport .red-profile-picture').attr('src', data.red.picture);
                $('.modal-gamereport .blue-profile-picture').attr('src', data.blue.picture);

                var scoreRed = data.red.score ? data.red.score : 5;
                var scoreBlue = data.blue.score ? data.blue.score : 5;

                $('.modal-gamereport .info-red .score big').html(scoreRed);
                $('.modal-gamereport .info-blue .score big').html(scoreBlue);

                $('.modal-gamereport .btn.save').attr('data-id', data.id);

                var $score = $($('.modal-gamereport .score').get(1));
                var $otherScore = $($('.modal-gamereport .score').get(2));
                updateScoresDisplay($score, $otherScore);

                $('.modal').modal('show');
            }

            function onMatchHover(data) {

            }

            $button = $('.modal-gamereport .btn.save');
            $button.attr('data-text-save', $button.html());
        }
    };
});
