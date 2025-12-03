#!/bin/bash

# Nadaj uprawnienia wykonywania wszystkim skryptom pomocniczym

echo "Nadaję uprawnienia wykonywania skryptom..."

chmod +x start-docker.sh
chmod +x start-local.sh
chmod +x diagnoza-obrazy.sh
chmod +x fix-storage-link.sh

echo "✅ Gotowe!"
echo ""
echo "Dostępne skrypty:"
echo "  ./start-docker.sh      - Uruchom projekt w Docker"
echo "  ./start-local.sh       - Uruchom projekt lokalnie"
echo "  ./diagnoza-obrazy.sh   - Diagnozuj problemy z obrazami"
echo "  ./fix-storage-link.sh  - Napraw tylko link symboliczny"
