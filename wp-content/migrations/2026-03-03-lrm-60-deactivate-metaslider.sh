#!/bin/bash
# LRM-60: Deactivate MetaSlider plugin (replaced by custom ACF hero slider)
# Run ONCE on the server after deploying the LRM-60 branch.
# Written: 2026-03-03
#
# Background: The front-page hero slider was rebuilt as a custom solution
# (template-parts/hero-slider.php) that mixes auto-pulled article slides
# with manually curated ACF promo slides. MetaSlider is no longer referenced
# anywhere in the lrma-rock theme and can be safely deactivated.

set -e

echo "🔄 Running LRM-60 migration: deactivate MetaSlider..."

sudo -u www-data wp plugin deactivate ml-slider --path=/var/www/html/

echo "✅ MetaSlider deactivated."
echo "Verify site: http://207.154.226.128/"
echo ""
echo "Note: Promo slides (Type B) require ACF Pro to be installed."
echo "      Until then, the slider auto-populates from latest articles only."
