<script>
    var $buoop = {
        notify:
            {e: -2, f: -4, o: -4, s: -2, c: -4},
        insecure: true,
        unsupported: true,
        api: 5,
        reminder: 24, // show again after 24h
        reminderClosed: 168, // if user closed the message, show it again after one week
        text: "<h1><?= __('Please update your browser') ?></h1><?= __('This tool needs a recent browser to work correctly. Please update.') ?><div class='buorg-update-button-wrapper'><a{up_but} class='btn btn-danger'><?= __('Update browser')?></a></div>"
    };

    function $buo_f() {
        var e = document.createElement("script");
        e.src = "//browser-update.org/update.min.js";
        document.body.appendChild(e);
    }

    try {
        document.addEventListener("DOMContentLoaded", $buo_f, false)
    } catch (e) {
        window.attachEvent("onload", $buo_f)
    }
</script>

<style>
    .buorg {
        border-bottom: 1px solid #f5c6cb;;
        background-color: #f8d7da;
        color: #721c24;
    }

    .buorg > div {
        padding: 1rem;
        font-size: 1rem;
    }

    .buorg div.buorg-update-button-wrapper {
        padding: 1rem 0;
    }
</style>