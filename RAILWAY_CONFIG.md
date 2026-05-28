# Railway Build and Deploy Commands

# Pre-deployment: Run migrations and caching
# Set these in Railway dashboard under Variables

# Build command (runs during deployment)
# Executed by Railway: npm run build + php caching commands
# (handled in nixpacks.toml)

# Start command (runs after build)
# php -S 0.0.0.0:${PORT:-8000} -t public
