# Dependencias principales
composer require symfony/framework-bundle
composer require symfony/console
composer require symfony/dotenv
composer require symfony/maker-bundle --dev

# Base de datos y ORM
composer require doctrine/orm
composer require doctrine/doctrine-bundle
composer require doctrine/doctrine-migrations-bundle
composer require doctrine/doctrine-fixtures-bundle --dev

# API y Serialización
composer require symfony/serializer
composer require symfony/validator
composer require nelmio/api-doc-bundle
composer require nelmio/cors-bundle

# HTTP Client para API externa
composer require symfony/http-client

# Cache
composer require symfony/cache

# JWT Authentication
composer require lexik/jwt-authentication-bundle

# Testing
composer require --dev phpunit/phpunit
composer require --dev symfony/test-pack
composer require --dev symfony/browser-kit
composer require --dev symfony/css-selector

# Utilidades adicionales
composer require symfony/uid
composer require symfony/messenger