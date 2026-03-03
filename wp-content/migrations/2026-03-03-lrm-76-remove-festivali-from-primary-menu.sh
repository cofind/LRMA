#!/bin/bash
# LRM-76: Remove stale Festivāli item from primary nav menu
# The correct primary menu should contain exactly 4 items:
#   Jaunumi · Intervijas · Koncerti · Radio
# Festivāli (menu item db_id 24968) was left over and made the nav inconsistent.
# Run ONCE on the server after deploying the code change.
# Written: 2026-03-03

set -e  # stop on any error

echo "🔄 Running LRM-76 migration..."

# Always backup first
sudo -u www-data wp db export /tmp/pre-migration-lrm-76-$(date +%Y%m%d-%H%M).sql --path=/var/www/html/
echo "✅ Backup saved"

# Confirm the item exists before removing it
echo "📋 Current primary menu items:"
sudo -u www-data wp menu item list 18 --path=/var/www/html/ --fields=db_id,title,url

# Remove the Festivāli menu item from the primary menu
sudo -u www-data wp menu item delete 24968 --path=/var/www/html/
echo "✅ Removed Festivāli (db_id 24968) from primary menu"

# Flush rewrite and object cache
sudo -u www-data wp cache flush --path=/var/www/html/
echo "✅ Cache flushed"

echo ""
echo "📋 Primary menu items after migration:"
sudo -u www-data wp menu item list 18 --path=/var/www/html/ --fields=db_id,title,url

echo ""
echo "✅ LRM-76 migration complete."
echo "Verify at: http://207.154.226.128/"
echo "Verify at: http://207.154.226.128/category/koncerti/"
