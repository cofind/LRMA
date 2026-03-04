<?php
/**
 * Template Name: Kļūt par Biedru
 *
 * Membership landing page — LRM-96
 */
get_header();
?>

<!-- ╔══════════════════════════════════════╗
     ║  HERO                               ║
     ╚══════════════════════════════════════╝ -->
<section class="membership-hero">
	<div class="membership-hero__inner">
		<div class="section-label">Biedri</div>
		<h1 class="membership-hero__title">Kļūt par Biedru</h1>
		<p class="membership-hero__lead">
			Latvijas Rokmūzikas Asociācija apvieno Latvijas roka mūzikas<br>
			autorus, izpildītājus un nozares profesionāļus kopš 2017. gada.
		</p>
		<a href="#pieteikums" class="btn btn-primary">Pieteikties &nbsp;→</a>
	</div>
</section>

<!-- ╔══════════════════════════════════════╗
     ║  BENEFITS                           ║
     ╚══════════════════════════════════════╝ -->
<section class="membership-section">
	<div class="membership-section__inner">
		<div class="section-label">Priekšrocības</div>
		<h2 class="membership-section__title">Ko tu iegūsi</h2>

		<div class="lrma-benefits-grid">

			<div class="lrma-benefit-card">
				<span class="lrma-benefit-icon" aria-hidden="true">◎</span>
				<div class="lrma-benefit-title">Atpazīstamība</div>
				<p class="lrma-benefit-desc">Profils LRMA tīmeklī, iekļaušana asociācijas komunikācijā un medijos.</p>
			</div>

			<div class="lrma-benefit-card">
				<span class="lrma-benefit-icon" aria-hidden="true">◈</span>
				<div class="lrma-benefit-title">Tīklošanās</div>
				<p class="lrma-benefit-desc">Piekļuve LRMA biedru tīklam — kontakti ar citiem mūziķiem, produceriem un nozares pārstāvjiem.</p>
			</div>

			<div class="lrma-benefit-card">
				<span class="lrma-benefit-icon" aria-hidden="true">◇</span>
				<div class="lrma-benefit-title">Atbalsts</div>
				<p class="lrma-benefit-desc">LRMA juridiskais un organizatoriskais atbalsts, informācija par grantu iespējām un nozares aktualitātēm.</p>
			</div>

			<div class="lrma-benefit-card">
				<span class="lrma-benefit-icon" aria-hidden="true">◉</span>
				<div class="lrma-benefit-title">Groover</div>
				<p class="lrma-benefit-desc">Priekšrocības LRMA sadarbības platformā Groover — mūzikas promocija starptautiskajiem kuratoriem.</p>
			</div>

			<div class="lrma-benefit-card">
				<span class="lrma-benefit-icon" aria-hidden="true">◐</span>
				<div class="lrma-benefit-title">Rock Radio</div>
				<p class="lrma-benefit-desc">Prioritāra iekļaušana LRMA Rock Radio rotācijā.</p>
			</div>

			<div class="lrma-benefit-card">
				<span class="lrma-benefit-icon" aria-hidden="true">◆</span>
				<div class="lrma-benefit-title">Balss</div>
				<p class="lrma-benefit-desc">Tiesības balsot LRMA kopsapulcēs un ietekmēt asociācijas virzienu.</p>
			</div>

		</div>
	</div>
</section>

<!-- ╔══════════════════════════════════════╗
     ║  REQUIREMENTS                       ║
     ╚══════════════════════════════════════╝ -->
<section class="membership-section membership-section--requirements">
	<div class="membership-section__inner membership-section__inner--narrow">
		<div class="section-label">Prasības</div>
		<h2 class="membership-section__title">Kas var kļūt par biedru?</h2>
		<p class="membership-req__lead">
			Fiziska persona, kas darbojas Latvijas rokmūzikas jomā —
			mūziķis, dziedātājs, komponists, tekstu autors, producents,
			skaņu režisors vai cits nozares profesionālis.
		</p>
		<ul class="membership-req__list">
			<li>Aktīva darbība rokmūzikas jomā</li>
			<li>Biedra naudas maksājums — 20 EUR/gadā</li>
			<li>Iesniegums un dalībnieku balsojums kopsapulcē</li>
		</ul>
	</div>
</section>

<!-- ╔══════════════════════════════════════╗
     ║  APPLICATION FORM                   ║
     ╚══════════════════════════════════════╝ -->
<section class="membership-form-section" id="pieteikums">
	<div class="membership-section__inner membership-section__inner--narrow">
		<div class="section-label">Iesniegums</div>
		<h2 class="membership-section__title">Pieteikties par biedru</h2>

		<form class="lrma-membership-form" novalidate>
			<div class="lrma-form-row">
				<div class="lrma-form-field">
					<label class="lrma-form-label" for="mbr-name">Vārds, Uzvārds *</label>
					<input class="lrma-form-input" id="mbr-name" name="name" type="text" required autocomplete="name">
				</div>
				<div class="lrma-form-field">
					<label class="lrma-form-label" for="mbr-email">E-pasts *</label>
					<input class="lrma-form-input" id="mbr-email" name="email" type="email" required autocomplete="email">
				</div>
			</div>

			<div class="lrma-form-row">
				<div class="lrma-form-field">
					<label class="lrma-form-label" for="mbr-phone">Tālrunis</label>
					<input class="lrma-form-input" id="mbr-phone" name="phone" type="tel" autocomplete="tel">
				</div>
				<div class="lrma-form-field">
					<label class="lrma-form-label" for="mbr-role">Loma nozarē *</label>
					<select class="lrma-form-select" id="mbr-role" name="role" required>
						<option value="">— izvēlēties —</option>
						<option value="Mūziķis">Mūziķis</option>
						<option value="Dziedātājs">Dziedātājs</option>
						<option value="Komponists">Komponists</option>
						<option value="Producents">Producents</option>
						<option value="Skaņu režisors">Skaņu režisors</option>
						<option value="Cits">Cits</option>
					</select>
				</div>
			</div>

			<div class="lrma-form-field">
				<label class="lrma-form-label" for="mbr-band">Grupa / Projekts</label>
				<input class="lrma-form-input" id="mbr-band" name="band" type="text">
			</div>

			<div class="lrma-form-field">
				<label class="lrma-form-label" for="mbr-social">Sociālie tīkli / Mājaslapa</label>
				<input class="lrma-form-input" id="mbr-social" name="social" type="url" placeholder="https://">
			</div>

			<div class="lrma-form-field">
				<label class="lrma-form-label" for="mbr-message">Papildu informācija</label>
				<textarea class="lrma-form-textarea" id="mbr-message" name="message" placeholder="Kāpēc vēlies kļūt par biedru?"></textarea>
			</div>

			<div class="lrma-form-field lrma-form-field--checkbox">
				<label class="lrma-form-checkbox-label">
					<input type="checkbox" name="consent" required>
					<span>Piekrītu datu apstrādei saskaņā ar <a href="/privatuma-politika/" target="_blank" rel="noopener">privātuma politiku</a> *</span>
				</label>
			</div>

			<div class="lrma-form-field">
				<p class="lrma-membership-error" id="lrma-membership-error" aria-live="polite"></p>
				<button type="submit" class="lrma-form-submit">Iesniegt pieteikumu &nbsp;→</button>
			</div>
		</form>
	</div>
</section>

<?php get_footer(); ?>
