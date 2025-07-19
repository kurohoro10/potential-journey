<?php
if (isset($_GET['accept-cookies'])) {
    Cookie::put('accept-cookies', 'true', 2592000);
    // Redirect::to('./');
}

if (!Cookie::exists('accept-cookies')) :
?>
<div class="cookie-banner">
    <div class="container">
        <p>
            We use cookie in this website. By using this website, we'll assume you consent to <a href="/cookies">the cookies we set</a>.
        </p>
        <a href="?accept-cookies" class="button">I understand</a>
    </div>
</div>
<?php endif; ?>