<?php
/**
 * LRMA Quick Tagger — Admin tool for bulk-tagging posts with optional AI analysis
 * Included from functions.php
 */

// ── Register admin submenu ────────────────────────────────────────────────────
add_action( 'admin_menu', function () {
    add_posts_page(
        'Quick Tagger',
        'Quick Tagger',
        'edit_posts',
        'lrma-quick-tagger',
        'lrma_tagger_render_page'
    );
} );

// ── AJAX: save tags ───────────────────────────────────────────────────────────
add_action( 'wp_ajax_lrma_save_tags', function () {
    check_ajax_referer( 'lrma_tagger', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Forbidden', 403 );

    $post_id = intval( $_POST['post_id'] ?? 0 );
    $raw     = sanitize_text_field( $_POST['tags'] ?? '' );
    if ( ! $post_id ) wp_send_json_error( 'Invalid post ID' );

    $tags = array_values( array_unique( array_filter( array_map( 'trim', explode( ',', $raw ) ) ) ) );
    wp_set_post_tags( $post_id, $tags, false );

    $saved = wp_get_post_tags( $post_id, [ 'fields' => 'names' ] );
    wp_send_json_success( [ 'tags' => $saved ] );
} );

// ── AJAX: save categories ─────────────────────────────────────────────────────
add_action( 'wp_ajax_lrma_save_cat', function () {
    check_ajax_referer( 'lrma_tagger', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Forbidden', 403 );

    $post_id = intval( $_POST['post_id'] ?? 0 );
    $cat_ids = array_map( 'intval', (array) ( $_POST['cats'] ?? [] ) );
    if ( ! $post_id ) wp_send_json_error( 'Invalid post ID' );

    wp_set_post_categories( $post_id, $cat_ids );
    wp_send_json_success();
} );

// ── AJAX: save OpenAI API key ─────────────────────────────────────────────────
add_action( 'wp_ajax_lrma_save_ai_key', function () {
    check_ajax_referer( 'lrma_tagger', 'nonce' );
    if ( ! current_user_can( 'manage_options' ) ) wp_die( 'Forbidden', 403 );

    $key = sanitize_text_field( $_POST['api_key'] ?? '' );
    update_option( 'lrma_openai_key', $key );
    wp_send_json_success( [ 'masked' => $key ? substr( $key, 0, 7 ) . '…' . substr( $key, -4 ) : '' ] );
} );

// ── AJAX: AI analyse post ─────────────────────────────────────────────────────
add_action( 'wp_ajax_lrma_ai_analyze', function () {
    check_ajax_referer( 'lrma_tagger', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Forbidden', 403 );

    $post_id = intval( $_POST['post_id'] ?? 0 );
    if ( ! $post_id ) wp_send_json_error( 'Invalid post ID' );

    $api_key = get_option( 'lrma_openai_key', '' );
    if ( ! $api_key ) wp_send_json_error( 'Nav iestatīta OpenAI API atslēga. Ievadi to iestatījumu joslā augšā.' );

    $post    = get_post( $post_id );
    $title   = $post->post_title;
    $body    = wp_strip_all_tags( $post->post_content );
    $body    = preg_replace( '/\s+/', ' ', trim( $body ) );
    $body    = mb_substr( $body, 0, 1800 ); // keep tokens reasonable

    $genres = [
        'Rokmūzika','Metāls','Smagais metāls','Black metal','Death metal','Thrash metal',
        'Power metal','Doom metal','Stoner rock','Post-rock','Punks','Hardcore','Metalcore',
        'Alternatīvais roks','Inditroks','Folk metal','Progresīvais roks','Klasiskais roks','Pop-roks',
    ];

    // Categories with descriptions so AI understands the distinction
    $cat_defs = [
        'Jaunumi'       => 'vispārīgas mūzikas ziņas, paziņojumi, jaunumi',
        'Intervijas'    => 'intervija ar mākslinieku, muzikantu vai grupu',
        'Recenzijas'    => 'albuma, singla vai koncerta recenzija/atsauksme',
        'Koncerti'      => 'koncerta paziņojums, biļešu tirdzniecība, koncerta atskaite',
        'Festivāli'     => 'festivāla ziņas, programma, atskaite',
        'Roka Nemieri'  => 'Roka Nemieri radio raidījuma epizode',
        'Platais Vakars'=> 'Platais Vakars raidījuma epizode',
        'News'          => 'ziņa angļu valodā',
    ];
    $cat_desc_str = implode( '; ', array_map( fn($k,$v) => "$k ($v)", array_keys($cat_defs), $cat_defs ) );

    $prompt = <<<PROMPT
You are an editorial assistant for LRMA.lv — the Latvian Rock Music Association website.

Analyze the article below and return ONLY a valid JSON object (no markdown, no code fences) with these fields:
- "tags_lv": array of 4-8 tags in Latvian — band/artist names, musician names, event names, venue names, Latvian genre words
- "tags_en": array of 4-8 tags in English — same concepts; keep proper nouns as-is, translate genre/descriptor terms
- "genre": array of 1-3 genres chosen strictly from this list: [{$genres_str}]
- "categories": array of 1-3 category names chosen strictly from this list: [{$cat_str}]. Pick ALL that apply. Every article gets at least "Jaunumi" unless it is clearly only a show episode.
- "focus_keyword": one short SEO keyphrase in Latvian (3-6 words), the most important search term for this article
- "meta_description": SEO meta description in Latvian, between 140 and 160 characters, engaging, mentions the main artist or event

Category definitions: {$cat_desc_str}

Article title: {$title}
Article content: {$body}
PROMPT;

    // Splice in lists (PHP heredoc can't call functions inline)
    $prompt = str_replace( '{$genres_str}', implode( ', ', $genres ), $prompt );
    $prompt = str_replace( '{$cat_str}',    implode( ', ', array_keys( $cat_defs ) ), $prompt );
    $prompt = str_replace( '{$cat_desc_str}', $cat_desc_str, $prompt );

    $response = wp_remote_post( 'https://api.openai.com/v1/chat/completions', [
        'timeout' => 30,
        'headers' => [
            'Authorization' => 'Bearer ' . $api_key,
            'Content-Type'  => 'application/json',
        ],
        'body' => wp_json_encode( [
            'model'           => 'gpt-4o-mini',
            'messages'        => [ [ 'role' => 'user', 'content' => $prompt ] ],
            'response_format' => [ 'type' => 'json_object' ],
            'max_tokens'      => 500,
            'temperature'     => 0.2,
        ] ),
    ] );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( $response->get_error_message() );
    }

    $body_raw = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! empty( $body_raw['error'] ) ) {
        wp_send_json_error( $body_raw['error']['message'] ?? 'OpenAI kļūda' );
    }

    $content_str = $body_raw['choices'][0]['message']['content'] ?? '';
    $data        = json_decode( $content_str, true );

    if ( ! $data ) {
        wp_send_json_error( 'Neizdevās parsēt AI atbildi' );
    }

    // Validate returned category names against our known list
    $valid_cats  = array_keys( $cat_defs );
    $ai_cats_raw = array_values( array_filter( (array) ( $data['categories'] ?? [] ) ) );
    $ai_cats     = array_values( array_filter( $ai_cats_raw, fn( $c ) => in_array( $c, $valid_cats, true ) ) );

    wp_send_json_success( [
        'tags_lv'          => array_values( array_filter( (array) ( $data['tags_lv'] ?? [] ) ) ),
        'tags_en'          => array_values( array_filter( (array) ( $data['tags_en'] ?? [] ) ) ),
        'genre'            => array_values( array_filter( (array) ( $data['genre'] ?? [] ) ) ),
        'categories'       => $ai_cats,
        'focus_keyword'    => sanitize_text_field( $data['focus_keyword'] ?? '' ),
        'meta_description' => sanitize_text_field( $data['meta_description'] ?? '' ),
        'tokens'           => $body_raw['usage']['total_tokens'] ?? 0,
    ] );
} );

// ── AJAX: save meta description + focus keyword to Yoast ─────────────────────
add_action( 'wp_ajax_lrma_save_seo', function () {
    check_ajax_referer( 'lrma_tagger', 'nonce' );
    if ( ! current_user_can( 'edit_posts' ) ) wp_die( 'Forbidden', 403 );

    $post_id = intval( $_POST['post_id'] ?? 0 );
    $desc    = sanitize_text_field( $_POST['desc'] ?? '' );
    $focus   = sanitize_text_field( $_POST['focus'] ?? '' );

    if ( ! $post_id ) wp_send_json_error( 'Invalid post ID' );

    if ( $desc )  update_post_meta( $post_id, '_yoast_wpseo_metadesc', $desc );
    if ( $focus ) update_post_meta( $post_id, '_yoast_wpseo_focuskw', $focus );

    wp_send_json_success();
} );

// ── Suggest tags from title (regex, no API needed) ───────────────────────────
function lrma_suggest_tags_from_title( string $title ): array {
    $tags = [];

    $album_ctx = 'albumu|singlu|dziesmu|skaņuplatei|skaņuplate|EP|minialbumu|video|videoklipu|dziesmai';

    // ROKA NEMIERI [Person] [Banda BAND] (date)
    if ( preg_match( '/^ROKA NEMIERI\s+(.+?)\s*\(\d/', $title, $m ) ) {
        $inner = preg_replace( '/^[Gg]rupa\s+/u', '', trim( $m[1] ) );
        if ( preg_match( '/^(.+?)\s+(?:[Gg]rupa\s+)(.+)$/u', $inner, $m2 ) ) {
            $tags[] = trim( $m2[1] );
            $tags[] = trim( $m2[2], ' "„"«»' );
        } elseif ( preg_match( '/^(.+?)\s+([A-ZĀČĒĢĪĶĻŅŠŪŽ\'][A-ZĀČĒĢĪĶĻŅŠŪŽ\s\']{2,})$/', $inner, $m2 ) ) {
            $tags[] = trim( $m2[1] );
            $tags[] = trim( $m2[2] );
        } elseif ( preg_match( '/^(.+?)\s+[„"«"](.+)[„"«"»"]$/u', $inner, $mq ) ) {
            $tags[] = trim( $mq[1] );
            $tags[] = trim( $mq[2] );
        } else {
            $tags[] = $inner;
        }
    }

    // PLATAIS VAKARS [Person] [(Band)] N. daļa (date)
    if ( preg_match( '/^PLATAIS VAKARS\s+(.+?)(?:\s*\(([^)]+)\))?\s+\d+\.\s*daļa/u', $title, $m ) ) {
        $tags[] = trim( $m[1] );
        if ( ! empty( $m[2] ) ) $tags[] = trim( $m[2] );
    }

    // Quoted names — skip if preceded by album/song context word
    preg_match_all( '/(?:(?:' . $album_ctx . ')\s+[„"«"]([^„"«"»"]{2,40})[„"«"»"])|[„"«"]([^„"«"»"]{2,40})[„"«"»"]/u', $title, $qm, PREG_SET_ORDER );
    foreach ( $qm as $match ) {
        if ( empty( $match[1] ) && ! empty( $match[2] ) && str_word_count( $match[2] ) <= 4 ) {
            $tags[] = $match[2];
        }
    }

    // After "Grupa " — band name until verb
    if ( preg_match( '/[Gg]rupa\s+[„"«"]?([A-ZĀČĒĢĪĶĻŅŠŪŽ][A-Za-zāčēģīķļņšūžĀČĒĢĪĶĻŅŠŪŽ.\s\'-]+?)[„"«"»"]?(?:\s+(?:izdod|publicē|laiž|aicina|nāk|atklāj|debitē|prezentē|ar\s|un\s|\())/u', $title, $m ) ) {
        $tags[] = trim( $m[1], ' "„"«»\'' );
    }

    // ALL-CAPS fallback
    $skip_caps = [ 'ROKA', 'NEMIERI', 'PLATAIS', 'VAKARS', 'NULL', 'EP', 'LP', 'MTV', 'GDPR', 'DJ' ];
    if ( empty( $tags ) && preg_match_all( '/\b([A-ZĀČĒĢĪĶĻŅŠŪŽ][A-ZĀČĒĢĪĶĻŅŠŪŽ\'.\-]{2,})\b/', $title, $cm ) ) {
        foreach ( $cm[1] as $cap ) {
            if ( ! in_array( $cap, $skip_caps, true ) ) $tags[] = $cap;
        }
    }

    $tags = array_unique( array_filter( array_map( 'trim', $tags ) ) );
    $tags = array_filter( $tags, function ( $t ) use ( $tags ) {
        $without = preg_replace( '/^[Gg]rupa\s+/u', '', $t );
        return $without === $t || ! in_array( $without, $tags );
    } );

    return array_values( $tags );
}

// ── Render page ───────────────────────────────────────────────────────────────
function lrma_tagger_render_page() {
    $nonce    = wp_create_nonce( 'lrma_tagger' );
    $all_cats = get_categories( [ 'hide_empty' => false ] );

    $filter = sanitize_text_field( $_GET['filter'] ?? 'all' );
    $paged  = max( 1, intval( $_GET['pg'] ?? 1 ) );
    $per    = 50;

    $args = [
        'post_type'      => 'post',
        'post_status'    => 'publish',
        'posts_per_page' => $per,
        'paged'          => $paged,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ];
    if ( $filter === 'notags' ) {
        $args['tax_query'] = [ [ 'taxonomy' => 'post_tag', 'operator' => 'NOT EXISTS' ] ];
    } elseif ( $filter === 'roka' ) {
        $args['s'] = 'ROKA NEMIERI';
    } elseif ( $filter === 'platais' ) {
        $args['s'] = 'PLATAIS VAKARS';
    } elseif ( $filter === 'other' ) {
        $args['post__not_in'] = get_posts( [ 'post_type' => 'post', 'numberposts' => -1, 'fields' => 'ids', 's' => 'ROKA NEMIERI' ] );
    } elseif ( $filter === 'noseo' ) {
        $args['meta_query'] = [ [ 'key' => '_yoast_wpseo_metadesc', 'compare' => 'NOT EXISTS' ] ];
    } elseif ( $filter === 'nocat' ) {
        $specific_slugs = [ 'intervijas', 'recenzijas', 'koncerti', 'festivali', 'roka-nemieri', 'platais-vakars', 'news' ];
        $args['tax_query'] = [
            [ 'taxonomy' => 'category', 'field' => 'slug', 'terms' => $specific_slugs, 'operator' => 'NOT IN' ],
        ];
    }

    $q     = new WP_Query( $args );
    $total = $q->found_posts;
    $pages = ceil( $total / $per );

    // API key status
    $saved_key    = get_option( 'lrma_openai_key', '' );
    $key_masked   = $saved_key ? substr( $saved_key, 0, 7 ) . '…' . substr( $saved_key, -4 ) : '';
    $key_set      = ! empty( $saved_key );

    ?>
    <div class="wrap" id="lrma-tagger">
    <h1>Quick Tagger</h1>

    <style>
    #lrma-tagger { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }

    /* ── AI settings bar ── */
    #lrma-ai-bar { display:flex; align-items:center; gap:10px; background:#f8f5ff; border:1px solid #d0c4f7; border-radius:6px; padding:10px 14px; margin-bottom:16px; flex-wrap:wrap; }
    #lrma-ai-bar .ai-icon { font-size:18px; }
    #lrma-ai-bar label { font-size:13px; font-weight:600; color:#4a1d96; }
    #lrma-ai-bar input[type=password] { border:1px solid #c3c4c7; border-radius:3px; padding:5px 8px; font-size:13px; width:280px; font-family:monospace; }
    #lrma-ai-bar input[type=password]:focus { outline:none; border-color:#7c3aed; box-shadow:0 0 0 2px rgba(124,58,237,.15); }
    #lrma-ai-bar .key-status { font-size:12px; color:#4a1d96; background:#ede9fe; border-radius:3px; padding:2px 8px; font-family:monospace; }
    #lrma-ai-bar .key-status.none { color:#888; background:#f0f0f1; }
    #lrma-ai-bar .btn-ai-setting { background:#7c3aed; color:#fff; border:none; border-radius:3px; padding:5px 12px; font-size:12px; cursor:pointer; }
    #lrma-ai-bar .btn-ai-setting:hover { background:#6d28d9; }
    #lrma-ai-bar .btn-analyze-all { background:#059669; color:#fff; border:none; border-radius:3px; padding:5px 12px; font-size:12px; cursor:pointer; margin-left:auto; }
    #lrma-ai-bar .btn-analyze-all:hover { background:#047857; }
    #lrma-ai-bar .btn-analyze-all:disabled { background:#9ca3af; cursor:default; }
    #lrma-ai-bar .btn-mass-save  { background:#166534; color:#fff; border:none; border-radius:3px; padding:5px 12px; font-size:12px; cursor:pointer; }
    #lrma-ai-bar .btn-mass-save:hover { background:#14532d; }
    #lrma-ai-bar .btn-mass-save:disabled { background:#9ca3af; cursor:default; }
    #lrma-ai-bar .analyze-progress { font-size:12px; color:#059669; }

    /* ── Filters ── */
    #lrma-tagger .filters { display:flex; gap:8px; margin-bottom:16px; flex-wrap:wrap; align-items:center; }
    #lrma-tagger .filters a { padding:5px 12px; border-radius:4px; background:#f0f0f1; color:#1d2327; text-decoration:none; font-size:13px; }
    #lrma-tagger .filters a.active { background:#2271b1; color:#fff; }
    #lrma-tagger .filters .count { color:#666; font-size:13px; margin-left:auto; }

    /* ── Table ── */
    #lrma-tagger table { border-collapse:collapse; width:100%; table-layout:fixed; }
    #lrma-tagger th { background:#f0f0f1; padding:8px 10px; text-align:left; font-size:12px; color:#1d2327; border-bottom:2px solid #c3c4c7; }
    #lrma-tagger td { padding:8px 10px; border-bottom:1px solid #e9e9e9; vertical-align:top; font-size:13px; }
    #lrma-tagger tr:hover > td { background:#f9f9f9; }
    #lrma-tagger .col-title { width:34%; }
    #lrma-tagger .col-date  { width:8%; }
    #lrma-tagger .col-cat   { width:13%; }
    #lrma-tagger .col-tags  { width:45%; }
    #lrma-tagger .post-title a { color:#1d2327; text-decoration:none; font-weight:500; font-size:13px; }
    #lrma-tagger .post-title a:hover { color:#2271b1; }

    /* ── Tag chips ── */
    #lrma-tagger .tag-chip { display:inline-flex; align-items:center; gap:2px; border-radius:3px; padding:2px 7px; font-size:12px; margin:2px; }
    #lrma-tagger .tag-chip.saved    { background:#dff0d8; color:#2d622d; }
    #lrma-tagger .tag-chip.sugg     { background:#e8f0fe; color:#1a56db; cursor:pointer; }
    #lrma-tagger .tag-chip.sugg-lv  { background:#ede9fe; color:#5b21b6; cursor:pointer; }
    #lrma-tagger .tag-chip.sugg-en  { background:#e0f2fe; color:#0369a1; cursor:pointer; }
    #lrma-tagger .tag-chip.sugg-genre { background:#fef3c7; color:#92400e; cursor:pointer; }
    #lrma-tagger .tag-chip .rm { cursor:pointer; font-size:13px; line-height:1; margin-left:2px; color:#aaa; }
    #lrma-tagger .tag-chip .rm:hover { color:#c00; }
    #lrma-tagger .chip-label { font-size:10px; opacity:.65; margin-right:2px; }

    /* ── Inputs / buttons ── */
    #lrma-tagger .tag-input { border:1px solid #c3c4c7; border-radius:3px; padding:4px 7px; font-size:13px; width:150px; margin:2px; }
    #lrma-tagger .tag-input:focus { outline:none; border-color:#2271b1; box-shadow:0 0 0 2px rgba(34,113,177,.2); }
    #lrma-tagger .btn-save { background:#2271b1; color:#fff; border:none; border-radius:3px; padding:5px 10px; font-size:12px; cursor:pointer; white-space:nowrap; }
    #lrma-tagger .btn-save:hover { background:#135e96; }
    #lrma-tagger .btn-save.saved { background:#2d622d; }
    #lrma-tagger .btn-ai { background:#7c3aed; color:#fff; border:none; border-radius:3px; padding:5px 10px; font-size:12px; cursor:pointer; white-space:nowrap; }
    #lrma-tagger .btn-ai:hover { background:#6d28d9; }
    #lrma-tagger .btn-ai.loading { background:#9ca3af; cursor:default; }
    #lrma-tagger .btn-ai.done { background:#059669; }
    #lrma-tagger .btn-seo-save { background:#d97706; color:#fff; border:none; border-radius:3px; padding:4px 9px; font-size:12px; cursor:pointer; }
    #lrma-tagger .btn-seo-save:hover { background:#b45309; }
    #lrma-tagger .btn-seo-save.saved { background:#2d622d; }
    #lrma-tagger .btn-add-all  { background:#0f766e; color:#fff; border:none; border-radius:3px; padding:5px 10px; font-size:12px; cursor:pointer; white-space:nowrap; }
    #lrma-tagger .btn-add-all:hover  { background:#0d5f58; }
    #lrma-tagger .btn-save-all { background:#166534; color:#fff; border:none; border-radius:3px; padding:5px 10px; font-size:12px; cursor:pointer; white-space:nowrap; }
    #lrma-tagger .btn-save-all:hover { background:#14532d; }
    #lrma-tagger .btn-save-all.saved { background:#2d622d; }

    /* ── Category badges ── */
    #lrma-tagger .cat-badge { display:inline-block; background:#f0f0f1; border-radius:3px; padding:2px 6px; font-size:11px; margin:1px; }
    #lrma-tagger .cat-badge.intervijas { background:#fce8e6; color:#8b1a1a; }
    #lrma-tagger .cat-badge.recenzijas { background:#fef9e7; color:#7d6608; }
    #lrma-tagger .cat-badge.koncerti   { background:#e8f4fd; color:#1a5276; }
    #lrma-tagger .cat-badge.festivali  { background:#f0fdf4; color:#166534; }

    /* ── AI result panel ── */
    .ai-result-panel { margin-top:8px; border-top:1px dashed #d0c4f7; padding-top:8px; }
    .ai-result-panel .ai-section { margin-bottom:6px; }
    .ai-result-panel .ai-label { font-size:10px; font-weight:700; color:#7c3aed; text-transform:uppercase; letter-spacing:.05em; margin-right:4px; }
    .ai-result-panel .meta-desc-wrap { margin-top:6px; }
    .ai-result-panel .meta-desc-input { width:100%; box-sizing:border-box; border:1px solid #c3c4c7; border-radius:3px; padding:5px 8px; font-size:12px; resize:vertical; min-height:52px; }
    .ai-result-panel .meta-desc-input:focus { outline:none; border-color:#d97706; box-shadow:0 0 0 2px rgba(217,119,6,.15); }
    .ai-result-panel .char-count { font-size:11px; color:#888; margin-left:6px; }
    .ai-result-panel .char-count.ok  { color:#2d622d; }
    .ai-result-panel .char-count.bad { color:#c00; }
    .ai-result-panel .focus-kw-input { border:1px solid #c3c4c7; border-radius:3px; padding:4px 7px; font-size:12px; width:200px; }
    .ai-result-panel .focus-kw-input:focus { outline:none; border-color:#d97706; box-shadow:0 0 0 2px rgba(217,119,6,.15); }
    .ai-result-panel .ai-error { color:#c00; font-size:12px; }
    .ai-result-panel .ai-tokens { font-size:10px; color:#aaa; margin-left:8px; }

    .saving { opacity:.5; pointer-events:none; }

    /* ── Pagination ── */
    .lrma-pagination { margin:16px 0; display:flex; gap:6px; flex-wrap:wrap; }
    .lrma-pagination a, .lrma-pagination span { padding:5px 10px; border-radius:3px; font-size:13px; background:#f0f0f1; text-decoration:none; color:#1d2327; }
    .lrma-pagination span.current { background:#2271b1; color:#fff; }
    </style>

    <!-- ── AI Settings bar ── -->
    <div id="lrma-ai-bar">
      <span class="ai-icon">🤖</span>
      <label for="lrma-api-key">OpenAI API atslēga:</label>
      <input type="password" id="lrma-api-key" placeholder="sk-…" autocomplete="off">
      <button class="btn-ai-setting" id="btn-save-key">Saglabāt atslēgu</button>
      <span class="key-status <?= $key_set ? '' : 'none' ?>" id="key-status">
        <?= $key_set ? '✓ ' . esc_html( $key_masked ) : 'Nav iestatīta' ?>
      </span>
      <?php if ( $key_set ) : ?>
        <button class="btn-analyze-all" id="btn-analyze-all" title="Analizē visus redzamos rakstus secīgi">⚡ Analizēt visu lapu</button>
        <span class="analyze-progress" id="analyze-progress" style="display:none;"></span>
        <button class="btn-mass-save" id="btn-mass-save-all" title="Pievienot visus tagus un saglabāt — tagi + SEO — visiem rakstiem lapā">💾 Saglabāt visu lapu</button>
        <span class="analyze-progress" id="mass-save-progress" style="display:none;"></span>
      <?php endif; ?>
    </div>

    <?php
    $base_url = admin_url( 'edit.php?page=lrma-quick-tagger' );
    $filters  = [
        'all'     => 'Visi (' . wp_count_posts()->publish . ')',
        'nocat'   => 'Tikai Jaunumi',
        'notags'  => 'Bez tagiem',
        'noseo'   => 'Bez SEO apraksta',
        'roka'    => 'Roka Nemieri',
        'platais' => 'Platais Vakars',
        'other'   => 'Citi',
    ];
    echo '<div class="filters">';
    foreach ( $filters as $key => $label ) {
        $cls = $filter === $key ? ' active' : '';
        echo '<a href="' . esc_url( $base_url . '&filter=' . $key ) . '" class="' . $cls . '">' . esc_html( $label ) . '</a>';
    }
    echo '<span class="count">Redzami: ' . $q->post_count . ' / ' . $total . '</span>';
    echo '</div>';
    ?>

    <table>
    <thead>
      <tr>
        <th class="col-title">Virsraksts</th>
        <th class="col-date">Datums</th>
        <th class="col-cat">Kategorija</th>
        <th class="col-tags">Tagi + AI</th>
      </tr>
    </thead>
    <tbody>
    <?php while ( $q->have_posts() ) : $q->the_post();
        $pid       = get_the_ID();
        $title     = get_the_title();
        $date      = get_the_date( 'd.m.Y' );
        $edit_link = get_edit_post_link( $pid );
        $cur_tags  = wp_get_post_tags( $pid, [ 'fields' => 'names' ] );
        $suggested = lrma_suggest_tags_from_title( $title );
        $new_sugg  = array_diff( $suggested, $cur_tags );
        $post_cats = wp_get_post_categories( $pid );
        $has_meta  = (bool) get_post_meta( $pid, '_yoast_wpseo_metadesc', true );
    ?>
    <tr data-pid="<?= $pid ?>">
      <td class="col-title">
        <div class="post-title"><a href="<?= esc_url( $edit_link ) ?>" target="_blank"><?= esc_html( $title ) ?></a></div>
        <?php if ( $has_meta ) echo '<span style="font-size:10px;color:#059669;">✓ SEO meta</span>'; ?>
      </td>
      <td class="col-date" style="color:#666;font-size:12px;padding-top:10px;"><?= $date ?></td>
      <td class="col-cat">
        <?php foreach ( $all_cats as $cat ) :
            $checked   = in_array( $cat->term_id, $post_cats ) ? 'checked' : '';
            $slug      = $cat->slug;
            $badge_cls = in_array( $slug, ['intervijas','recenzijas','koncerti','festivali'] ) ? ' ' . $slug : '';
        ?>
        <label style="display:flex;align-items:center;gap:4px;font-size:12px;margin:1px 0;cursor:pointer;">
          <input type="checkbox" class="cat-cb" value="<?= $cat->term_id ?>" data-name="<?= esc_attr( $cat->name ) ?>" <?= $checked ?>>
          <span class="cat-badge<?= $badge_cls ?>"><?= esc_html( $cat->name ) ?></span>
        </label>
        <?php endforeach; ?>
        <button class="btn-save btn-cat-save" style="margin-top:5px;">Saglabāt</button>
      </td>
      <td class="col-tags">
        <div class="tag-area">
          <!-- Saved + title-suggested chips -->
          <div class="tag-chips">
            <?php foreach ( $cur_tags as $tag ) : ?>
              <span class="tag-chip saved"><?= esc_html( $tag ) ?><span class="rm">×</span></span>
            <?php endforeach; ?>
            <?php foreach ( $new_sugg as $s ) : ?>
              <span class="tag-chip sugg" title="Ieteikums no virsraksta — klikšķini lai pievienotu">+ <?= esc_html( $s ) ?></span>
            <?php endforeach; ?>
          </div>
          <!-- Input row -->
          <div style="display:flex;gap:4px;margin-top:5px;flex-wrap:wrap;align-items:center;">
            <input type="text" class="tag-input" placeholder="Pievienot tagus, ar komatu…">
            <button class="btn-save btn-tag-save">Saglabāt</button>
            <button class="btn-add-all" title="Pievieno visus ieteiktos tagus (arī AI)">+ Visi tagi</button>
            <button class="btn-save-all" title="Pievieno visus tagus un saglabā — tagus + SEO meta">💾 Saglabāt visu</button>
            <button class="btn-ai btn-ai-analyze">🤖 AI</button>
          </div>
          <!-- AI results panel (hidden initially) -->
          <div class="ai-result-panel" style="display:none;"></div>
        </div>
      </td>
    </tr>
    <?php endwhile; wp_reset_postdata(); ?>
    </tbody>
    </table>

    <?php if ( $pages > 1 ) : ?>
    <div class="lrma-pagination">
      <?php for ( $i = 1; $i <= $pages; $i++ ) :
          $url = esc_url( $base_url . '&filter=' . $filter . '&pg=' . $i );
          if ( $i === $paged ) echo '<span class="current">' . $i . '</span>';
          else echo '<a href="' . $url . '">' . $i . '</a>';
      endfor; ?>
    </div>
    <?php endif; ?>

    </div><!-- .wrap -->

    <script>
    (function(){
      const NONCE = <?= json_encode( $nonce ) ?>;

      // ── Save API key ──────────────────────────────────────────────────────
      document.getElementById('btn-save-key')?.addEventListener('click', function() {
        const key = document.getElementById('lrma-api-key').value.trim();
        if ( !key ) return;
        this.textContent = 'Saglabā…';
        fetch(ajaxurl, {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({ action:'lrma_save_ai_key', nonce:NONCE, api_key:key })
        }).then(r=>r.json()).then(d=>{
          if (d.success) {
            document.getElementById('key-status').textContent = '✓ ' + d.data.masked;
            document.getElementById('key-status').classList.remove('none');
            document.getElementById('lrma-api-key').value = '';
            this.textContent = 'Saglabāts ✓';
            // Show "analyze all" button if not visible
            if (!document.getElementById('btn-analyze-all')) location.reload();
          }
          setTimeout(()=>{ this.textContent='Saglabāt atslēgu'; }, 2500);
        });
      });

      // ── Build AI result HTML ──────────────────────────────────────────────
      function buildAiPanel(data) {
        const chipHtml = (tags, cls, prefix) => tags.map(t =>
          `<span class="tag-chip ${cls}" title="Klikšķini lai pievienotu"><span class="chip-label">${prefix}</span>${esc(t)}</span>`
        ).join('');

        const lvHtml    = chipHtml(data.tags_lv, 'sugg-lv', 'LV');
        const enHtml    = chipHtml(data.tags_en, 'sugg-en', 'EN');
        const genreHtml = chipHtml(data.genre,   'sugg-genre', '♪');

        const descLen  = (data.meta_description||'').length;
        const lenClass = descLen >= 140 && descLen <= 160 ? 'ok' : (descLen > 0 ? 'bad' : '');
        const tokens   = data.tokens ? `<span class="ai-tokens">${data.tokens} tokens</span>` : '';

        const catHtml = (data.categories||[]).map(c =>
          `<span class="ai-cat-badge">${esc(c)}</span>`
        ).join('');

        return `
          <div class="ai-result-panel">
            <div class="ai-section">
              ${lvHtml}${enHtml}
            </div>
            ${data.genre.length ? `<div class="ai-section"><span class="ai-label">Žanrs</span>${genreHtml}</div>` : ''}
            ${catHtml ? `<div class="ai-section"><span class="ai-label">AI kategorijas</span>${catHtml}</div>` : ''}
            <div class="ai-section meta-desc-wrap">
              <span class="ai-label">Focus keyword</span>
              <input type="text" class="focus-kw-input" value="${esc(data.focus_keyword)}" placeholder="fokusa atslēgvārds">
            </div>
            <div class="ai-section meta-desc-wrap">
              <span class="ai-label">Meta apraksts</span>
              <span class="char-count ${lenClass}" id="cc">${descLen}/160</span>${tokens}<br>
              <textarea class="meta-desc-input" rows="2" maxlength="170">${esc(data.meta_description)}</textarea>
              <div style="margin-top:4px;">
                <button class="btn-seo-save">💾 Saglabāt SEO</button>
              </div>
            </div>
          </div>`;
      }

      // ── Apply AI-suggested categories to the checkbox column ─────────────
      function applyAiCategories(row, categories) {
        if ( !categories || !categories.length ) return;
        categories.forEach(name => {
          const cb = [...row.querySelectorAll('.cat-cb')]
                       .find(c => c.dataset.name === name);
          if ( cb ) cb.checked = true;
        });
      }

      function esc(s) {
        return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
      }

      // ── Run AI on one row ─────────────────────────────────────────────────
      function analyzeRow(row) {
        return new Promise((resolve, reject) => {
          const pid    = row.dataset.pid;
          const btn    = row.querySelector('.btn-ai-analyze');
          const area   = row.querySelector('.tag-area');
          const panel  = row.querySelector('.ai-result-panel');

          btn.classList.add('loading');
          btn.textContent = '⏳ …';
          area.classList.add('saving');

          fetch(ajaxurl, {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({ action:'lrma_ai_analyze', nonce:NONCE, post_id:pid })
          })
          .then(r=>r.json())
          .then(d=>{
            btn.classList.remove('loading');
            area.classList.remove('saving');
            if ( d.success ) {
              btn.classList.add('done');
              btn.textContent = '🤖 ✓';
              // Replace or create panel
              const existing = row.querySelector('.ai-result-panel');
              existing.outerHTML = buildAiPanel(d.data);
              row.querySelector('.ai-result-panel').style.display = 'block';
              // Auto-check suggested categories
              applyAiCategories(row, d.data.categories);
              resolve();
            } else {
              btn.textContent = '🤖 AI';
              const existing = row.querySelector('.ai-result-panel');
              existing.innerHTML = `<span class="ai-error">Kļūda: ${esc(d.data)}</span>`;
              existing.style.display = 'block';
              reject(d.data);
            }
          })
          .catch(err => {
            btn.classList.remove('loading');
            btn.textContent = '🤖 AI';
            area.classList.remove('saving');
            reject(err);
          });
        });
      }

      // ── Save all tags + SEO for one row (reusable) ───────────────────
      async function saveAllRow(row) {
        const pid      = row.dataset.pid;
        const chipArea = row.querySelector('.tag-chips');
        const btn      = row.querySelector('.btn-save-all');

        // Accept every pending suggestion chip
        [...row.querySelectorAll('.tag-chip.sugg, .tag-chip.sugg-lv, .tag-chip.sugg-en, .tag-chip.sugg-genre')].forEach(chip => {
          const labelEl = chip.querySelector('.chip-label');
          const name = (labelEl ? chip.textContent.replace(labelEl.textContent,'') : chip.textContent)
                        .replace(/^\+\s*/,'').trim();
          chip.className = 'tag-chip saved';
          chip.innerHTML = esc(name) + '<span class="rm">×</span>';
          if ( !chip.closest('.tag-chips') ) chipArea.appendChild(chip);
        });

        // Collect tags
        const chips = [...chipArea.querySelectorAll('.tag-chip.saved')].map(c => {
          const rm = c.querySelector('.rm');
          return rm ? c.textContent.replace(rm.textContent,'').trim() : c.textContent.trim();
        });
        const input   = row.querySelector('.tag-input');
        const extra   = input.value.split(',').map(s=>s.trim()).filter(Boolean);
        const allTags = [...chips, ...extra].filter(Boolean);

        // Collect SEO fields if AI panel is open
        const panel  = row.querySelector('.ai-result-panel');
        const hasSeo = panel && panel.style.display !== 'none';
        const desc   = hasSeo ? (panel.querySelector('.meta-desc-input')?.value || '') : '';
        const focus  = hasSeo ? (panel.querySelector('.focus-kw-input')?.value  || '') : '';

        if ( btn ) { btn.classList.add('saving'); btn.textContent = '⏳ …'; }

        const saveTagsP = fetch(ajaxurl, {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body: new URLSearchParams({ action:'lrma_save_tags', nonce:NONCE, post_id:pid, tags:allTags.join(',') })
        }).then(r=>r.json());

        const saveSeoP = (hasSeo && (desc || focus))
          ? fetch(ajaxurl, {
              method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
              body: new URLSearchParams({ action:'lrma_save_seo', nonce:NONCE, post_id:pid, desc, focus })
            }).then(r=>r.json())
          : Promise.resolve({ success:true });

        // Save whichever category checkboxes are currently ticked (including AI-suggested ones)
        const catParams = new URLSearchParams({ action:'lrma_save_cat', nonce:NONCE, post_id:pid });
        [...row.querySelectorAll('.cat-cb:checked')].forEach(c => catParams.append('cats[]', c.value));
        const saveCatP = fetch(ajaxurl, {
          method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
          body: catParams
        }).then(r=>r.json());

        const [tagRes, seoRes, catRes] = await Promise.all([saveTagsP, saveSeoP, saveCatP]);

        if ( btn ) btn.classList.remove('saving');

        if ( tagRes.success ) {
          chipArea.innerHTML = tagRes.data.tags.map(t =>
            `<span class="tag-chip saved">${esc(t)}<span class="rm">×</span></span>`
          ).join('');
          input.value = '';
        }

        if ( seoRes.success && hasSeo && (desc || focus) ) {
          const titleCell = row.querySelector('.col-title');
          if ( !titleCell.querySelector('.seo-ok') ) {
            titleCell.insertAdjacentHTML('beforeend','<span class="seo-ok" style="font-size:10px;color:#059669;display:block;">✓ SEO meta</span>');
          }
          const seoBtn = panel?.querySelector('.btn-seo-save');
          if ( seoBtn ) {
            seoBtn.classList.add('saved'); seoBtn.textContent = '💾 Saglabāts ✓';
            setTimeout(()=>{ seoBtn.classList.remove('saved'); seoBtn.textContent='💾 Saglabāt SEO'; }, 2000);
          }
        }

        if ( catRes.success ) {
          const catSaveBtn = row.querySelector('.btn-cat-save');
          if ( catSaveBtn ) {
            catSaveBtn.classList.add('saved'); catSaveBtn.textContent = '✓ Saglabāts';
            setTimeout(()=>{ catSaveBtn.classList.remove('saved'); catSaveBtn.textContent='Saglabāt'; }, 2000);
          }
        }

        if ( btn ) {
          btn.classList.add('saved'); btn.textContent = '💾 Viss saglabāts ✓';
          setTimeout(()=>{ btn.classList.remove('saved'); btn.textContent='💾 Saglabāt visu'; }, 2500);
        }
      }

      // ── "Analyze all" button: sequential queue ───────────────────────────
      document.getElementById('btn-analyze-all')?.addEventListener('click', async function() {
        const rows     = [...document.querySelectorAll('#lrma-tagger tbody tr')];
        const progress = document.getElementById('analyze-progress');
        this.disabled  = true;
        progress.style.display = 'inline';

        let done = 0;
        for (const row of rows) {
          const btn = row.querySelector('.btn-ai-analyze');
          if ( btn && !btn.classList.contains('done') ) {
            progress.textContent = `Analizē ${++done}/${rows.length}…`;
            try { await analyzeRow(row); } catch(e) { /* continue on error */ }
            await new Promise(r => setTimeout(r, 300)); // small pause between requests
          }
        }
        progress.textContent = `Pabeigts! ${done} raksti analizēti.`;
        this.disabled = false;
      });

      // ── "Save all page" button: sequential queue ──────────────────────
      document.getElementById('btn-mass-save-all')?.addEventListener('click', async function() {
        const rows     = [...document.querySelectorAll('#lrma-tagger tbody tr')];
        const progress = document.getElementById('mass-save-progress');
        this.disabled  = true;
        progress.style.display = 'inline';

        let done = 0;
        for (const row of rows) {
          progress.textContent = `Saglabā ${++done}/${rows.length}…`;
          try { await saveAllRow(row); } catch(e) { /* continue on error */ }
          await new Promise(r => setTimeout(r, 150));
        }
        progress.textContent = `Pabeigts! ${done} raksti saglabāti.`;
        this.disabled = false;
      });

      // ── Delegated click handler ───────────────────────────────────────────
      document.addEventListener('click', function(e) {

        // Remove chip
        if ( e.target.classList.contains('rm') ) {
          e.target.closest('.tag-chip').remove();
          return;
        }

        // Accept suggested chip (any variant)
        if ( e.target.closest('.tag-chip.sugg, .tag-chip.sugg-lv, .tag-chip.sugg-en, .tag-chip.sugg-genre') ) {
          const chip = e.target.closest('.tag-chip');
          if ( chip.classList.contains('sugg') || chip.classList.contains('sugg-lv') ||
               chip.classList.contains('sugg-en') || chip.classList.contains('sugg-genre') ) {
            const labelEl = chip.querySelector('.chip-label');
            const name = (labelEl ? chip.textContent.replace(labelEl.textContent,'') : chip.textContent)
                          .replace(/^\+\s*/,'').trim();
            chip.className = 'tag-chip saved';
            chip.innerHTML = esc(name) + '<span class="rm">×</span>';
            // Move it to the main tag-chips div
            const row = chip.closest('tr');
            if ( row ) {
              const chipArea = row.querySelector('.tag-chips');
              if ( chipArea && !chip.closest('.tag-chips') ) chipArea.appendChild(chip);
            }
            return;
          }
        }

        // ── Accept all suggested chips in this row ───────────────────────
        if ( e.target.classList.contains('btn-add-all') ) {
          const row      = e.target.closest('tr');
          const chipArea = row.querySelector('.tag-chips');
          const suggs    = [...row.querySelectorAll('.tag-chip.sugg, .tag-chip.sugg-lv, .tag-chip.sugg-en, .tag-chip.sugg-genre')];
          suggs.forEach(chip => {
            const labelEl = chip.querySelector('.chip-label');
            const name = (labelEl ? chip.textContent.replace(labelEl.textContent,'') : chip.textContent)
                          .replace(/^\+\s*/,'').trim();
            chip.className = 'tag-chip saved';
            chip.innerHTML = esc(name) + '<span class="rm">×</span>';
            if ( !chip.closest('.tag-chips') ) chipArea.appendChild(chip);
          });
          return;
        }

        // ── Accept all + save tags + save SEO in one click ───────────────
        if ( e.target.classList.contains('btn-save-all') ) {
          saveAllRow( e.target.closest('tr') );
          return;
        }

        // AI button
        if ( e.target.classList.contains('btn-ai-analyze') ) {
          const row = e.target.closest('tr');
          analyzeRow(row).catch(()=>{});
          return;
        }

        // Save tags
        if ( e.target.classList.contains('btn-tag-save') ) {
          const row   = e.target.closest('tr');
          const pid   = row.dataset.pid;
          const area  = row.querySelector('.tag-area');
          const chips = [...row.querySelectorAll('.tag-chips .tag-chip.saved')].map(c => {
            const rm = c.querySelector('.rm');
            return rm ? c.textContent.replace(rm.textContent,'').trim() : c.textContent.trim();
          });
          const input = row.querySelector('.tag-input');
          const extra = input.value.split(',').map(s=>s.trim()).filter(Boolean);
          const all   = [...chips, ...extra].filter(Boolean);

          area.classList.add('saving');
          fetch(ajaxurl, {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({ action:'lrma_save_tags', nonce:NONCE, post_id:pid, tags:all.join(',') })
          }).then(r=>r.json()).then(d=>{
            area.classList.remove('saving');
            if ( d.success ) {
              row.querySelector('.tag-chips').innerHTML = d.data.tags.map(t =>
                `<span class="tag-chip saved">${esc(t)}<span class="rm">×</span></span>`
              ).join('');
              input.value = '';
              const btn = e.target;
              btn.classList.add('saved'); btn.textContent = '✓ Saglabāts';
              setTimeout(()=>{ btn.classList.remove('saved'); btn.textContent='Saglabāt'; }, 2000);
            }
          });
          return;
        }

        // Save categories
        if ( e.target.classList.contains('btn-cat-save') ) {
          const row  = e.target.closest('tr');
          const pid  = row.dataset.pid;
          const cats = [...row.querySelectorAll('.cat-cb:checked')].map(c=>c.value);
          const btn  = e.target;
          btn.classList.add('saving');
          const params = new URLSearchParams({ action:'lrma_save_cat', nonce:NONCE, post_id:pid });
          cats.forEach(c => params.append('cats[]', c));
          fetch(ajaxurl, { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:params })
            .then(r=>r.json()).then(d=>{
              btn.classList.remove('saving');
              if ( d.success ) {
                btn.classList.add('saved'); btn.textContent='✓ Saglabāts';
                setTimeout(()=>{ btn.classList.remove('saved'); btn.textContent='Saglabāt'; }, 2000);
              }
            });
          return;
        }

        // Save SEO (meta description + focus keyword)
        if ( e.target.classList.contains('btn-seo-save') ) {
          const row   = e.target.closest('tr');
          const pid   = row.dataset.pid;
          const panel = row.querySelector('.ai-result-panel');
          const desc  = panel.querySelector('.meta-desc-input')?.value || '';
          const focus = panel.querySelector('.focus-kw-input')?.value || '';
          const btn   = e.target;
          btn.classList.add('saving');
          fetch(ajaxurl, {
            method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'},
            body: new URLSearchParams({ action:'lrma_save_seo', nonce:NONCE, post_id:pid, desc, focus })
          }).then(r=>r.json()).then(d=>{
            btn.classList.remove('saving');
            if ( d.success ) {
              btn.classList.add('saved'); btn.textContent='💾 Saglabāts ✓';
              // Show SEO indicator in title column
              const titleCell = row.querySelector('.col-title');
              if ( !titleCell.querySelector('.seo-ok') ) {
                titleCell.insertAdjacentHTML('beforeend','<span class="seo-ok" style="font-size:10px;color:#059669;display:block;">✓ SEO meta</span>');
              }
              setTimeout(()=>{ btn.classList.remove('saved'); btn.textContent='💾 Saglabāt SEO'; }, 2500);
            }
          });
          return;
        }
      });

      // ── Meta description char counter ─────────────────────────────────────
      document.addEventListener('input', function(e) {
        if ( e.target.classList.contains('meta-desc-input') ) {
          const len = e.target.value.length;
          const cc  = e.target.closest('.ai-result-panel')?.querySelector('.char-count');
          if ( cc ) {
            cc.textContent = len + '/160';
            cc.className = 'char-count ' + (len >= 140 && len <= 160 ? 'ok' : 'bad');
          }
        }
      });

      // ── Enter key saves tags ──────────────────────────────────────────────
      document.addEventListener('keydown', function(e) {
        if ( e.key === 'Enter' && e.target.classList.contains('tag-input') ) {
          e.preventDefault();
          e.target.closest('tr').querySelector('.btn-tag-save').click();
        }
      });
    })();
    </script>
    <?php
}
