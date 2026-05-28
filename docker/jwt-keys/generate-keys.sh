#!/bin/bash
# ============================================================
# FitLife ERP — RSA Key Pair Generator for JWT Authentication
# ============================================================
# Run this script ONCE before your first `docker compose up`.
# It generates the RSA-2048 key pair used for signing (Auth Service)
# and verifying (all other services) JWT tokens.
#
# Usage:
#   bash docker/jwt-keys/generate-keys.sh
# ============================================================

set -e

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

if [ -f "$SCRIPT_DIR/private.pem" ]; then
    echo "⚠️  Keys already exist at $SCRIPT_DIR/"
    echo "   Delete private.pem and public.pem first if you want to regenerate."
    exit 0
fi

echo "🔑 Generating RSA-2048 key pair for JWT signing..."
openssl genrsa -out "$SCRIPT_DIR/private.pem" 2048
openssl rsa -in "$SCRIPT_DIR/private.pem" -pubout -out "$SCRIPT_DIR/public.pem"

chmod 600 "$SCRIPT_DIR/private.pem"
chmod 644 "$SCRIPT_DIR/public.pem"

echo ""
echo "✅ Keys generated successfully at $SCRIPT_DIR/"
echo "   🔒 private.pem — Auth Service only (signs tokens)"
echo "   🔓 public.pem  — All services (verifies tokens)"
echo ""
echo "⚠️  NEVER commit private.pem to version control!"
