#!/bin/bash

# Ustaw uprawnienia wykonywalne dla wszystkich skryptów

chmod +x deploy-fixes.sh
chmod +x quick-fix.sh  
chmod +x test-fixes.sh

echo "✅ Uprawnienia wykonywalne ustawione dla wszystkich skryptów:"
echo "   - deploy-fixes.sh"
echo "   - quick-fix.sh"
echo "   - test-fixes.sh"
echo ""
echo "Możesz teraz uruchomić deployment:"
echo "   ./deploy-fixes.sh"
