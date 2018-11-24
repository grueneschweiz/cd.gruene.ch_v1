<script>
    (function ($) {
        var setLoginPosition = function () {
            var $footer = $('.footer'),
                $loginBox = $('.login-box'),
                $window = $(window),
                $body = $('body');

            if ($footer.outerHeight(true) + $loginBox.outerHeight(true) > $window.height()) {
                $body.height($footer.outerHeight(true) + $loginBox.outerHeight(true));
            }

            $loginBox.css('margin-top', function () {
                var spare = $window.height() - $loginBox.outerHeight(true) - $footer.outerHeight(true);

                if (spare > 0) {
                    return spare / 2;
                }
            });
        };

        setLoginPosition();

    })($);
</script>