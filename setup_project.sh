#!/bin/bash

# Define base project directory
PROJECT_DIR="./src/"

# Define folders to create
DIRECTORIES=(
  "$PROJECT_DIR/Application/Services"
  "$PROJECT_DIR/Application/Commands"
  "$PROJECT_DIR/Application/DTOs"
  "$PROJECT_DIR/Domain/Entities"
  "$PROJECT_DIR/Domain/Interfaces"
  "$PROJECT_DIR/Infrastructure/Repositories"
  "$PROJECT_DIR/Infrastructure/Database"
  "$PROJECT_DIR/Support/Helpers"
  "tests/Unit"
  "tests/Integration"
  "public"
)

# Create directories and add .gitkeep
for DIR in "${DIRECTORIES[@]}"; do
  mkdir -p "$DIR"
  touch "$DIR/.gitkeep"
done

# Define PHP files with namespaces
declare -A PHP_FILES
PHP_FILES=(
  ["$PROJECT_DIR/Application/Services/ProductImportService.php"]="namespace Application\\Services;"
  ["$PROJECT_DIR/Application/Services/ProductExportService.php"]="namespace Application\\Services;"
  ["$PROJECT_DIR/Application/Services/XmlFeedGenerator.php"]="namespace Application\\Services;"
  ["$PROJECT_DIR/Application/Services/DatabaseSeeder.php"]="namespace Application\\Services;"

  ["$PROJECT_DIR/Application/Commands/ImportProductsCommand.php"]="namespace Application\\Commands;"
  ["$PROJECT_DIR/Application/Commands/GenerateXmlCommand.php"]="namespace Application\\Commands;"

  ["$PROJECT_DIR/Application/DTOs/ProductDTO.php"]="namespace Application\\DTOs;"
  ["$PROJECT_DIR/Application/DTOs/CategoryDTO.php"]="namespace Application\\DTOs;"

  ["$PROJECT_DIR/Domain/Entities/Product.php"]="namespace Domain\\Entities;"
  ["$PROJECT_DIR/Domain/Entities/Category.php"]="namespace Domain\\Entities;"
  ["$PROJECT_DIR/Domain/Entities/PriceHistory.php"]="namespace Domain\\Entities;"

  ["$PROJECT_DIR/Domain/Interfaces/ProductRepositoryInterface.php"]="namespace Domain\\Interfaces;"
  ["$PROJECT_DIR/Domain/Interfaces/CategoryRepositoryInterface.php"]="namespace Domain\\Interfaces;"
  ["$PROJECT_DIR/Domain/Interfaces/XmlFeedGeneratorInterface.php"]="namespace Domain\\Interfaces;"

  ["$PROJECT_DIR/Infrastructure/Repositories/ProductRepository.php"]="namespace Infrastructure\\Repositories;"
  ["$PROJECT_DIR/Infrastructure/Repositories/CategoryRepository.php"]="namespace Infrastructure\\Repositories;"

  ["$PROJECT_DIR/Infrastructure/Database/DatabaseConnection.php"]="namespace Infrastructure\\Database;"

  ["$PROJECT_DIR/Support/Helpers/CsvParser.php"]="namespace Support\\Helpers;"
  ["$PROJECT_DIR/Support/Helpers/XmlFormatter.php"]="namespace Support\\Helpers;"

  ["tests/Unit/ProductTest.php"]="namespace Tests\\Unit;"
  ["tests/Unit/CategoryTest.php"]="namespace Tests\\Unit;"
  ["tests/Unit/XmlFeedTest.php"]="namespace Tests\\Unit;"

  ["tests/Integration/ProductImportTest.php"]="namespace Tests\\Integration;"
  ["tests/Integration/XmlExportTest.php"]="namespace Tests\\Integration;"
)

# Create PHP files with namespaces
for FILE in "${!PHP_FILES[@]}"; do
  if [[ "$FILE" == tests/* ]]; then
    printf "<?php\n\n%s\n\nuse PHPUnit\Framework\TestCase;\n\nclass %s extends TestCase \n{\n    public function testBasicFunction()\n    {\n        \$this->assertTrue(true);\n    }\n}\n" \
      "${PHP_FILES[$FILE]}" "$(basename "$FILE" .php)" > "$FILE"
  else
    printf "<?php\n\n%s\n\nclass %s \n{\n    // TODO: Implement\n}\n" \
      "${PHP_FILES[$FILE]}" "$(basename "$FILE" .php)" > "$FILE"
  fi
done

# Create root-level files
touch "bootstrap.php"
touch "docker-compose.yml"
touch ".env"
touch ".gitignore"
touch "public/index.php"

# Generate composer.json with correct content
cat <<EOL > composer.json
{
  "require": {
    "php": "^8.3",
    "symfony/dotenv": "^6.0",
    "vlucas/phpdotenv": "^5.4",
    "doctrine/dbal": "^3.0"
  },
  "require-dev": {
    "phpunit/phpunit": "^10.0"
  },
  "autoload": {
    "psr-4": {
      "Application\\\\": "src/Application/",
      "Domain\\\\": "src/Domain/",
      "Infrastructure\\\\": "src/Infrastructure/",
      "Support\\\\": "src/Support/"
    }
  },
  "autoload-dev": {
    "psr-4": {
      "Tests\\\\": "tests/"
    }
  }
}
EOL

#!/bin/bash

# Ensure tests directory exists
mkdir -p tests/Integration

# Create `DatabaseTest.php` and write the test class into it
cat <<EOL > tests/Integration/DatabaseTest.php
<?php

namespace Tests\Integration;

use PHPUnit\Framework\TestCase;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;

class DatabaseTest extends TestCase
{
    private \$connection;

    protected function setUp(): void
    {
        \$connectionParams = [
            'dbname'   => \$_ENV['DB_DATABASE'] ?? getenv('DB_DATABASE'),
            'user'     => \$_ENV['DB_USERNAME'] ?? getenv('DB_USERNAME'),
            'password' => \$_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD'),
            'host'     => \$_ENV['DB_HOST'] ?? getenv('DB_HOST'),
            'driver'   => 'pdo_mysql',
        ];

        try {
            \$this->connection = DriverManager::getConnection(\$connectionParams);
        } catch (Exception \$e) {
            \$this->fail("Database connection failed: " . \$e->getMessage());
        }
    }

    public function testDatabaseExists()
    {
        \$this->assertNotNull(\$this->connection, "Database connection should not be null.");
    }

    public function testCanRetrieveTables()
    {
        \$stmt = \$this->connection->query("SHOW TABLES;");
        \$tables = \$stmt->fetchAllAssociative();

        \$this->assertNotEmpty(\$tables, "Database has no tables.");
    }

    public function testCanInsertAndRetrieveData()
    {
        \$this->connection->executeStatement("INSERT INTO products (title) VALUES ('Test Product')");

        \$stmt = \$this->connection->executeQuery("SELECT * FROM products WHERE title = 'Test Product'");
        \$result = \$stmt->fetchAssociative();

        \$this->assertNotEmpty(\$result, "Inserted data could not be retrieved.");
        \$this->assertEquals('Test Product', \$result['title'], "Inserted title does not match expected value.");
    }
}
EOL

# Initialize git in project root (Corrected)
git init
echo "Setup complete. fws-probafeladat structure is ready."