<?php

session_set_cookie_params(60 * 60 * 24, null, null, false, true);
session_start();
//session_destroy();

error_reporting(0);
ini_set('display_errors', 0);

main();

function main()
{
    if (isset($_POST['dgfshagfdgfdgfdgfd']) && $_POST['dgfshagfdgfdgfdgfd'] === $_SESSION['iamnotarobot']) {
        template('check');
        exit;
    }

    template('form');
}

function check()
{
    $url = filter_input(INPUT_POST, 'gdfsgdfsgfdgfd', FILTER_SANITIZE_URL);

    echo '<h2>Checking site</h2>';

    switch(isvulnerable($url)) {

        case 2:
            echo '<p>Amasty Feed not installed or not found.</p>';
            break;
        case 1:
            echo '<p style="color:red;">Amasty Feed vulnerable, patch today!<br>';
            echo 'Contact <a href="https://amasty.com/product-feed.html">Amasty</a> to see how you can fix this.</p>';
            break;

        case 0:
            echo '<p style="color: green;">Amasty Feed not vulnerable, you are safe!</p>';
            break;
    }

    echo '<br><a href="/">Try another</a>';

    // Allow once per submission
    createnew();
}

function isvulnerable($url)
{
    if (requesthead($url . '/js/amasty/amfeed/amfeed.js') !== 200) {
        // Not found
        return 2;
    }

    if (requesthead($url . '/amfeed/main/download/?file=../../../app/Mage.php') !== 200) {
        // Not vulnerable
        return 0;
    }

    return 1;
}

function requesthead($url, $info = CURLINFO_HTTP_CODE)
{
    $curl = curl_init();

    curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOBODY => true,
            CURLOPT_HEADER => true,
    ]);

    curl_exec($curl);
    $info = curl_getinfo($curl, $info);
    curl_close($curl);

    return $info;
}

function createnew() {
    $_SESSION['iamnotarobot'] = sha1(mt_rand(0, 99999999999999));
}

function form() {
    createnew();

    ?>

    <form action="" method="post">
        <h2>Check your site by entering the base url!</h2>
        <input type="hidden" name="dgfshagfdgfdgfdgfd" value="<?php echo $_SESSION['iamnotarobot']; ?>"/>
        <input type="text" name="gdfsgdfsgfdgfd" value=""/>
        <input type="submit" value="Check!"/>
    </form>
    <?php
}

function template($callback)
{
    ?>
    <html>
    <head>
        <title>Amasty Product Feed - Local File Disclosure check</title>
    </head>
    <body>
        <h1>Amasty Product Feed - Local File Disclosure check</h1>
        <p>
            As of today Amasty Feed for Magento is vulnerable to local file disclosure.<br>
            More information on this can be found <a href="https://gist.github.com/JeroenBoersma/87b7c996f66b96b2a24d8977b1b165ac">here</a><br>
            Amasty already supplied a valid patch on this matter, <a href="https://amasty.com/contacts/">contact them</a> if you need the latest version of the module.
        </p>
        <?php echo call_user_func($callback); ?>

    </body>
    </html>
    <?php
}

