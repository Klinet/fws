<?php

namespace Application\Services;

use Doctrine\DBAL\Connection;
use League\Csv\Reader;
use Doctrine\DBAL\Exception;

class ProductImportService
{
    private Connection $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function importCsv(string $filePath): void
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File not found: $filePath");
        }

        $csv = Reader::createFromPath($filePath, 'r');
        $csv->setHeaderOffset(0);  // Set the header to be the first row

        foreach ($csv as $record) {
            $this->syncProduct($record);
        }
    }

    private function syncProduct(array $record): void
    {
        // Ensure we handle null values safely and only trim non-null values
        $productName = trim($record['Termék megnevezése'] ?? ''); // Default to empty string if null
        $price = (int)($record['Bruttó ár'] ?? 0); // Default to 0 if null
        $categories = array_filter([
            trim($record['Kategória 1'] ?? ''),
            trim($record['Kategória 2'] ?? ''),
            trim($record['Kategória 3'] ?? '')
        ]);

        // Ensure productName and price are valid
        if (empty($productName) || $price <= 0) {
            return;
        }

        try {
            $this->db->beginTransaction();

            $productId = $this->getOrCreateProduct($productName, $price);
            $this->assignCategoriesToProduct($productId, $categories);

            // Update price_history with the new price and timestamp
            $this->updatePriceHistory($productId, $price);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            throw new \RuntimeException("Database error: " . $e->getMessage());
        }
    }

    private function getOrCreateProduct(string $name, int $price): int
    {
        $product = $this->db->fetchAssociative("SELECT id, price FROM products WHERE title = ?", [$name]);

        if ($product) {
            if ($product['price'] !== $price) {
                $this->db->executeStatement("UPDATE products SET price = ? WHERE id = ?", [$price, $product['id']]);
            }
            return $product['id'];
        }

        $this->db->executeStatement("INSERT INTO products (title, price) VALUES (?, ?)", [$name, $price]);
        return (int)$this->db->lastInsertId();
    }

    private function assignCategoriesToProduct(int $productId, array $categories): void
    {
        foreach ($categories as $categoryName) {
            if (empty($categoryName)) {
                continue;
            }

            $categoryId = $this->getOrCreateCategory($categoryName);

            $exists = $this->db->fetchOne(
                "SELECT COUNT(*) FROM product_categories WHERE product_id = ? AND category_id = ?",
                [$productId, $categoryId]
            );

            if (!$exists) {
                $this->db->executeStatement(
                    "INSERT INTO product_categories (product_id, category_id) VALUES (?, ?)",
                    [$productId, $categoryId]
                );
            }
        }
    }

    private function getOrCreateCategory(string $name): int
    {
        $category = $this->db->fetchAssociative("SELECT id FROM categories WHERE title = ?", [$name]);

        if ($category) {
            return $category['id'];
        }

        $this->db->executeStatement("INSERT INTO categories (title) VALUES (?)", [$name]);
        return (int)$this->db->lastInsertId();
    }

    private function updatePriceHistory(int $productId, int $price): void
    {
        // Get the current timestamp
        $timestamp = (new \DateTime())->format('Y-m-d H:i:s');

        // Insert a new record into price_history with the current price and timestamp
        $this->db->executeStatement(
            "INSERT INTO price_history (product_id, price, updated_at) VALUES (?, ?, ?)",
            [$productId, $price, $timestamp]
        );
    }
}