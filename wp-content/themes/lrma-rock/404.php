<?php get_header(); ?>

<div class="error-404">
    <div class="error-code">404</div>
    <h1 class="error-title">Lapa nav atrasta</h1>
    <p class="error-desc">
        Meklētā lapa nepastāv vai ir pārvietota.<br>Pārbaudiet adresi vai atgriezieties sākumlapā.
    </p>
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-red">Uz Sākumu</a>
</div>

<?php get_footer(); ?>
